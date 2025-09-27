<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/utils.php';

require_login();

$pdo   = db();
$user  = current_user();
$scope = $_GET['scope'] ?? 'all'; // all | active | rent | buy | due | overdue
$today = new DateTimeImmutable('today');

// Фильтры
$where = ["t.user_id = :uid"];
$params = ['uid' => $user['id']];

switch ($scope) {
  case 'active':
    $where[] = "t.status = 'active'";
    break;
  case 'rent':
    $where[] = "t.type = 'rent'";
    break;
  case 'buy':
    $where[] = "t.type = 'buy'";
    break;
  case 'due':
    $where[] = "t.type='rent' AND t.status='active' AND t.due_date BETWEEN :d1 AND :d3";
    $params['d1'] = $today->format('Y-m-d');
    $params['d3'] = $today->modify('+3 days')->format('Y-m-d');
    break;
  case 'overdue':
    $where[] = "t.type='rent' AND t.status='active' AND t.due_date < :d0";
    $params['d0'] = $today->format('Y-m-d');
    break;
}

$sql = "
  SELECT t.*, b.title, b.id AS book_id
  FROM transactions t
  JOIN books b ON b.id = t.book_id
  WHERE " . implode(' AND ', $where) . "
  ORDER BY t.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$title = 'Мои покупки и аренды';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Мои покупки и аренды</h1>
  <a class="btn btn-outline-secondary btn-sm" href="/"><i class="bi bi-house me-1"></i>В каталог</a>
</div>

<ul class="nav nav-pills mb-3">
  <li class="nav-item"><a class="nav-link <?= $scope==='all'?'active':'' ?>" href="/rentals.php">Все</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='active'?'active':'' ?>" href="/rentals.php?scope=active">Активные</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='rent'?'active':'' ?>" href="/rentals.php?scope=rent">Аренды</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='buy'?'active':'' ?>" href="/rentals.php?scope=buy">Покупки</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='due'?'active':'' ?>" href="/rentals.php?scope=due">До 3 дней</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='overdue'?'active':'' ?>" href="/rentals.php?scope=overdue">Просрочено</a></li>
</ul>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Книга</th>
            <th style="width:110px;">Тип</th>
            <th style="width:120px;">Начало</th>
            <th style="width:120px;">Срок до</th>
            <th style="width:110px;">Осталось</th>
            <th style="width:120px;">Цена</th>
            <th style="width:120px;">Статус</th>
            <th style="width:120px;">Действия</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr>
            <td colspan="8" class="text-center text-secondary py-4">
              Пока пусто. Оформите покупку или аренду — здесь появится история.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $r):
            $type = $r['type'] === 'rent' ? 'Аренда' : 'Покупка';
            $typeBadge = $r['type'] === 'rent'
              ? '<span class="badge rounded-pill bg-info text-dark">Аренда</span>'
              : '<span class="badge rounded-pill bg-secondary">Покупка</span>';

            $dueHtml = '—';
            $leftHtml = '—';
            $statusBadge = '<span class="badge rounded-pill bg-secondary">'.h($r['status']).'</span>';

            if ($r['type'] === 'rent' && !empty($r['due_date'])) {
              $due = new DateTimeImmutable($r['due_date']);
              $dueHtml = h($due->format('Y-m-d'));
              $diffDays = (int)$due->diff($today)->format('%r%a'); // >0 просрочено; <0 осталось
              if ($diffDays > 0) {
                $leftHtml = '<span class="text-danger fw-semibold">+'. $diffDays .' д.</span>';
                $statusBadge = '<span class="badge rounded-pill bg-danger">overdue</span>';
              } elseif ($diffDays === 0) {
                $leftHtml = '<span class="text-warning fw-semibold">сегодня</span>';
                $statusBadge = '<span class="badge rounded-pill bg-warning text-dark">due today</span>';
              } else {
                $leftHtml = '<span class="text-success fw-semibold">'. abs($diffDays) .' д.</span>';
                $statusBadge = '<span class="badge rounded-pill bg-success">'.h($r['status']).'</span>';
              }
            }

            $price = number_format((float)$r['price_paid'], 2, '.', ' ');
          ?>
          <tr>
            <td>
              <a href="/book.php?id=<?= (int)$r['book_id'] ?>" class="link-body-emphasis text-decoration-none fw-semibold">
                <?= h($r['title']) ?>
              </a>
            </td>
            <td><?= $typeBadge ?></td>
            <td><span class="text-secondary"><?= h($r['start_date']) ?></span></td>
            <td><?= $dueHtml ?></td>
            <td><?= $leftHtml ?></td>
            <td><strong><?= $price ?> ₽</strong></td>
            <td><?= $statusBadge ?></td>
            <td>
              <a href="/book.php?id=<?= (int)$r['book_id'] ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-arrow-up-right"></i> О книге
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
