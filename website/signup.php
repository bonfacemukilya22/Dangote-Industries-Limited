<?php
require __DIR__ . '/inc.php';
$err = ''; $v = ['first'=>'','last'=>'','email'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $v = ['first'=>trim($_POST['first'] ?? ''),'last'=>trim($_POST['last'] ?? ''),'email'=>strtolower(trim($_POST['email'] ?? ''))];
    $p = $_POST['password'] ?? '';
    if (!captcha_ok()) $err = 'Robot check answer is wrong.';
    elseif ($v['first'] === '' || $v['last'] === '') $err = 'Enter your first and last name.';
    elseif (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err = 'Enter a valid email address.';
    elseif (!strong_password($p)) $err = 'Password must be at least 8 characters with an upper case letter, a lower case letter and a number.';
    elseif ($p !== ($_POST['password2'] ?? '')) $err = 'Passwords do not match.';
    elseif (empty($_POST['agree'])) $err = 'You must accept the data privacy consent statement to create an account.';
    else {
        $x = db()->prepare('SELECT 1 FROM users WHERE email=?'); $x->execute([$v['email']]);
        if ($x->fetch()) $err = 'An account with this email already exists.';
        else {
            db()->prepare('INSERT INTO users(first,last,email,hash,joined) VALUES(?,?,?,?,?)')->execute([$v['first'], $v['last'], $v['email'], password_hash($p, PASSWORD_DEFAULT), date('Y-m-d')]);
            session_regenerate_id(true); $_SESSION['uid'] = db()->lastInsertId();
            $next = $_SESSION['next'] ?? 'profile.php'; unset($_SESSION['next']); go($next);
        }
    }
}
page_head('Create an account');
?>
<div class="sec form"><h1>Create an account</h1>
<p class="mut">Already registered? <a href="signin.php"><u>Please sign in</u></a></p>
<?php if ($err) echo '<p class="err">' . e($err) . '</p>'; ?>
<form method="post"><?= csrf_field() ?>
<label for="first">First name *</label><input id="first" name="first" required value="<?= e($v['first']) ?>">
<label for="last">Last name *</label><input id="last" name="last" required value="<?= e($v['last']) ?>">
<label for="email">Email address *</label><input id="email" name="email" type="email" required value="<?= e($v['email']) ?>">
<label for="password">Password *</label><input id="password" name="password" type="password" required autocomplete="new-password">
<p class="mut" style="font-size:.9rem">At least 8 characters with an upper case letter, a lower case letter and a number.</p>
<label for="password2">Retype password *</label><input id="password2" name="password2" type="password" required autocomplete="new-password">
<label>Data privacy consent statement</label><div class="policy"><?= nl2br(e(setting('policy'))) ?></div>
<p><label class="chk"><input type="checkbox" name="agree" value="1" required> I have read and accept the data privacy consent statement.</label></p>
<label for="cap">Confirm you are not a robot: what is <?= captcha_q() ?>? *</label><input id="cap" name="cap" inputmode="numeric" required autocomplete="off">
<p><button class="btn s">Sign up</button></p></form></div>
<?php page_foot();
