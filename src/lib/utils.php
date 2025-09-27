<?php
declare(strict_types=1);

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function select_options(array $items, ?string $selected = null): string {
  $html = '';
  foreach ($items as $v) {
    $sel = ($selected !== null && $selected === $v) ? ' selected' : '';
    $html .= "<option value=\"".h($v)."\"$sel>".h($v)."</option>";
  }
  return $html;
}

function placeholder_image_url(): string {
  return 'https://imgholder.ru/600x300/8493a8/adb9ca?text=IMAGE+NOT+FOUND&font=kelson';
}

function is_placeholder_url(string $url): bool {
  if (function_exists('str_starts_with')) return str_starts_with($url, 'https://imgholder.ru/');
  return strpos($url, 'https://imgholder.ru/') === 0;
}

/** Вернёт URL обложки или плейсхолдер */
function book_cover_url(?string $url): string {
  $u = trim((string)$url);
  if ($u !== '' && (filter_var($u, FILTER_VALIDATE_URL) || (function_exists('str_starts_with') ? str_starts_with($u, '/') : strpos($u, '/') === 0))) {
    return $u;
  }
  return placeholder_image_url();
}

function app_url(string $path = '/'): string {
  $base = rtrim(getenv('APP_URL') ?: 'http://localhost:8080', '/');
  if ($path === '' || $path[0] !== '/') $path = '/'.$path;
  return $base.$path;
}

