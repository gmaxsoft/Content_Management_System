<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables for tests
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up test environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['DEBUG'] = 'false';

// Mock database connection for tests (optional)
if (!defined('DB_CONNECTION_MOCK')) {
    define('DB_CONNECTION_MOCK', true);
}