<?php

require_once 'inc/config.php';
require_once 'notificationshelper.php';

$notification = new NotificationHelper($pdo);

$notification->checkAllPets();

echo "Done";