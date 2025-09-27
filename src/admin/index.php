<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();
require_once __DIR__ . '/../lib/db.php';

$title = 'Админка';
$pdo = db();
$stats = [
  'books' => (int)$pdo->query("SELECT count(*) FROM books")->fetchColumn(),
  'users' => (int)$pdo->query("SELECT count(*) FROM users")->fetchColumn(),
  'active_rents' => (int)$pdo->query("SELECT count(*) FROM transactions WHERE type='rent' AND status='active'")->fetchColumn(),
];

require __DIR__ . '/../partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Админ-панель</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-primary btn-sm" href="/admin/books.php"><i class="bi bi-book me-1"></i>Книги</a>
    <a class="btn btn-outline-primary btn-sm" href="/admin/rentals.php"><i class="bi bi-clock-history me-1"></i>Аренды</a>
    <a class="btn btn-outline-secondary btn-sm" href="/"><i class="bi bi-house me-1"></i>На сайт</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-sm-4">
    <div class="card shadow-sm h-100 position-relative">
      <div class="card-body d-flex align-items-center">
        <i class="bi bi-book fs-3 me-3 text-primary"></i>
        <div>
          <div class="text-secondary small">Книги</div>
          <div class="fs-4 fw-bold"><?=$stats['books']?></div>
        </div>
      </div>
      <a href="/admin/books.php" class="stretched-link" aria-label="Перейти в книги"></a>
    </div>
  </div>

  <div class="col-sm-4">
    <div class="card shadow-sm h-100 position-relative">
      <div class="card-body d-flex align-items-center">
        <i class="bi bi-people fs-3 me-3 text-primary"></i>
        <div>
          <div class="text-secondary small">Пользователи</div>
          <div class="fs-4 fw-bold"><?=$stats['users']?></div>
        </div>
      </div>
      <!-- сюда можно добавить ссылку на /admin/users.php, когда появится -->
    </div>
  </div>

  <div class="col-sm-4">
    <div class="card shadow-sm h-100 position-relative">
      <div class="card-body d-flex align-items-center">
        <i class="bi bi-clock-history fs-3 me-3 text-primary"></i>
        <div>
          <div class="text-secondary small">Активные аренды</div>
          <div class="fs-4 fw-bold"><?=$stats['active_rents']?></div>
        </div>
      </div>
      <a href="/admin/rentals.php" class="stretched-link" aria-label="Перейти в аренды"></a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
