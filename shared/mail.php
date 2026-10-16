<?php
// Sends an email with PHP mail() and keeps a copy in data/outbox.log so you can test locally.
function send_mail($to, $subject, $body) {
    $host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    $host = preg_replace('/:\d+$/', '', $host);
    $headers = "From: Savanna Group <noreply@$host>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    @file_put_contents(__DIR__ . '/../data/outbox.log', '[' . date('Y-m-d H:i') . "] To: $to | $subject\n$body\n-----\n", FILE_APPEND);
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}