<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/mailer.php';
require_once __DIR__ . '/../lib/utils.php';

$pdo = db();
$today = (new DateTimeImmutable('today'))->format('Y-m-d');

$q = $pdo->prepare("
  SELECT t.id, t.due_date, u.email, u.name, b.title, b.id AS book_id
  FROM transactions t
  JOIN users u ON u.id=t.user_id
  JOIN books b ON b.id=t.book_id
  WHERE t.type='rent' AND t.status='active'
    AND t.due_date IS NOT NULL
    AND (t.due_date <= :today OR t.due_date = :d1 OR t.due_date = :d2 OR t.due_date = :d3)
");
$d1 = (new DateTimeImmutable('+1 day'))->format('Y-m-d');
$d2 = (new DateTimeImmutable('+2 day'))->format('Y-m-d');
$d3 = (new DateTimeImmutable('+3 day'))->format('Y-m-d');
$q->execute(['today'=>$today,'d1'=>$d1,'d2'=>$d2,'d3'=>$d3]);

while ($row = $q->fetch()) {
  $bookUrl = app_url('/book.php?id='.$row['book_id']);
  $accountUrl = app_url('/rentals.php');
  [$subject, $html, $text] = tpl_rental_reminder($row['name'], $row['title'], $row['due_date'], $bookUrl, $accountUrl);

  try {
    send_email_html($row['email'], $subject, $html, $text);
    $pdo->prepare("INSERT INTO email_queue (to_email, subject, body, status, sent_at) VALUES (:e,:s,:b,'sent', now())")
        ->execute(['e'=>$row['email'], 's'=>$subject, 'b'=>$text]);
  } catch (Throwable $e) {
    $pdo->prepare("INSERT INTO email_queue (to_email, subject, body, status) VALUES (:e,:s,:b,'failed')")
        ->execute(['e'=>$row['email'], 's'=>$subject, 'b'=>$text]);
  }
}
