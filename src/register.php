<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/utils.php';

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  if ($name && $email && $pass) {
    $stmt = db()->prepare("INSERT INTO users(name,email,password_hash) VALUES (:n,:e,:p) RETURNING *");
    try {
      $stmt->execute(['n'=>$name,'e'=>$email,'p'=>password_hash($pass, PASSWORD_DEFAULT)]);
      $_SESSION['user'] = $stmt->fetch();
      header('Location: /'); exit;
    } catch (Throwable $e) { $err = 'Email уже используется'; }
  } else $err = 'Заполните все поля';
}
?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>Регистрация</title>
<link rel="stylesheet" href="/public/styles.css"></head><body><div class="container">
<h2>Регистрация</h2>
<?php if ($err): ?><div style="color:red;"><?=h($err)?></div><?php endif; ?>
<form method="post" class="form-row" style="max-width:420px;">
  <input name="name" placeholder="Имя" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Пароль" required>
  <button class="btn primary">Создать</button>
</form>
<a class="btn" href="/login.php">У меня есть аккаунт</a>
</div></body></html>
