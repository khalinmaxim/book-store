<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';

require_login();

$book_id = (int)($_POST['book_id'] ?? 0);
$type = $_POST['type'] ?? 'buy';
$duration = $_POST['duration'] ?? '2w';

$pdo = db();
$pdo->beginTransaction();

$book = $pdo->prepare("SELECT * FROM books WHERE id=:id FOR UPDATE");
$book->execute(['id'=>$book_id]);
$b = $book->fetch();
if (!$b || $b['status'] !== 'available' || $b['quantity'] <= 0) {
  $pdo->rollBack();
  header("Location: /book.php?id=$book_id&msg=".urlencode('Недоступно для заказа'));
  exit;
}

$user = current_user();
$price = 0; $due = null;
if ($type === 'buy') {
  $price = (float)$b['price_buy'];
} else {
  switch ($duration) {
    case '2w': $price=(float)$b['price_rent_2w']; $due=(new DateTimeImmutable('+14 days'))->format('Y-m-d'); break;
    case '1m': $price=(float)$b['price_rent_1m']; $due=(new DateTimeImmutable('+1 month'))->format('Y-m-d'); break;
    case '3m': $price=(float)$b['price_rent_3m']; $due=(new DateTimeImmutable('+3 months'))->format('Y-m-d'); break;
    default: $price=(float)$b['price_rent_2w']; $due=(new DateTimeImmutable('+14 days'))->format('Y-m-d');
  }
}

$stmt = $pdo->prepare("INSERT INTO transactions (user_id, book_id, type, due_date, price_paid, status)
VALUES (:uid,:bid,:type,:due,:price,'active') RETURNING id");
$stmt->execute([
  'uid'=>$user['id'],
  'bid'=>$b['id'],
  'type'=>$type,
  'due'=>$due,
  'price'=>$price
]);

// уменьшить количество только при аренде (или и при покупке — как бизнес-правило)
$pdo->prepare("UPDATE books SET quantity = quantity - 1 WHERE id=:id")->execute(['id'=>$b['id']]);

$pdo->commit();

$back = "/book.php?id=".$b['id']."&msg=".urlencode($type==='buy'?'Покупка оформлена':'Аренда оформлена до '.$due);
header("Location: $back");
