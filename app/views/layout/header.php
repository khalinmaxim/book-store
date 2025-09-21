<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книжный магазин</title>
    <link rel="stylesheet" href="/public/css/style.css?v=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline';">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/books" class="nav-logo">Книжный магазин</a>
            <div class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="/logout">Выйти</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="/admin">Админ панель</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/login">Войти</a>
                    <a href="/register">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">
