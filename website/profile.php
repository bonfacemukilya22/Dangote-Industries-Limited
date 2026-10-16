<?php
require __DIR__ . '/inc.php';
$u = current_user();
if (!$u) { $_SESSION['next'] = 'profile.php'; go('signin.php'); }
$msg = ''; $pwmsg = ''; $pwerr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['act'] ?? '') === 'password') {
        $new = $_POST['new'] ?? '';
        if (!password_verify($_POST['current'] ?? '', $u['hash'])) $pwerr = 'Your current password is wrong.';
        elseif (!strong_password($new)) $pwerr = 'New password must be at least 8 characters with an upper case letter, a lower case letter and a number.';
        elseif ($new !== ($_POST['new2'] ?? '')) $pwerr = 'The new passwords do not match.';
        else { db()->prepare('UPDATE users SET hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]); $pwmsg = 'Password changed.'; }
    } else {
        $f = trim($_POST['first'] ?? ''); $l = trim($_POST['last'] ?? '');
        if ($f && $l) { db()->prepare('UPDATE users SET first=?, last=?, phone=? WHERE id=?')->execute([$f, $l, trim($_POST['phone'] ?? ''), $u['id']]); $msg = 'Profile saved.'; $u = current_user(); }
    }
}
$a = db()->prepare('SELECT a.applied, j.title, j.id FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.user_id=? ORDER BY a.applied DESC'); $a->execute([$u['id']]); $apps = $a->fetchAll();
page_head('My Profile', 'profile.php');
?>
<div class="sec"><h1><?= e(t('prof')) ?></h1>
<form method="post" class="card form"><?= csrf_field() ?>
<label for="first">First name</label><input id="first" name="first" value="<?= e($u['first']) ?>" required>
<label for="last">Last name</label><input id="last" name="last" value="<?= e($u['last']) ?>" required>
<label for="em">Email</label><input id="em" value="<?= e($u['email']) ?>" disabled>
<label for="phone">Phone</label><input id="phone" name="phone" value="<?= e($u['phone']) ?>">
<p><button class="btn s">Save profile</button></p><?php if ($msg) echo '<p class="ok">' . e($msg) . '</p>'; ?></form>

<h2>Change password</h2>
<form method="post" class="card form"><?= csrf_field() ?><input type="hidden" name="act" value="password">
<label for="current">Current password</label><input id="current" name="current" type="password" required autocomplete="current-password">
<label for="new">New password</label><input id="new" name="new" type="password" required autocomplete="new-password">
<label for="new2">Retype new password</label><input id="new2" name="new2" type="password" required autocomplete="new-password">
<p><button class="btn s">Change password</button></p>
<?php if ($pwerr) echo '<p class="err">' . e($pwerr) . '</p>'; if ($pwmsg) echo '<p class="ok">' . e($pwmsg) . '</p>'; ?></form>

<h2>My applications</h2>
<?php foreach ($apps as $x) echo '<div class="card"><b><a href="job.php?id=' . $x['id'] . '">' . e($x['title']) . '</a></b><br><span class="mut">Applied ' . e($x['applied']) . '</span></div>'; if (!$apps) echo '<p class="mut">You have not applied to any jobs yet.</p>'; ?>
</div>
<?php page_foot();