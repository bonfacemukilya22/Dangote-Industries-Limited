<?php
require __DIR__ . '/inc.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') go('jobs.php');
check_csrf();
$days = max(1, min(365, (int)($_POST['days'] ?? 7)));
$k = trim($_POST['k'] ?? ''); $l = trim($_POST['l'] ?? '');
$back = 'jobs.php?' . http_build_query(['k' => $k, 'l' => $l]);
$u = current_user();
if (!$u) { $_SESSION['next'] = $back; $_SESSION['flash'] = 'Please sign in to create a job alert.'; go('signin.php'); }
db()->prepare('INSERT INTO alerts(user_id,keyword,location,days,created) VALUES(?,?,?,?,?)')->execute([$u['id'], $k, $l, $days, date('Y-m-d')]);
go($back . '&alert=ok');