<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables for tests
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up test environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['DEBUG'] = 'false';

// Initialize database connection for tests
use Core\Database;
Database::init();

// Start session for tests
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}