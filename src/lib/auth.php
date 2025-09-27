<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

session_start();

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function require_login(): void {
  if (!current_user()) {
    header('Location: /login.php'); exit;
  }
}

function require_admin(): void {
  $u = current_user();
  if (!$u || $u['role'] !== 'admin') {
    http_response_code(403);
    echo "Forbidden"; exit;
  }
}

function attempt_login(string $email, string $password): bool {
  $stmt = db()->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $u = $stmt->fetch();
  if ($u && password_verify($password, $u['password_hash'])) {
    $_SESSION['user'] = $u;
    return true;
  }
  // Для записей, созданных через init.sql (crypt), разрешим бэкап-проверку:
  if ($u) {
    $stmt2 = db()->prepare("SELECT crypt(:pwd, :hash) = :hash AS ok");
    $stmt2->execute(['pwd' => $password, 'hash' => $u['password_hash']]);
    $row = $stmt2->fetch();
    if ($row && $row['ok'] === true) {
      $_SESSION['user'] = $u;
      return true;
    }
  }
  return false;
}
