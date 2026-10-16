<?php
// Savanna Group admin panel. Separate from the public website: it has no link from the site.
require __DIR__ . '/../shared/db.php';
require __DIR__ . '/../shared/mail.php';
session_name('savanna_admin');
session_start();
function csrf() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function go($u) { header('Location: ' . $u); exit; }
function site_url() {
    $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $s . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\') . '/website/';
}
function emails_for($aud) {
    $q = [
        'alerts'      => 'SELECT DISTINCT u.email FROM alerts a JOIN users u ON u.id=a.user_id',
        'users'       => 'SELECT email FROM users',
        'subscribers' => 'SELECT email FROM subscribers',
    ];
    $out = [];
    foreach (($aud === 'all' ? array_keys($q) : [$aud]) as $k)
        if (isset($q[$k])) foreach (db()->query($q[$k])->fetchAll(PDO::FETCH_COLUMN) as $em) $out[strtolower($em)] = $em;
    return array_values($out);
}
function random_password() {
    $a = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; $b = 'abcdefghijkmnopqrstuvwxyz'; $c = '23456789';
    $p = $a[random_int(0, strlen($a) - 1)] . $b[random_int(0, strlen($b) - 1)] . $c[random_int(0, strlen($c) - 1)];
    $all = $a . $b . $c;
    for ($i = 0; $i < 7; $i++) $p .= $all[random_int(0, strlen($all) - 1)];
    return str_shuffle($p);
}
$err = ''; $msg = $_SESSION['msg'] ?? ''; unset($_SESSION['msg']);

if (isset($_GET['logout'])) { session_destroy(); go('index.php'); }

// ---- Login ----
if (empty($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_SESSION['lock'] ?? 0) > time()) $err = 'Too many attempts. Wait a minute and try again.';
        elseif (!hash_equals(csrf(), $_POST['csrf'] ?? '')) $err = 'Invalid request.';
        else {
            $s = db()->prepare('SELECT * FROM admins WHERE username=?'); $s->execute([trim($_POST['username'] ?? '')]); $a = $s->fetch();
            if ($a && password_verify($_POST['password'] ?? '', $a['hash'])) {
                session_regenerate_id(true); $_SESSION['admin'] = $a['id']; $_SESSION['fails'] = 0; go('index.php');
            }
            $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
            if ($_SESSION['fails'] >= 5) { $_SESSION['lock'] = time() + 60; $_SESSION['fails'] = 0; }
            $err = 'Username or password is incorrect.';
        }
    }
    ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="admin.css"></head>
    <body class="login"><form method="post" class="box"><h1>Savanna Admin</h1><?= field() ?>
    <?php if ($err) echo '<p class="err">' . e($err) . '</p>'; ?>
    <label for="u">Username</label><input id="u" name="username" required autocomplete="username">
    <label for="p">Password</label><input id="p" name="password" type="password" required autocomplete="current-password">
    <p><button class="btn">Sign in</button></p></form></body></html><?php
    exit;
}

// ---- Actions (POST) ----
$p = $_GET['p'] ?? 'jobs';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) die('Invalid request.');
    $act = $_POST['act'] ?? ''; $d = db();
    if ($act === 'save_job') {
        $v = [trim($_POST['title']), trim($_POST['dept']), trim($_POST['location']), trim($_POST['published']), trim($_POST['description']), trim($_POST['requirements']), trim($_POST['skills'])];
        if ($v[0] === '') { $_SESSION['msg'] = 'Job title is required.'; go('index.php?p=jobs'); }
        if (!empty($_POST['id'])) { $v[] = (int)$_POST['id']; $d->prepare('UPDATE jobs SET title=?,dept=?,location=?,published=?,description=?,requirements=?,skills=? WHERE id=?')->execute($v); $_SESSION['msg'] = 'Job updated.'; }
        else { $d->prepare('INSERT INTO jobs(title,dept,location,published,description,requirements,skills) VALUES(?,?,?,?,?,?,?)')->execute($v); $_SESSION['msg'] = 'Job added.'; }
        go('index.php?p=jobs');
    }
    if ($act === 'del_job') { $d->prepare('DELETE FROM jobs WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['msg'] = 'Job deleted.'; go('index.php?p=jobs'); }
    if ($act === 'del_user') { $d->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['msg'] = 'User removed.'; go('index.php?p=users'); }
    if ($act === 'edit_user') { $d->prepare('UPDATE users SET first=?,last=?,phone=? WHERE id=?')->execute([trim($_POST['first']), trim($_POST['last']), trim($_POST['phone']), (int)$_POST['id']]); $_SESSION['msg'] = 'User updated.'; go('index.php?p=users'); }
    if ($act === 'del_sub') { $d->prepare('DELETE FROM subscribers WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['msg'] = 'Email removed.'; go('index.php?p=subscribers'); }
    if ($act === 'del_alert') { $d->prepare('DELETE FROM alerts WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['msg'] = 'Alert removed.'; go('index.php?p=alerts'); }
    if ($act === 'site') {
        foreach (['hero_title','hero_text','about','faq','policy'] as $k) $d->prepare('INSERT OR REPLACE INTO settings(k,v) VALUES(?,?)')->execute([$k, trim($_POST[$k] ?? '')]);
        $_SESSION['msg'] = 'Site content saved.'; go('index.php?p=site');
    }
    if ($act === 'account') {
        $s = $d->prepare('SELECT * FROM admins WHERE id=?'); $s->execute([$_SESSION['admin']]); $a = $s->fetch();
        $nu = trim($_POST['username'] ?? ''); $np = $_POST['new'] ?? '';
        if (!password_verify($_POST['current'] ?? '', $a['hash'])) $_SESSION['msg'] = 'Current password is wrong.';
        elseif ($nu === '' || ($np !== '' && strlen($np) < 8)) $_SESSION['msg'] = 'Enter a username and a new password of at least 8 characters.';
        else { $d->prepare('UPDATE admins SET username=?, hash=? WHERE id=?')->execute([$nu, $np !== '' ? password_hash($np, PASSWORD_DEFAULT) : $a['hash'], $a['id']]); $_SESSION['msg'] = 'Account updated.'; }
        go('index.php?p=account');
    }
    // Set or generate a new password for a user
    if ($act === 'reset_pw') {
        $s = $d->prepare('SELECT * FROM users WHERE id=?'); $s->execute([(int)$_POST['id']]); $u = $s->fetch();
        $np = trim($_POST['newpw'] ?? '');
        if (!$u) $_SESSION['msg'] = 'User not found.';
        elseif ($np !== '' && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $np)) $_SESSION['msg'] = 'Password needs 8+ characters with an upper case letter, a lower case letter and a number.';
        else {
            if ($np === '') $np = random_password();
            $d->prepare('UPDATE users SET hash=? WHERE id=?')->execute([password_hash($np, PASSWORD_DEFAULT), $u['id']]);
            $_SESSION['msg'] = 'New password for ' . $u['email'] . ' is: ' . $np . '   (send it to the user and ask them to change it on My Profile)';
        }
        go('index.php?p=passwords');
    }
    // Send a message to a group of people
    if ($act === 'send_update') {
        $aud = $_POST['audience'] ?? 'alerts'; $sub = trim($_POST['subject'] ?? ''); $body = trim($_POST['body'] ?? '');
        if ($sub === '' || $body === '') { $_SESSION['msg'] = 'Enter a subject and a message.'; go('index.php?p=send'); }
        $ok = 0; $fail = 0;
        foreach (array_slice(emails_for($aud), 0, 500) as $em) { if (send_mail($em, $sub, $body)) $ok++; else $fail++; }
        $d->prepare('INSERT INTO updates(subject,body,audience,sent_ok,sent_fail,created) VALUES(?,?,?,?,?,?)')->execute([$sub, $body, $aud, $ok, $fail, date('Y-m-d H:i')]);
        $_SESSION['msg'] = "Update processed: $ok sent, $fail failed.";
        go('index.php?p=send');
    }
    // Send the jobs that match one person's alert
    if ($act === 'send_alert') {
        $s = $d->prepare('SELECT a.*, u.email, u.first FROM alerts a JOIN users u ON u.id=a.user_id WHERE a.id=?'); $s->execute([(int)$_POST['id']]); $a = $s->fetch();
        if (!$a) { $_SESSION['msg'] = 'Alert not found.'; go('index.php?p=alerts'); }
        $w = ' WHERE 1'; $args = [];
        if ($a['keyword'] !== '') { $w .= ' AND (title LIKE ? OR dept LIKE ? OR description LIKE ? OR skills LIKE ?)'; array_push($args, '%' . $a['keyword'] . '%', '%' . $a['keyword'] . '%', '%' . $a['keyword'] . '%', '%' . $a['keyword'] . '%'); }
        if ($a['location'] !== '') { $w .= ' AND location LIKE ?'; $args[] = '%' . $a['location'] . '%'; }
        $q = $d->prepare('SELECT * FROM jobs' . $w . ' ORDER BY published DESC, id DESC LIMIT 10'); $q->execute($args); $jobs = $q->fetchAll();
        if (!$jobs) { $_SESSION['msg'] = 'No jobs match this alert right now.'; go('index.php?p=alerts'); }
        $body = 'Hello ' . $a['first'] . ",\n\nThese jobs match your job alert:\n\n";
        foreach ($jobs as $j) $body .= '- ' . $j['title'] . ' (' . $j['location'] . ")\n  " . site_url() . 'job.php?id=' . $j['id'] . "\n";
        $body .= "\nSavanna Group Careers";
        $_SESSION['msg'] = send_mail($a['email'], 'New jobs matching your alert', $body) ? 'Alert email sent to ' . $a['email'] . '.' : 'Could not send to ' . $a['email'] . '. A copy was saved in data/outbox.log.';
        go('index.php?p=alerts');
    }
}

// ---- Panel ----
$tabs = ['jobs'=>'Jobs','users'=>'Users','applications'=>'Applications','subscribers'=>'Subscribers','alerts'=>'Job alerts','send'=>'Send update','passwords'=>'Passwords','site'=>'Site content','account'=>'Account'];
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin panel</title><link rel="stylesheet" href="admin.css"></head><body>
<header><b>Savanna Admin</b><nav><?php foreach ($tabs as $k => $n) echo '<a href="?p=' . $k . '"' . ($p === $k ? ' class="on"' : '') . '>' . $n . '</a>'; ?><a href="?logout=1">Sign out</a></nav></header>
<main>
<?php if ($msg) echo '<p class="ok">' . e($msg) . '</p>';
$d = db();
if ($p === 'jobs') {
    $edit = null;
    if (isset($_GET['edit'])) { $s = $d->prepare('SELECT * FROM jobs WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit = $s->fetch(); }
    if ($edit || isset($_GET['new'])) {
        $j = $edit ?: ['id'=>'','title'=>'','dept'=>'','location'=>'','published'=>date('Y-m-d'),'description'=>'','requirements'=>'','skills'=>''];
        echo '<h1>' . ($edit ? 'Edit job' : 'Add job') . '</h1><form method="post" class="box">' . field() . '<input type="hidden" name="act" value="save_job"><input type="hidden" name="id" value="' . e($j['id']) . '">';
        foreach (['title'=>'Title','dept'=>'Department','location'=>'Location (write it as: City, Country)','published'=>'Date published (YYYY-MM-DD)'] as $k => $l) echo '<label>' . $l . '</label><input name="' . $k . '" value="' . e($j[$k]) . '">';
        foreach (['description'=>'Description','requirements'=>'Requirements (one per line)','skills'=>'Skills and competencies (one per line)'] as $k => $l) echo '<label>' . $l . '</label><textarea name="' . $k . '" rows="4">' . e($j[$k]) . '</textarea>';
        echo '<p><button class="btn">Save job</button> <a class="btn g" href="?p=jobs">Cancel</a></p></form>';
    } else {
        echo '<h1>Jobs</h1><p><a class="btn" href="?p=jobs&new=1">Add job</a></p><div class="sc"><table><tr><th>Title</th><th>Department</th><th>Location</th><th>Published</th><th></th></tr>';
        foreach ($d->query('SELECT * FROM jobs ORDER BY published DESC, id DESC') as $j)
            echo '<tr><td>' . e($j['title']) . '</td><td>' . e($j['dept']) . '</td><td>' . e($j['location']) . '</td><td>' . e($j['published']) . '</td><td><a class="btn g" href="?p=jobs&edit=' . $j['id'] . '">Edit</a> <form method="post" class="in" onsubmit="return confirm(\'Delete this job?\')">' . field() . '<input type="hidden" name="act" value="del_job"><input type="hidden" name="id" value="' . $j['id'] . '"><button class="btn d">Delete</button></form></td></tr>';
        echo '</table></div>';
    }
}
if ($p === 'users') {
    echo '<h1>Users</h1><div class="sc"><table><tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th></th></tr>';
    $forms = '';
    foreach ($d->query('SELECT * FROM users ORDER BY id DESC') as $u) {
        $f = 'form="f' . $u['id'] . '"';
        $forms .= '<form id="f' . $u['id'] . '" method="post">' . field() . '<input type="hidden" name="id" value="' . $u['id'] . '"></form>';
        echo '<tr><td><input ' . $f . ' name="first" value="' . e($u['first']) . '"><input ' . $f . ' name="last" value="' . e($u['last']) . '"></td><td>' . e($u['email']) . '</td><td><input ' . $f . ' name="phone" value="' . e($u['phone']) . '"></td><td>' . e($u['joined']) . '</td><td><button ' . $f . ' class="btn g" name="act" value="edit_user">Save</button> <button ' . $f . ' class="btn d" name="act" value="del_user" onclick="return confirm(\'Remove this user and their applications?\')">Remove</button></td></tr>';
    }
    echo '</table></div>' . $forms;
}
if ($p === 'applications') {
    echo '<h1>Applications</h1><div class="sc"><table><tr><th>Applicant</th><th>Email</th><th>Job</th><th>Date</th></tr>';
    foreach ($d->query('SELECT a.applied, u.first, u.last, u.email, j.title FROM applications a JOIN users u ON u.id=a.user_id JOIN jobs j ON j.id=a.job_id ORDER BY a.id DESC') as $a)
        echo '<tr><td>' . e($a['first'] . ' ' . $a['last']) . '</td><td>' . e($a['email']) . '</td><td>' . e($a['title']) . '</td><td>' . e($a['applied']) . '</td></tr>';
    echo '</table></div>';
}
if ($p === 'subscribers') {
    echo '<h1>Job update subscribers</h1><div class="sc"><table><tr><th>Email</th><th>Joined</th><th></th></tr>';
    foreach ($d->query('SELECT * FROM subscribers ORDER BY id DESC') as $s)
        echo '<tr><td>' . e($s['email']) . '</td><td>' . e($s['joined']) . '</td><td><form method="post" class="in" onsubmit="return confirm(\'Remove this email?\')">' . field() . '<input type="hidden" name="act" value="del_sub"><input type="hidden" name="id" value="' . $s['id'] . '"><button class="btn d">Remove</button></form></td></tr>';
    echo '</table></div>';
}
if ($p === 'alerts') {
    echo '<h1>Job alerts</h1><div class="sc"><table><tr><th>User</th><th>Email</th><th>Keyword</th><th>Location</th><th>Every (days)</th><th>Created</th><th></th></tr>';
    foreach ($d->query('SELECT a.*, u.email, u.first, u.last FROM alerts a JOIN users u ON u.id=a.user_id ORDER BY a.id DESC') as $a)
        echo '<tr><td>' . e($a['first'] . ' ' . $a['last']) . '</td><td>' . e($a['email']) . '</td><td>' . e($a['keyword']) . '</td><td>' . e($a['location']) . '</td><td>' . (int)$a['days'] . '</td><td>' . e($a['created']) . '</td><td><form method="post" class="in">' . field() . '<input type="hidden" name="act" value="send_alert"><input type="hidden" name="id" value="' . $a['id'] . '"><button class="btn">Send matching jobs</button></form> <form method="post" class="in" onsubmit="return confirm(\'Remove this alert?\')">' . field() . '<input type="hidden" name="act" value="del_alert"><input type="hidden" name="id" value="' . $a['id'] . '"><button class="btn d">Remove</button></form></td></tr>';
    echo '</table></div>';
}
if ($p === 'send') {
    echo '<h1>Send update</h1><form method="post" class="box wide">' . field() . '<input type="hidden" name="act" value="send_update">
    <label>Send to</label><select name="audience" style="width:100%;padding:9px;border:1px solid #cfd6e6;border-radius:6px">
    <option value="alerts">People who created a job alert (' . count(emails_for('alerts')) . ')</option>
    <option value="users">All registered users (' . count(emails_for('users')) . ')</option>
    <option value="subscribers">Job update email subscribers (' . count(emails_for('subscribers')) . ')</option>
    <option value="all">Everyone above (' . count(emails_for('all')) . ')</option></select>
    <label>Subject</label><input name="subject"><label>Message</label><textarea name="body" rows="8"></textarea>
    <p><button class="btn" onclick="return confirm(\'Send this message now?\')">Send update</button></p></form>';
    echo '<h2>Sent updates</h2><div class="sc"><table><tr><th>Date</th><th>Subject</th><th>To</th><th>Sent</th><th>Failed</th></tr>';
    foreach ($d->query('SELECT * FROM updates ORDER BY id DESC LIMIT 20') as $u)
        echo '<tr><td>' . e($u['created']) . '</td><td>' . e($u['subject']) . '</td><td>' . e($u['audience']) . '</td><td>' . (int)$u['sent_ok'] . '</td><td>' . (int)$u['sent_fail'] . '</td></tr>';
    echo '</table></div>';
}
if ($p === 'passwords') {
    $q = trim($_GET['q'] ?? '');
    $s = $d->prepare('SELECT * FROM users WHERE email LIKE ? OR first LIKE ? OR last LIKE ? ORDER BY id DESC LIMIT 100'); $s->execute(["%$q%", "%$q%", "%$q%"]);
    echo '<h1>User passwords</h1><p class="note">Passwords are stored scrambled, so nobody can read a current password. Set a new one here, or leave the box empty to generate a random one. The new password is shown once at the top. Send it to the user and ask them to change it on My Profile.</p>';
    echo '<form method="get" class="in"><input type="hidden" name="p" value="passwords"><input name="q" value="' . e($q) . '" placeholder="Search name or email" style="max-width:280px"> <button class="btn g">Search</button></form>';
    echo '<div class="sc" style="margin-top:14px"><table><tr><th>Name</th><th>Email</th><th>Set new password</th></tr>';
    foreach ($s as $u)
        echo '<tr><td>' . e($u['first'] . ' ' . $u['last']) . '</td><td>' . e($u['email']) . '</td><td><form method="post" class="pw">' . field() . '<input type="hidden" name="act" value="reset_pw"><input type="hidden" name="id" value="' . $u['id'] . '"><input name="newpw" placeholder="Type one, or leave empty to generate" autocomplete="off"> <button class="btn">Set password</button></form></td></tr>';
    echo '</table></div>';
}
if ($p === 'site') {
    echo '<h1>Site content</h1><form method="post" class="box wide">' . field() . '<input type="hidden" name="act" value="site">';
    foreach (['hero_title'=>['Home headline',1],'hero_text'=>['Home text',2],'about'=>['Who We Are text',5],'faq'=>['Careers FAQ (one per line: question|answer)',6],'policy'=>['Data privacy consent statement',6]] as $k => [$l, $r])
        echo '<label>' . $l . '</label><textarea name="' . $k . '" rows="' . $r . '">' . e(setting($k)) . '</textarea>';
    echo '<p><button class="btn">Save site content</button></p></form>';
}
if ($p === 'account') {
    $s = $d->prepare('SELECT username FROM admins WHERE id=?'); $s->execute([$_SESSION['admin']]);
    echo '<h1>Account</h1><form method="post" class="box">' . field() . '<input type="hidden" name="act" value="account"><label>Username</label><input name="username" value="' . e($s->fetch()['username']) . '"><label>Current password *</label><input type="password" name="current" required><label>New password (leave empty to keep)</label><input type="password" name="new"><p><button class="btn">Update account</button></p></form>';
}
?></main></body></html>