<?php

if (!defined('ADMIN_URL')) {
    require_once __DIR__ . '/inc/config.php';
}

class NotificationHelper
{
    private \PDO $pdo;
    private int $cooldownHours = 24;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Log an admin panel create/update/delete to the notifications table for the affected owner(s).
     *
     * Options:
     *   owner_id (int), pet_id (int), details (string), message (string), title (string),
     *   link (string), type (string), broadcast (bool), trigger_alerts (bool)
     */
    public function logAdminDbChange(string $action, string $entity, array $options = []): void
    {
        $action = strtolower($action);
        if (!in_array($action, ['created', 'updated', 'deleted'], true)) {
            return;
        }

        $pet_id = isset($options['pet_id']) ? (int)$options['pet_id'] : null;
        $owner_id = isset($options['owner_id']) ? (int)$options['owner_id'] : null;

        if (!$owner_id && $pet_id) {
            $owner_id = $this->resolveOwnerIdFromPet($pet_id);
        }

        $owner_ids = [];
        if ($owner_id) {
            $owner_ids = [$owner_id];
        } elseif (!empty($options['broadcast'])) {
            $owner_ids = $this->getActiveOwnerIds();
        }

        if (empty($owner_ids)) {
            return;
        }

        $pet_name = $pet_id ? $this->getPetName($pet_id) : '';
        $details = (string)($options['details'] ?? '');

        $action_label = ucfirst($action);
        $title = (string)($options['title'] ?? "{$entity} {$action_label}");
        $message = (string)($options['message'] ?? $this->buildAdminMessage($action, $entity, $pet_name, $details));
        $link = (string)($options['link'] ?? $this->defaultAdminLink($entity, $pet_id));
        $type = (string)($options['type'] ?? 'admin_' . $action);

        foreach ($owner_ids as $uid) {
            $this->insertNotification((int)$uid, $pet_id ?: null, $type, $title, $message, $link);
        }

        if (!empty($options['trigger_alerts']) && $pet_id) {
            $this->checkAndNotifyPet($pet_id);
        }
    }

    /**
     * Log owner-panel create/update/delete (user_id = logged-in owner_id).
     */
    public function logOwnerDbChange(string $action, int $owner_id, string $entity, array $options = []): void
    {
        $action = strtolower($action);
        if (!in_array($action, ['created', 'updated', 'deleted'], true) || $owner_id <= 0) {
            return;
        }

        $pet_id = isset($options['pet_id']) ? (int)$options['pet_id'] : null;

        if ($pet_id && !$this->ownerOwnsPet($owner_id, $pet_id)) {
            return;
        }

        $pet_name = $pet_id ? $this->getPetName($pet_id) : '';
        $details = (string)($options['details'] ?? '');

        $action_label = ucfirst($action);
        $title = (string)($options['title'] ?? "Your {$entity} {$action_label}");
        $message = (string)($options['message'] ?? $this->buildOwnerMessage($action, $entity, $pet_name, $details));
        $link = (string)($options['link'] ?? $this->defaultAdminLink($entity, $pet_id));
        $type = (string)($options['type'] ?? 'owner_' . $action);

        $this->insertNotification($owner_id, $pet_id ?: null, $type, $title, $message, $link);

        if (!empty($options['trigger_alerts']) && $pet_id) {
            $this->checkAndNotifyPet($pet_id);
        }
    }

    public function ownerOwnsPet(int $owner_id, int $pet_id): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tbl_pet WHERE pet_id = ? AND owner_id = ? LIMIT 1');
        $stmt->execute([$pet_id, $owner_id]);

        return (bool)$stmt->fetchColumn();
    }

    private function buildOwnerMessage(
        string $action,
        string $entity,
        string $pet_name,
        string $details
    ): string {
        if ($action === 'created') {
            $verb = 'added';
        } elseif ($action === 'updated') {
            $verb = 'updated';
        } elseif ($action === 'deleted') {
            $verb = 'removed';
        } else {
            $verb = 'changed';
        }

        $message = "Your {$entity} was {$verb}";

        if ($pet_name !== '') {
            $message .= " for {$pet_name}";
        }

        if ($details !== '') {
            $message .= $details;
        }

        return $message . '.';
    }

    private function resolveOwnerIdFromPet(int $pet_id): ?int
    {
        $stmt = $this->pdo->prepare('SELECT owner_id FROM tbl_pet WHERE pet_id = ? LIMIT 1');
        $stmt->execute([$pet_id]);
        $owner_id = $stmt->fetchColumn();

        return $owner_id !== false ? (int)$owner_id : null;
    }

    private function getPetName(int $pet_id): string
    {
        $stmt = $this->pdo->prepare('SELECT pet_name FROM tbl_pet WHERE pet_id = ? LIMIT 1');
        $stmt->execute([$pet_id]);
        $name = $stmt->fetchColumn();

        return $name !== false ? (string)$name : '';
    }

    /** @return int[] */
    private function getActiveOwnerIds(): array
    {
        $stmt = $this->pdo->query('SELECT owner_id FROM tbl_owner WHERE is_active = 1');
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return array_map('intval', $ids ?: []);
    }

    private function buildAdminMessage(
        string $action,
        string $entity,
        string $pet_name,
        string $details
    ): string {
        if ($action === 'created') {
            $verb = 'added';
        } elseif ($action === 'updated') {
            $verb = 'updated';
        } elseif ($action === 'deleted') {
            $verb = 'removed';
        } else {
            $verb = 'changed';
        }

        $message = "An administrator {$verb} {$entity}";

        if ($pet_name !== '') {
            $message .= " for {$pet_name}";
        }

        if ($details !== '') {
            $message .= $details;
        }

        return $message . '.';
    }

    private function defaultAdminLink(string $entity, ?int $pet_id): string
    {
        $map = [
            'Pet' => 'pet.php',
            'Health record' => 'health.php',
            'Vaccination' => 'vaccination.php',
            'Device' => 'device.php',
            'Medical note' => 'medical.php',
            'Owner account' => 'pet.php',
            'Profile' => 'profile-edit.php',
            'Password' => 'profile-edit.php',
            'Payment' => 'payment.php',
            'Blog post' => 'blog.php',
            'Slider' => 'index.php',
            'FAQ' => 'faq.php',
        ];

        foreach ($map as $key => $link) {
            if (stripos($entity, $key) !== false) {
                return $link;
            }
        }

        return 'notification.php';
    }

    /**
     * Main notification checker
     */
    public function checkAndNotifyPet(int $pet_id): void
    {
        // Get pet + owner info
        $stmt = $this->pdo->prepare("
            SELECT 
                p.pet_id,
                p.pet_name,
                p.daily_goal_minutes,
                o.owner_id,
                o.owner_name
            FROM tbl_pet p
            JOIN tbl_owner o ON p.owner_id = o.owner_id
            WHERE p.pet_id = ?
        ");

        $stmt->execute([$pet_id]);

        $pet = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$pet) {
            return;
        }

        $owner_id = (int)$pet['owner_id'];
        $pet_name = (string)$pet['pet_name'];

        /**
         * =====================================================
         * DEVICE NOTIFICATIONS
         * =====================================================
         */

        if ($this->wasRecentlyUpdated('devices', 'last_synced', $pet_id)) {

            $device = $this->getDeviceData($pet_id);

            if ($device) {

                // Low battery
                if (
                    $device['battery_percent'] !== null &&
                    (int)$device['battery_percent'] <= 15
                ) {

                    $this->addNotification(
                        $owner_id,
                        $pet_id,
                        'battery',
                        'Low Battery Warning',
                        "{$pet_name}'s collar battery is at {$device['battery_percent']}%. Please charge soon.",
                        'device_status.php'
                    );
                }

                // Device disconnected
                if (
                    !empty($device['bluetooth_status']) &&
                    strtolower($device['bluetooth_status']) === 'disconnected'
                ) {

                    $this->addNotification(
                        $owner_id,
                        $pet_id,
                        'device',
                        'Device Offline',
                        "{$pet_name}'s smart collar is offline. Check connection.",
                        'devices.php'
                    );
                }
            }
        }

        /**
         * =====================================================
         * HEALTH NOTIFICATIONS
         * =====================================================
         */

        if ($this->wasRecentlyUpdated('pet_health_records', 'recorded_at', $pet_id)) {

            $health = $this->getLatestHealthRecord($pet_id);

            if ($health) {

                /**
                 * Heart Rate
                 */
                if (
                    $health['heart_rate_bpm'] !== null &&
                    (
                        (int)$health['heart_rate_bpm'] < 60 ||
                        (int)$health['heart_rate_bpm'] > 140
                    )
                ) {

                    $this->addNotification(
                        $owner_id,
                        $pet_id,
                        'health_alert',
                        'Abnormal Heart Rate',
                        "Heart rate: {$health['heart_rate_bpm']} BPM. Normal range is 60–140 BPM.",
                        "pet_health.php?id={$pet_id}"
                    );
                }

                /**
                 * Temperature
                 */
                if ($health['body_temp_f'] !== null) {

                    $tempC = ((float)$health['body_temp_f'] - 32) * 5 / 9;

                    if ($tempC < 37.5 || $tempC > 39.2) {

                        $status = $tempC > 39.2
                            ? 'possible fever'
                            : 'low temperature';

                        $this->addNotification(
                            $owner_id,
                            $pet_id,
                            'health_alert',
                            'Abnormal Body Temperature',
                            sprintf(
                                "Temperature: %.1f°C detected (%s).",
                                $tempC,
                                $status
                            ),
                            "pet_health.php?id={$pet_id}"
                        );
                    }
                }

                /**
                 * Emotion / Behavior
                 */
                $emotion = strtolower($health['emotion_state'] ?? '');

                if (in_array($emotion, ['anxious', 'agitated'], true)) {

                    $this->addNotification(
                        $owner_id,
                        $pet_id,
                        'behavior',
                        'Behavioral Alert',
                        "{$pet_name} appears {$health['emotion_state']}. Extra attention may help.",
                        "pet_behavior.php?id={$pet_id}"
                    );
                }

                /**
                 * Daily Activity Goal
                 */
                if (
                    !empty($pet['daily_goal_minutes']) &&
                    $health['active_minutes'] !== null &&
                    (int)$health['active_minutes'] < (int)$pet['daily_goal_minutes']
                ) {

                    $needed =
                        (int)$pet['daily_goal_minutes'] -
                        (int)$health['active_minutes'];

                    $this->addNotification(
                        $owner_id,
                        $pet_id,
                        'activity',
                        'Daily Activity Goal Not Met',
                        "Only {$health['active_minutes']} active minutes today. {$needed} more needed to reach goal.",
                        "pet_activity.php?id={$pet_id}"
                    );
                }
            }
        }

        /**
         * =====================================================
         * VACCINATION NOTIFICATIONS
         * =====================================================
         */

        $this->checkVaccinations(
            $pet_id,
            $owner_id,
            $pet_name
        );
    }

    /**
     * =====================================================
     * DEVICE DATA
     * =====================================================
     */

    private function getDeviceData(int $pet_id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                battery_percent,
                bluetooth_status,
                last_synced
            FROM devices
            WHERE pet_id = ?
            ORDER BY last_synced DESC
            LIMIT 1
        ");

        $stmt->execute([$pet_id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * =====================================================
     * HEALTH DATA
     * =====================================================
     */

    private function getLatestHealthRecord(int $pet_id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                heart_rate_bpm,
                body_temp_f,
                active_minutes,
                emotion_state,
                recorded_at
            FROM pet_health_records
            WHERE pet_id = ?
            ORDER BY recorded_at DESC
            LIMIT 1
        ");

        $stmt->execute([$pet_id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * =====================================================
     * CHECK RECENT DB CHANGES
     * =====================================================
     */

    private function wasRecentlyUpdated(
        string $table,
        string $column,
        int $pet_id,
        int $minutes = 5
    ): bool {

        // Security whitelist
        $allowed = [
            'devices' => 'last_synced',
            'pet_health_records' => 'recorded_at',
            'vaccinations' => 'due_date'
        ];

        if (
            !isset($allowed[$table]) ||
            $allowed[$table] !== $column
        ) {
            return false;
        }

        $sql = "
            SELECT 1
            FROM {$table}
            WHERE pet_id = ?
              AND {$column} >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $pet_id,
            $minutes
        ]);

        return (bool)$stmt->fetchColumn();
    }

    /**
     * =====================================================
     * VACCINATIONS
     * =====================================================
     */

    private function checkVaccinations(
        int $pet_id,
        int $owner_id,
        string $pet_name
    ): void {

        $stmt = $this->pdo->prepare("
            SELECT
                vaccine_name,
                due_date
            FROM vaccinations
            WHERE pet_id = ?
              AND due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");

        $stmt->execute([$pet_id]);

        $vaccinations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($vaccinations as $vac) {

            $dueDate = new \DateTime($vac['due_date']);
            $today = new \DateTime();

            $isOverdue = $dueDate < $today;

            $type = $isOverdue
                ? 'overdue_vaccination'
                : 'vaccination';

            $title = $isOverdue
                ? 'Vaccination Overdue'
                : 'Vaccination Due Soon';

            $message = $isOverdue
                ? "{$vac['vaccine_name']} for {$pet_name} was due on {$vac['due_date']}."
                : "{$vac['vaccine_name']} for {$pet_name} is due on {$vac['due_date']}. Please schedule vaccination.";

            $this->addNotification(
                $owner_id,
                $pet_id,
                $type,
                $title,
                $message,
                'vaccinations.php'
            );
        }
    }

    /**
     * =====================================================
     * INSERT NOTIFICATION
     * =====================================================
     */

    private function addNotification(
        int $user_id,
        int $pet_id,
        string $type,
        string $title,
        string $message,
        string $link
    ): void {

        // Prevent duplicate notifications
        $check = $this->pdo->prepare("
            SELECT id
            FROM notifications
            WHERE user_id = ?
              AND pet_id = ?
              AND type = ?
              AND message = ?
              AND (
                    is_read = 0
                    OR created_at > DATE_SUB(
                        NOW(),
                        INTERVAL ? HOUR
                    )
              )
            LIMIT 1
        ");

        $check->execute([
            $user_id,
            $pet_id,
            $type,
            $message,
            $this->cooldownHours
        ]);

        if ($check->fetch()) {
            return;
        }

        $this->insertNotification($user_id, $pet_id, $type, $title, $message, $link);
    }

    private function insertNotification(
        int $user_id,
        ?int $pet_id,
        string $type,
        string $title,
        string $message,
        string $link
    ): void {
        $insert = $this->pdo->prepare("
            INSERT INTO notifications (
                user_id,
                pet_id,
                type,
                title,
                message,
                link,
                created_at
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, NOW()
            )
        ");

        $insert->execute([
            $user_id,
            $pet_id,
            $type,
            $title,
            $message,
            $link
        ]);
    }

    /**
     * =====================================================
     * CHECK ALL PETS
     * =====================================================
     */

    public function checkAllPets(): void
    {
        $stmt = $this->pdo->query("
            SELECT pet_id
            FROM tbl_pet
        ");

        $pets = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($pets as $pet_id) {
            $this->checkAndNotifyPet((int)$pet_id);
        }
    }

    /**
     * Log devices table create/update/delete for the pet's owner (user_id + pet_id).
     */
    public function logDeviceDbChange(string $action, int $pet_id, array $options = []): void
    {
        if ($pet_id <= 0) {
            return;
        }

        $this->logAdminDbChange($action, 'Device', array_merge([
            'pet_id' => $pet_id,
            'link' => 'device.php',
            'trigger_alerts' => true,
        ], $options));
    }
}

/**
 * Log admin DB change from any admin script (requires $pdo in scope).
 */
function admin_notify_db_change(\PDO $pdo, string $action, string $entity, array $options = []): void
{
    static $helper = null;

    if ($helper === null) {
        $helper = new NotificationHelper($pdo);
    }

    $helper->logAdminDbChange($action, $entity, $options);
}

/**
 * Log devices table change with correct owner_id (from pet_id) and pet_id.
 */
function admin_notify_device_change(\PDO $pdo, string $action, int $pet_id, array $options = []): void
{
    static $helper = null;

    if ($helper === null) {
        $helper = new NotificationHelper($pdo);
    }

    $helper->logDeviceDbChange($action, $pet_id, $options);
}

/**
 * SQL WHERE fragment: only notifications for this owner (and their pets).
 * Bind :owner_id twice when using named params, or pass [$owner_id, $owner_id] for positional.
 */
function notification_filter_for_owner_sql(string $alias = 'n'): string
{
    $a = preg_replace('/[^a-z_]/i', '', $alias) ?: 'n';

    return "{$a}.user_id = ?
        AND (
            {$a}.pet_id IS NULL
            OR EXISTS (
                SELECT 1 FROM tbl_pet op
                WHERE op.pet_id = {$a}.pet_id AND op.owner_id = ?
            )
        )";
}

/**
 * Log owner-panel DB change (requires logged-in owner_id).
 */
function owner_notify_db_change(\PDO $pdo, string $action, int $owner_id, string $entity, array $options = []): void
{
    static $helper = null;

    if ($helper === null) {
        $helper = new NotificationHelper($pdo);
    }

    $helper->logOwnerDbChange($action, $owner_id, $entity, $options);
}