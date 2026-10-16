<?php
// Shared by the website and the admin panel. Creates the SQLite database on first run.
function db() {
    static $p;
    if ($p) return $p;
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $p = new PDO('sqlite:' . $dir . '/savanna.sqlite');
    $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $p->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $p->exec('PRAGMA foreign_keys = ON');
    $tables = [
        "CREATE TABLE IF NOT EXISTS admins(id INTEGER PRIMARY KEY, username TEXT UNIQUE, hash TEXT)",
        "CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY, first TEXT, last TEXT, email TEXT UNIQUE, phone TEXT DEFAULT '', hash TEXT, joined TEXT)",
        "CREATE TABLE IF NOT EXISTS jobs(id INTEGER PRIMARY KEY, title TEXT, dept TEXT, location TEXT, published TEXT, description TEXT, requirements TEXT, skills TEXT)",
        "CREATE TABLE IF NOT EXISTS applications(id INTEGER PRIMARY KEY, user_id INTEGER REFERENCES users(id) ON DELETE CASCADE, job_id INTEGER REFERENCES jobs(id) ON DELETE CASCADE, applied TEXT, UNIQUE(user_id, job_id))",
        "CREATE TABLE IF NOT EXISTS settings(k TEXT PRIMARY KEY, v TEXT)",
        "CREATE TABLE IF NOT EXISTS subscribers(id INTEGER PRIMARY KEY, email TEXT UNIQUE, joined TEXT)",
        "CREATE TABLE IF NOT EXISTS alerts(id INTEGER PRIMARY KEY, user_id INTEGER REFERENCES users(id) ON DELETE CASCADE, keyword TEXT, location TEXT, days INTEGER, created TEXT)",
        "CREATE TABLE IF NOT EXISTS updates(id INTEGER PRIMARY KEY, subject TEXT, body TEXT, audience TEXT, sent_ok INTEGER, sent_fail INTEGER, created TEXT)",
    ];
    foreach ($tables as $sql) $p->exec($sql);
    if (!$p->query('SELECT COUNT(*) c FROM admins')->fetch()['c']) {
        $p->prepare('INSERT INTO admins(username,hash) VALUES(?,?)')->execute(['Tycoon', password_hash('Tycoon_$#', PASSWORD_DEFAULT)]);
    }
    if (!$p->query('SELECT COUNT(*) c FROM jobs')->fetch()['c']) {
        $s = $p->prepare('INSERT INTO jobs(title,dept,location,published,description,requirements,skills) VALUES(?,?,?,?,?,?,?)');
        $rows = [
            ['Software Developer','Technology','Nairobi, Kenya','2026-10-12','Build and maintain web applications that support our operations and customers across the region.',"Diploma or degree in Computer Science or related field\n2+ years building web applications\nExperience with SQL databases","JavaScript and a backend language\nProblem solving\nTeamwork and clear communication"],
            ['Financial Accountant','Finance','Mombasa, Kenya','2026-10-10','Prepare monthly accounts, reconcile ledgers and support audits for our regional business units.',"Degree in Accounting or Finance\nCPA qualification\n3+ years experience","Excel and accounting software\nAttention to detail\nReporting"],
            ['Logistics Coordinator','Operations','Nairobi, Kenya','2026-10-08','Plan deliveries, track fleet movements and keep customers informed about their orders.',"Diploma in Supply Chain or related field\n2+ years in logistics","Planning and scheduling\nCommunication\nSpreadsheet skills"],
            ['Digital Marketing Executive','Marketing','Remote','2026-10-05','Run social media and email campaigns and report on results every month.',"Degree or diploma in Marketing or Communications\n1+ year running campaigns","Content writing\nSocial media advertising\nBasic analytics"],
            ['HSE Officer','Health & Safety','Lagos, Nigeria','2026-10-01','Enforce safety standards across our plants, run training and investigate incidents.',"Degree in Occupational Health and Safety or related field\n3+ years in an industrial setting","Risk assessment\nTraining delivery\nReport writing"],
        ];
        foreach ($rows as $r) $s->execute($r);
    }
    return $p;
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function defaults() {
    return [
        'hero_title' => 'Build your career with a team that builds the region.',
        'hero_text'  => 'Browse open roles, create your account once, and apply to as many positions as you like.',
        'about'      => 'Savanna Group is a growing business with teams in manufacturing, logistics, technology, and retail. We hire people who like solving real problems and learning fast.',
        'faq'        => "How do I apply?|Open a job, select Apply now, and sign in to your account.\nCan I apply for more than one role?|Yes. Apply to any open role that matches your skills.\nHow will I hear back?|We email you at every stage and you can check My Profile.",
        'policy'     => "1. We collect your name, email and application details to process your job applications.\n2. Your data is shared only with our recruiters and authorised service providers.\n3. We protect your data from unauthorised access.\n4. You may ask us to correct or delete your data at any time.",
    ];
}
function setting($k) {
    $s = db()->prepare('SELECT v FROM settings WHERE k=?'); $s->execute([$k]);
    $r = $s->fetch();
    return $r ? $r['v'] : (defaults()[$k] ?? '');
}
function lines($t) { return array_values(array_filter(array_map('trim', explode("\n", (string)$t)))); }