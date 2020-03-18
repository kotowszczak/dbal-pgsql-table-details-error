<?php

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . './db.config.php';
require_once __DIR__ . './UsersTable.php';


$conn = \Doctrine\DBAL\DriverManager::getConnection([
    'host' => DB_HOST,
    'dbname' => DB_NAME ,
    'user' => DB_USER,
    'password' => DB_PASS,
    'port' => DB_PORT,
    'driver' => DB_DRIVER,
]);

$usersTable = new UsersTable($conn);

if (! $usersTable->exists()) {
    $usersTable->create();
}

$usersTableDetails = $usersTable->details();

echo '<pre>';
print_r($usersTableDetails);
echo '</pre>';






