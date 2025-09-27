<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/utils.php';

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
  http_response_code(404);
  $title = 'Книга не найдена';
  require __DIR__ . '/partials/header.php';
  echo '<div class="alert alert-danger">Книга не найдена</div>';
  require __DIR__ . '/partials/footer.php';
  exit;
}

$title = $book['title'];
$u = current_user();
$msg = $_GET['msg'] ?? '';

require __DIR__ . '/partials/header.php';

// Подготовим картинку с плейсхолдером
$src  = book_cover_url($book['cover_url'] ?? null);
$isPh = function_exists('is_placeholder_url') ? is_placeholder_url($src) : false;
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="/">Каталог</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= h($book['title']) ?></li>
  </ol>
</nav>

<div class="row g-4">
  <div class="col-md-5">
    <img
      src="<?= h($src) ?>"
      class="img-fluid rounded shadow-sm <?= $isPh ? 'img-ph' : '' ?>"
      alt="<?= h($book['title']) ?>"
      onerror="this.onerror=null;this.src='<?= h(placeholder_image_url()) ?>'; this.classList.add('img-ph');"
    />
  </div>
  <div class="col-md-7">
    <h1 class="h3 mb-1"><?= h($book['title']) ?></h1>
    <div class="mb-2 text-secondary">
      Автор: <strong><?= h($book['author']) ?></strong>
      · Год: <strong><?= h((string)$book['year']) ?></strong>
    </div>
    <span class="badge rounded-pill badge-soft mb-3"><?= h($book['category']) ?></span>

    <?php if (!empty($book['description'])) { ?>
      <p class="lead"><?= nl2br(h($book['description'])) ?></p>
    <?php } ?>

    <div class="card mb-3">
      <div class="card-header fw-semibold">Цены</div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li>Покупка: <strong><?= h($book['price_buy']) ?> ₽</strong></li>
          <li>Аренда 2 недели: <strong><?= h($book['price_rent_2w']) ?> ₽</strong></li>
          <li>Аренда 1 месяц: <strong><?= h($book['price_rent_1m']) ?> ₽</strong></li>
          <li>Аренда 3 месяца: <strong><?= h($book['price_rent_3m']) ?> ₽</strong></li>
        </ul>
      </div>
    </div>

    <?php if ($msg) { ?>
      <div class="alert alert-success"><?= h($msg) ?></div>
    <?php } ?>

    <?php if ($u) { ?>
      <form action="/purchase.php" method="post" class="row gy-2 align-items-center">
        <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
        <div class="col-12 col-md-auto">
          <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="type" id="buy" value="buy" checked>
            <label class="btn btn-outline-primary" for="buy"><i class="bi bi-bag-check me-1"></i>Купить</label>
            <input type="radio" class="btn-check" name="type" id="rent" value="rent">
            <label class="btn btn-outline-primary" for="rent"><i class="bi bi-clock-history me-1"></i>Аренда</label>
          </div>
        </div>
        <div class="col-12 col-md-auto">
          <select name="duration" class="form-select">
            <option value="2w">2 недели</option>
            <option value="1m">1 месяц</option>
            <option value="3m">3 месяца</option>
          </select>
        </div>
        <div class="col-12 col-md-auto">
          <button class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i>Оформить</button>
        </div>
      </form>
    <?php } else { ?>
      <a class="btn btn-primary" href="/login.php">
        <i class="bi bi-box-arrow-in-right me-1"></i>Войдите, чтобы купить/арендовать
      </a>
    <?php } ?>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
