<?php
require_once __DIR__ . '/../lib/auth.php';
$u = current_user();
$title = $title ?? 'Книжный магазин';
?>
<!doctype html>
<html lang="ru" data-bs-theme="auto">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Custom -->
  <link rel="stylesheet" href="/public/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
      <i class="bi bi-journal-richtext"></i> Bookstore
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="topnav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <?php if ($u && $u['role']==='admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/admin/"><i class="bi bi-speedometer2 me-1"></i>Админ</a></li>
        <?php endif; ?>
        <?php if ($u): ?>
          <li class="nav-item"><a class="nav-link" href="/rentals.php"><i class="bi bi-bookmarks me-1"></i>Мои аренды</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary btn-sm" id="themeToggle" title="Тема">
          <i class="bi bi-sun-fill" id="iconSun"></i>
          <i class="bi bi-moon-fill d-none" id="iconMoon"></i>
        </button>
        <?php if ($u): ?>
          <span class="text-secondary small d-none d-md-inline">Привет, <?=htmlspecialchars($u['name'])?></span>
          <a class="btn btn-outline-primary btn-sm" href="/logout.php">Выйти</a>
        <?php else: ?>
          <a class="btn btn-outline-primary btn-sm" href="/login.php">Войти</a>
          <a class="btn btn-primary btn-sm" href="/register.php">Регистрация</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="py-4">
<div class="container">
