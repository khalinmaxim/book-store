<?php
require_once __DIR__ . '/../../config/database.php';

class Controller {
    protected $db;

    public function __construct() {
        $this->connectDB();
    }

    protected function connectDB() {
        try {
            $dsn = "pgsql:host=" . DatabaseConfig::$host .
                   ";port=" . DatabaseConfig::$port .
                   ";dbname=" . DatabaseConfig::$dbname;
            $this->db = new PDO($dsn, DatabaseConfig::$username, DatabaseConfig::$password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    protected function render($view, $data = []) {
        // Устанавливаем путь к views
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: " . $viewPath);
        }

        extract($data);

        // Начинаем буферизацию
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        echo $content;
    }

    protected function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $this->redirect('/login');
        }
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
}
?>
