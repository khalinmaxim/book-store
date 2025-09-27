<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/utils.php';

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (attempt_login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
    header('Location: /'); exit;
  } else $err = 'Неверный логин или пароль';
}
?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>Вход</title>
<link rel="stylesheet" href="/public/styles.css"></head><body><div class="container">
<h2>Вход</h2>
<?php if ($err): ?><div style="color:red;"><?=h($err)?></div><?php endif; ?>
<form method="post" class="form-row" style="max-width:420px;">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Пароль" required>
  <button class="btn primary">Войти</button>
</form>
<a class="btn" href="/register.php">Регистрация</a>
</div></body></html>
