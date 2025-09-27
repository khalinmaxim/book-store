<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin(); // проверка ДО вывода
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/mailer.php';
require_once __DIR__ . '/../lib/utils.php';

$pdo = db();
$today = new DateTimeImmutable('today');
$scope = $_GET['scope'] ?? 'all'; // all | due | overdue
$notice = '';

// Ручная отправка напоминания
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_now'])) {
  $tid = (int)$_POST['id'];
  $r = $pdo->prepare("SELECT t.*, u.email, u.name, b.title, b.id AS book_id
                      FROM transactions t
                      JOIN users u ON u.id = t.user_id
                      JOIN books b ON b.id = t.book_id
                      WHERE t.id = :id");
  $r->execute(['id' => $tid]);
  if ($row = $r->fetch()) {
    $bookUrl = app_url('/book.php?id=' . $row['book_id']);
    $accountUrl = app_url('/rentals.php');
    [$subject, $html, $text] = tpl_rental_reminder($row['name'], $row['title'], $row['due_date'], $bookUrl, $accountUrl);
    try {
      send_email_html($row['email'], $subject, $html, $text);
      $notice = 'Напоминание отправлено: ' . htmlspecialchars($row['email'], ENT_QUOTES);
    } catch (Throwable $e) {
      $notice = 'Ошибка отправки: ' . $e->getMessage();
    }
  }
}

// Выборка с фильтрами
$cond = "t.type='rent' AND t.status='active' AND t.due_date IS NOT NULL";
$params = [];
if ($scope === 'due') {
  $d1 = $today->format('Y-m-d');
  $d4 = $today->modify('+3 days')->format('Y-m-d');
  $cond .= " AND t.due_date BETWEEN :d1 AND :d4";
  $params += ['d1' => $d1, 'd4' => $d4];
} elseif ($scope === 'overdue') {
  $d0 = $today->format('Y-m-d');
  $cond .= " AND t.due_date < :d0";
  $params += ['d0' => $d0];
}

$sql = "
  SELECT t.*, u.email, u.name, b.title, b.id AS book_id
  FROM transactions t
  JOIN users u ON u.id = t.user_id
  JOIN books b ON b.id = t.book_id
  WHERE $cond
  ORDER BY t.due_date ASC NULLS LAST, t.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$title = 'Админка — Аренды';
require __DIR__ . '/../partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">Активные аренды</h1>
  <a class="btn btn-outline-secondary btn-sm" href="/admin/"><i class="bi bi-arrow-left me-1"></i>Назад</a>
</div>

<?php if ($notice): ?>
  <div class="alert alert-info"><?= htmlspecialchars($notice, ENT_QUOTES) ?></div>
<?php endif; ?>

<ul class="nav nav-pills mb-3">
  <li class="nav-item"><a class="nav-link <?= $scope==='all'?'active':'' ?>" href="/admin/rentals.php">Все</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='due'?'active':'' ?>" href="/admin/rentals.php?scope=due">До 3 дней</a></li>
  <li class="nav-item"><a class="nav-link <?= $scope==='overdue'?'active':'' ?>" href="/admin/rentals.php?scope=overdue">Просрочено</a></li>
</ul>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Книга</th>
            <th>Пользователь</th>
            <th>Начало</th>
            <th>До</th>
            <th>Осталось</th>
            <th>Статус</th>
            <th style="width: 220px;">Действия</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="7" class="text-center text-secondary py-4">Нет записей</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r):
            $due = $r['due_date'] ? new DateTimeImmutable($r['due_date']) : null;
            $diffDays = $due ? (int)$due->diff($today)->format('%r%a') : null; // отрицательное — осталось дней
            // оформление
            $badge = '<span class="badge rounded-pill bg-secondary">active</span>';
            $leftHtml = '—';
            if ($due) {
              if ($diffDays > 0) { // просрочно
                $leftHtml = '<span class="text-danger fw-semibold">+'. $diffDays .' д.</span>';
                $badge = '<span class="badge rounded-pill bg-danger">overdue</span>';
              } elseif ($diffDays === 0) {
                $leftHtml = '<span class="text-warning fw-semibold">сегодня</span>';
                $badge = '<span class="badge rounded-pill bg-warning text-dark">due today</span>';
              } else { // осталось
                $leftHtml = '<span class="text-success fw-semibold">'. abs($diffDays) .' д.</span>';
                if (abs($diffDays) <= 3) {
                  $badge = '<span class="badge rounded-pill bg-info">due soon</span>';
                } else {
                  $badge = '<span class="badge rounded-pill bg-success">active</span>';
                }
              }
            }
          ?>
          <tr>
            <td>
              <div class="fw-semibold"><?= h($r['title']) ?></div>
              <div class="small text-secondary">ID #<?= (int)$r['book_id'] ?></div>
            </td>
            <td>
              <div class="fw-semibold"><?= h($r['name']) ?></div>
              <div class="small text-secondary"><?= h($r['email']) ?></div>
            </td>
            <td><span class="text-secondary"><?= h($r['start_date']) ?></span></td>
            <td><?= h($r['due_date']) ?></td>
            <td><?= $leftHtml ?></td>
            <td><?= $badge ?></td>
            <td class="text-nowrap">
              <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-outline-primary" name="send_now" title="Отправить напоминание">
                  <i class="bi bi-envelope"></i> Отправить
                </button>
              </form>
              <a href="<?= app_url('/book.php?id='.(int)$r['book_id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
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

<?php require __DIR__ . '/../partials/footer.php'; ?>
