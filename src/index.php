<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/utils.php';
$title = 'Книжный магазин';

$pdo = db();

$cat = $_GET['category'] ?? '';
$author = $_GET['author'] ?? '';
$year = $_GET['year'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$w = ["status='available'"];
$p = [];
if ($cat !== '') { $w[] = 'category = :category'; $p['category'] = $cat; }
if ($author !== '') { $w[] = 'author = :author'; $p['author'] = $author; }
if ($year !== '') { $w[] = 'year = :year'; $p['year'] = (int)$year; }
$where = $w ? ('WHERE '.implode(' AND ', $w)) : '';

$allowedSort = ['title','author','year'];
if (!in_array($sort, $allowedSort, true)) $sort = 'title';

$stmt = $pdo->prepare("SELECT * FROM books $where ORDER BY $sort ASC");
$stmt->execute($p);
$books = $stmt->fetchAll();

$cats = $pdo->query("SELECT DISTINCT category FROM books ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$authors = $pdo->query("SELECT DISTINCT author FROM books ORDER BY author")->fetchAll(PDO::FETCH_COLUMN);
$years = $pdo->query("SELECT DISTINCT year FROM books ORDER BY year")->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Каталог</h1>
  <a class="btn btn-outline-secondary btn-sm" href="/"><i class="bi bi-arrow-counterclockwise me-1"></i>Сбросить</a>
</div>

<form method="get" class="form-inline-gap mb-3">
  <select name="category" class="form-select form-select-sm" style="max-width:220px;">
    <option value="">Категория</option>
    <?= select_options($cats, $cat) ?>
  </select>
  <select name="author" class="form-select form-select-sm" style="max-width:220px;">
    <option value="">Автор</option>
    <?= select_options($authors, $author) ?>
  </select>
  <select name="year" class="form-select form-select-sm" style="max-width:160px;">
    <option value="">Год</option>
    <?= select_options(array_map('strval',$years), $year) ?>
  </select>
  <select name="sort" class="form-select form-select-sm" style="max-width:200px;">
    <option value="title" <?=$sort==='title'?'selected':''?>>По названию</option>
    <option value="author" <?=$sort==='author'?'selected':''?>>По автору</option>
    <option value="year" <?=$sort==='year'?'selected':''?>>По году</option>
  </select>
  <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Фильтр</button>
</form>

<?php if (!$books): ?>
  <div class="alert alert-warning">Ничего не найдено. Попробуйте изменить фильтры.</div>
<?php endif; ?>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
  <?php foreach ($books as $b): ?>
    <div class="col">
      <div class="card h-100 card-book">
        <?php $src = book_cover_url($b['cover_url'] ?? null); $isPh = is_placeholder_url($src); ?>
        <img
          src="<?=h($src)?>"
          class="card-img-top <?= $isPh ? 'img-ph' : '' ?>"
          alt="<?=h($b['title'])?>"
          loading="lazy"
          onerror="this.onerror=null;this.src='<?=h(placeholder_image_url())?>'; this.classList.add('img-ph');"
        />

        <div class="card-body d-flex flex-column">
          <div class="d-flex align-items-center justify-content-between">
            <h3 class="h6 mb-1"><?=h($b['title'])?></h3>
            <span class="badge rounded-pill badge-soft"><?=h($b['category'])?></span>
          </div>
          <div class="text-secondary small mb-2">Автор: <?=h($b['author'])?> · <?=h((string)$b['year'])?></div>
          <p class="card-text flex-grow-1"><?=h($b['description'] ?? '')?></p>
          <div class="d-grid">
            <a class="btn btn-primary" href="/book.php?id=<?=$b['id']?>"><i class="bi bi-box-arrow-up-right me-1"></i>Подробнее</a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
