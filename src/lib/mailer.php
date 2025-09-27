<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';

function make_mailer(): PHPMailer {
  $mail = new PHPMailer(true);
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->Host = getenv('SMTP_HOST') ?: 'mailhog';
  $mail->Port = (int)(getenv('SMTP_PORT') ?: '1025');
  $user = getenv('SMTP_USER');
  if ($user) {
    $mail->SMTPAuth = true;
    $mail->Username = $user;
    $mail->Password = getenv('SMTP_PASS') ?: '';
  }
  $mail->setFrom(getenv('SMTP_FROM') ?: 'no-reply@bookstore.local', getenv('SMTP_FROM_NAME') ?: 'Bookstore');
  return $mail;
}

/** Универсальная отправка HTML-писем с plain-text фолбеком */
function send_email_html(string $to, string $subject, string $html, ?string $textFallback = null): bool {
  $mail = make_mailer();
  $mail->addAddress($to);
  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body = $html;
  $mail->AltBody = $textFallback ?? strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html));
  return $mail->send();
}

/** Обёртка письма (табличная вёрстка, инлайн-стили) */
function email_wrap(string $title, string $contentHtml): string {
  $brand = htmlspecialchars(getenv('SMTP_FROM_NAME') ?: 'Bookstore', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $app   = app_url('/');
  $year  = date('Y');

  // ВАЖНО: нет лишних пробелов до/после идентификатора HTML
  $html = <<<HTML
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{$title}</title>
  </head>
  <body style="margin:0;background:#f5f7fb;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;">
      <tr>
        <td align="center" style="padding:24px;">
          <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="width:640px;max-width:100%;background:#ffffff;border:1px solid #eaeef5;border-radius:12px;overflow:hidden;">
            <tr>
              <td style="background:#0d6efd;color:#ffffff;padding:16px 24px;font:600 18px system-ui,-apple-system,'Segoe UI',Roboto,Arial;">
                📚 {$brand}
              </td>
            </tr>
            <tr>
              <td style="padding:24px;font:400 16px/1.5 system-ui,-apple-system,'Segoe UI',Roboto,Arial;color:#1f2937;">
                {$contentHtml}
              </td>
            </tr>
            <tr>
              <td style="padding:16px 24px;background:#f8fafc;font:400 12px/1.4 system-ui,-apple-system,'Segoe UI',Roboto,Arial;color:#64748b;">
                Это письмо отправлено автоматически. Не отвечайте на него.<br>
                <a href="{$app}" style="color:#0d6efd;text-decoration:none;">{$brand}</a> · © {$year}
              </td>
            </tr>
          </table>
          <div style="height:24px;"></div>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;

  return $html;
}

/** Контент напоминания об аренде */
function tpl_rental_reminder(string $userName, string $bookTitle, string $dueDateISO, string $bookUrl, string $accountUrl): array {
  $dueHuman = (new DateTimeImmutable($dueDateISO))->format('d.m.Y');
  $subject  = "Напоминание: аренда книги «{$bookTitle}» до {$dueHuman}";

  $btnPrimary = "display:inline-block;background:#0d6efd;color:#fff;text-decoration:none;padding:12px 18px;border-radius:8px;font:600 15px system-ui,-apple-system,'Segoe UI',Roboto,Arial;";
  $btnGhost   = "display:inline-block;background:#eef2ff;color:#1f2937;text-decoration:none;padding:12px 18px;border-radius:8px;font:600 15px system-ui,-apple-system,'Segoe UI',Roboto,Arial;border:1px solid #c7d2fe;";

  $content = <<<HTML
<h1 style="margin:0 0 8px 0;font:700 20px system-ui,-apple-system,'Segoe UI',Roboto,Arial;">Здравствуйте, {$userName}!</h1>
<p style="margin:0 0 12px 0;">Напоминаем, что аренда книги <strong>«{$bookTitle}»</strong> истекает <strong>{$dueHuman}</strong>.</p>
<div style="margin:16px 0 20px 0;">
  <a href="{$accountUrl}" style="{$btnPrimary}">Продлить / Управлять арендой</a>
  <span style="display:inline-block;width:8px;"></span>
  <a href="{$bookUrl}" style="{$btnGhost}">О книге</a>
</div>
<p style="margin:16px 0 0 0;color:#475569;">Если вы уже вернули книгу — спасибо! В противном случае, пожалуйста, продлите или верните её вовремя.</p>
HTML;

  $html = email_wrap($subject, $content);

  $text = "Здравствуйте, {$userName}!\n"
        . "Напоминаем, что аренда книги «{$bookTitle}» истекает {$dueHuman}.\n\n"
        . "Продлить / Управлять: {$accountUrl}\n"
        . "О книге: {$bookUrl}\n";

  return [$subject, $html, $text];
}
