<?php
class DatabaseConfig {
    public static $host;
    public static $dbname;
    public static $username;
    public static $password;
    public static $port;

    public static function init() {
        self::$host = getenv('DB_HOST') ?: 'postgres';
        self::$dbname = getenv('DB_NAME') ?: 'book_store';
        self::$username = getenv('DB_USER') ?: 'postgres';
        self::$password = getenv('DB_PASSWORD') ?: 'postgres123';
        self::$port = getenv('DB_PORT') ?: '5432';
    }
}

// Инициализируем конфигурацию
DatabaseConfig::init();
?>
