<?php

require_once __DIR__ . '/../admin/inc/config.php';
require_once __DIR__ . '/../admin/inc/functions.php';

if (!isset($pdo)) {
    die('Database connection failed.');
}

function web_h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function web_upload(string $file): string
{
    if ($file === '') {
        return '';
    }

    return 'admin/assets/uploads/' . ltrim($file, '/');
}

/** Public URL for a file stored under admin/assets/uploads/, with fallback if missing on disk. */
function web_media_url(string $filename, string $fallback = 'img/about.jpg'): string
{
    $filename = trim($filename);
    if ($filename !== '') {
        $relative = 'admin/assets/uploads/' . ltrim(str_replace('\\', '/', $filename), '/');
        $absolute = dirname(__DIR__) . '/' . $relative;
        if (is_file($absolute)) {
            return $relative;
        }
    }

    return $fallback;
}

function web_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
    $stmt->execute([$table]);

    return (bool)$stmt->fetchColumn();
}

function web_excerpt(?string $html, int $length = 120): string
{
    $text = trim(strip_tags((string)$html));

    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length - 3) . '...';
}

function web_format_date(?string $date): string
{
    if (empty($date)) {
        return '';
    }

    $ts = strtotime($date);

    return $ts ? date('d M, Y', $ts) : web_h($date);
}

function web_fetch_products(PDO $pdo, int $limit = 8, bool $featuredOnly = false): array
{
    if (!web_table_exists($pdo, 'tbl_product')) {
        return [];
    }

    $sql = "SELECT p_id, p_name, p_current_price, p_old_price, p_featured_photo, p_short_description
            FROM tbl_product
            WHERE p_is_active = 1";

    if ($featuredOnly) {
        $sql .= ' AND p_is_featured = 1';
    }

    $sql .= ' ORDER BY p_id DESC LIMIT ' . (int)$limit;

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function web_fetch_posts(PDO $pdo, int $limit = 6): array
{
    if (!web_table_exists($pdo, 'tbl_post')) {
        return [];
    }

    $sql = 'SELECT post_id, post_title, post_slug, post_content, post_date, photo, total_view
            FROM tbl_post
            ORDER BY post_id DESC
            LIMIT ' . (int)$limit;

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function web_fetch_post_by_slug(PDO $pdo, string $slug): ?array
{
    if ($slug === '' || !web_table_exists($pdo, 'tbl_post')) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT * FROM tbl_post WHERE post_slug = ? LIMIT 1');
    $stmt->execute([$slug]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function web_fetch_faqs(PDO $pdo): array
{
    if (!web_table_exists($pdo, 'tbl_faq')) {
        return [];
    }

    return $pdo->query('SELECT faq_id, faq_title, faq_content FROM tbl_faq ORDER BY faq_id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function web_setting_on(array $settings, string $key): bool
{
    return isset($settings[$key]) && (int)$settings[$key] === 1;
}

function web_flash_get(string $key): string
{
    if (empty($_SESSION[$key])) {
        return '';
    }

    $msg = (string)$_SESSION[$key];
    unset($_SESSION[$key]);

    return $msg;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$settings = $pdo->query('SELECT * FROM tbl_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC) ?: [];
$page_row = $pdo->query('SELECT * FROM tbl_page WHERE id = 1')->fetch(PDO::FETCH_ASSOC) ?: [];
$socials = $pdo->query('SELECT * FROM tbl_social WHERE social_url != "" ORDER BY social_id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
$sliders = $pdo->query('SELECT * FROM tbl_slider ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];

$site_name = 'PetPulse';
$logo_file = $settings['logo'] ?? '';
$favicon_file = $settings['favicon'] ?? 'favicon.png';
$has_products = web_table_exists($pdo, 'tbl_product') && count(web_fetch_products($pdo, 1)) > 0;
