<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin(); // проверка ДО любого вывода
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/utils.php';

$pdo = db();

// --- Обработка форм ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['create'])) {
      $stmt = $pdo->prepare("
        INSERT INTO books
          (title, author, category, year, description, cover_url, status, quantity,
           price_buy, price_rent_2w, price_rent_1m, price_rent_3m)
        VALUES
          (:t, :a, :c, :y, :d, :u, :s, :q, :pb, :p2, :p1, :p3)
      ");
      $stmt->execute([
        't'  => trim($_POST['title'] ?? ''),
        'a'  => trim($_POST['author'] ?? ''),
        'c'  => trim($_POST['category'] ?? ''),
        'y'  => (int)($_POST['year'] ?? 0),
        'd'  => trim($_POST['description'] ?? ''),
        'u'  => trim($_POST['cover_url'] ?? ''),
        's'  => trim($_POST['status'] ?? 'available'),
        'q'  => (int)($_POST['quantity'] ?? 0),
        'pb' => (float)($_POST['price_buy'] ?? 0),
        'p2' => (float)($_POST['price_rent_2w'] ?? 0),
        'p1' => (float)($_POST['price_rent_1m'] ?? 0),
        'p3' => (float)($_POST['price_rent_3m'] ?? 0),
      ]);
    } elseif (isset($_POST['update'])) {
      $stmt = $pdo->prepare("
        UPDATE books SET
          title=:t, author=:a, category=:c, year=:y, description=:d, cover_url=:u,
          status=:s, quantity=:q,
          price_buy=:pb, price_rent_2w=:p2, price_rent_1m=:p1, price_rent_3m=:p3
        WHERE id=:id
      ");
      $stmt->execute([
        'id' => (int)($_POST['id'] ?? 0),
        't'  => trim($_POST['title'] ?? ''),
        'a'  => trim($_POST['author'] ?? ''),
        'c'  => trim($_POST['category'] ?? ''),
        'y'  => (int)($_POST['year'] ?? 0),
        'd'  => trim($_POST['description'] ?? ''),
        'u'  => trim($_POST['cover_url'] ?? ''),
        's'  => trim($_POST['status'] ?? 'available'),
        'q'  => (int)($_POST['quantity'] ?? 0),
        'pb' => (float)($_POST['price_buy'] ?? 0),
        'p2' => (float)($_POST['price_rent_2w'] ?? 0),
        'p1' => (float)($_POST['price_rent_1m'] ?? 0),
        'p3' => (float)($_POST['price_rent_3m'] ?? 0),
      ]);
    } elseif (isset($_POST['delete'])) {
      $stmt = $pdo->prepare("DELETE FROM books WHERE id=:id");
      $stmt->execute(['id' => (int)($_POST['id'] ?? 0)]);
    }
    header('Location: /admin/books.php'); // PRG
    exit;
  } catch (Throwable $e) {
    $error = 'Ошибка: ' . $e->getMessage();
  }
}

// --- Данные для отображения ---
$rows = $pdo->query("SELECT * FROM books ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$title = 'Админка — Книги';

require __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Книги</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="/admin/"><i class="bi bi-arrow-left me-1"></i>Назад</a>
  </div>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?=h($error)?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">Добавить книгу</div>
  <div class="card-body">
    <form method="post" class="row g-2">
      <div class="col-md-4">
        <input name="title" class="form-control" placeholder="Название" required>
      </div>
      <div class="col-md-3">
        <input name="author" class="form-control" placeholder="Автор" required>
      </div>
      <div class="col-md-2">
        <input name="category" class="form-control" placeholder="Категория" required>
      </div>
      <div class="col-md-1">
        <input name="year" type="number" class="form-control" placeholder="Год" required>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="available">available</option>
          <option value="unavailable">unavailable</option>
          <option value="archived">archived</option>
        </select>
      </div>

      <div class="col-md-6">
        <input name="cover_url" class="form-control" placeholder="Cover URL">
      </div>
      <div class="col-md-6">
        <input name="description" class="form-control" placeholder="Описание">
      </div>

      <div class="col-md-2">
        <input name="quantity" type="number" class="form-control" placeholder="Кол-во" value="1">
      </div>
      <div class="col-md-2">
        <input name="price_buy" type="number" step="0.01" class="form-control" placeholder="Цена покупки" required>
      </div>
      <div class="col-md-2">
        <input name="price_rent_2w" type="number" step="0.01" class="form-control" placeholder="Аренда 2 недели" required>
      </div>
      <div class="col-md-2">
        <input name="price_rent_1m" type="number" step="0.01" class="form-control" placeholder="Аренда 1 месяц" required>
      </div>
      <div class="col-md-2">
        <input name="price_rent_3m" type="number" step="0.01" class="form-control" placeholder="Аренда 3 месяца" required>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary" name="create"><i class="bi bi-plus-circle me-1"></i>Добавить</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">Список книг</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px;">ID</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Категория</th>
            <th style="width:90px;">Год</th>
            <th style="width:140px;">Статус</th>
            <th style="width:110px;">Кол-во</th>
            <th style="min-width:320px;">Цены</th>
            <th style="width:200px;">Действия</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <form method="post" class="row-gap-2">
              <td>
                <?=h((string)$r['id'])?>
                <input type="hidden" name="id" value="<?=h((string)$r['id'])?>">
              </td>
              <td><input name="title" class="form-control form-control-sm" value="<?=h($r['title'])?>"></td>
              <td><input name="author" class="form-control form-control-sm" value="<?=h($r['author'])?>"></td>
              <td><input name="category" class="form-control form-control-sm" value="<?=h($r['category'])?>"></td>
              <td style="max-width:100px;"><input type="number" name="year" class="form-control form-control-sm" value="<?=h((string)$r['year'])?>"></td>
              <td>
                <select name="status" class="form-select form-select-sm">
                  <?php foreach (['available','unavailable','archived'] as $s): ?>
                    <option value="<?=$s?>" <?=$r['status']===$s ? 'selected' : ''?>><?=$s?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input type="number" name="quantity" class="form-control form-control-sm" value="<?=h((string)$r['quantity'])?>"></td>
              <td>
                <div class="row g-1">
                  <div class="col-6 col-lg-3">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">П</span>
                      <input type="number" step="0.01" name="price_buy" class="form-control" value="<?=h($r['price_buy'])?>">
                    </div>
                  </div>
                  <div class="col-6 col-lg-3">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">2w</span>
                      <input type="number" step="0.01" name="price_rent_2w" class="form-control" value="<?=h($r['price_rent_2w'])?>">
                    </div>
                  </div>
                  <div class="col-6 col-lg-3">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">1m</span>
                      <input type="number" step="0.01" name="price_rent_1m" class="form-control" value="<?=h($r['price_rent_1m'])?>">
                    </div>
                  </div>
                  <div class="col-6 col-lg-3">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">3m</span>
                      <input type="number" step="0.01" name="price_rent_3m" class="form-control" value="<?=h($r['price_rent_3m'])?>">
                    </div>
                  </div>
                  <div class="col-12 mt-1">
                    <input name="cover_url" class="form-control form-control-sm" placeholder="Cover URL" value="<?=h($r['cover_url'] ?? '')?>">
                  </div>
                  <div class="col-12 mt-1">
                    <input name="description" class="form-control form-control-sm" placeholder="Описание" value="<?=h($r['description'] ?? '')?>">
                  </div>
                </div>
              </td>
              <td class="text-nowrap">
                <button class="btn btn-sm btn-primary" name="update"><i class="bi bi-save me-1"></i>Сохранить</button>
                <button class="btn btn-sm btn-outline-danger" name="delete" onclick="return confirm('Удалить книгу &laquo;<?=h($r['title'])?>&raquo;?')">
                  <i class="bi bi-trash me-1"></i>Удалить
                </button>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
