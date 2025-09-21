<?php
// Обслуживание статических файлов
$request = $_SERVER['REQUEST_URI'];

// Если запрос к статическому файлу
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg)$/i', $request)) {
    $file_path = __DIR__ . $request;

    if (file_exists($file_path) && is_file($file_path)) {
        $mime_types = [
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'ico'  => 'image/x-icon',
            'svg'  => 'image/svg+xml'
        ];

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
            header('Cache-Control: public, max-age=86400');
        } else {
            header('Content-Type: application/octet-stream');
        }

        readfile($file_path);
        exit;
    } else {
        http_response_code(404);
        exit;
    }
}
// Установка базового пути
define('BASE_PATH', dirname(__DIR__));

// Загрузка конфигурации
require_once BASE_PATH . '/config/database.php';

// Загрузка контроллеров
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/controllers/BookController.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/AdminController.php';

session_start();

// Обработка маршрутов
$path = parse_url($request, PHP_URL_PATH);

// Убираем начальный слэш
$path = ltrim($path, '/');

// Простые маршруты
if ($path === '' || $path === 'books') {
    $controller = new BookController();
    $controller->index();
} elseif (preg_match('/^books\/(\d+)$/', $path, $matches)) {
    $controller = new BookController();
    $controller->show($matches[1]);
} elseif ($path === 'login') {
    $controller = new AuthController();
    $controller->login();
} elseif ($path === 'register') {
    $controller = new AuthController();
    $controller->register();
} elseif ($path === 'logout') {
    $controller = new AuthController();
    $controller->logout();
} elseif ($path === 'admin') {
    $controller = new AdminController();
    $controller->index();
} elseif (preg_match('/^admin\/edit\/(\d+)$/', $path, $matches)) {
    $controller = new AdminController();
    $controller->editBook($matches[1]);
} else {
    http_response_code(404);
    echo "Страница не найдена: " . htmlspecialchars($path);
}
?>
