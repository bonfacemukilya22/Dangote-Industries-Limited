<?php
require __DIR__ . '/inc.php';
$err = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $em = strtolower(trim($_POST['email'] ?? ''));
    if (!captcha_ok()) $err = 'Robot check answer is wrong.';
    else {
        $s = db()->prepare('SELECT * FROM users WHERE email=?'); $s->execute([$em]); $u = $s->fetch();
        if (!$u || !password_verify($_POST['password'] ?? '', $u['hash'])) $err = 'Email or password is incorrect.';
        else {
            session_regenerate_id(true); $_SESSION['uid'] = $u['id'];
            $next = $_SESSION['next'] ?? 'profile.php'; unset($_SESSION['next']);
            go($next);
        }
    }
}
page_head('Sign in');
?>
<div class="sec form"><h1>Sign in</h1>
<p class="mut">Credentials are case sensitive. No account? <a href="signup.php"><u>Create an account</u></a> to apply for our career opportunities.</p>
<?php if ($err) echo '<p class="err">' . e($err) . '</p>'; ?>
<form method="post"><?= csrf_field() ?>
<label for="email">Email address *</label><input id="email" name="email" type="email" required autocomplete="email">
<label for="password">Password *</label><input id="password" name="password" type="password" required autocomplete="current-password">
<label for="cap">Confirm you are not a robot: what is <?= captcha_q() ?>? *</label><input id="cap" name="cap" inputmode="numeric" required autocomplete="off">
<p><button class="btn s">Sign in</button></p></form></div>
<?php page_foot();
