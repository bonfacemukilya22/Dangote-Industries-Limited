<?php
require __DIR__ . '/inc.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') go('jobs.php');
check_csrf();
$id = (int)($_POST['job'] ?? 0);
$u = current_user();
if (!$u) { $_SESSION['next'] = 'job.php?id=' . $id; $_SESSION['flash'] = 'Please sign in to apply.'; go('signin.php'); }
$ok = db()->prepare('SELECT 1 FROM jobs WHERE id=?'); $ok->execute([$id]);
if ($ok->fetch()) db()->prepare('INSERT OR IGNORE INTO applications(user_id,job_id,applied) VALUES(?,?,?)')->execute([$u['id'], $id, date('Y-m-d')]);
go('job.php?id=' . $id . '&done=1');
