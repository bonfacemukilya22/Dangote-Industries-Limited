<?php
require __DIR__ . '/../shared/db.php';
session_name('savanna_site');
session_start();
$L = $_SESSION['lang'] ?? 'en';
$T = [
 'en'=>['why'=>'Why Choose Us','home'=>'Home','jobs'=>'Jobs','about'=>'Who We Are','faq'=>'Careers FAQ','prof'=>'My Profile','vp'=>'View profile','out'=>'Sign out','kw'=>'Search by keyword','loc'=>'Search by location','sj'=>'Search jobs','clr'=>'Clear','apply'=>'Apply now'],
 'fr'=>['why'=>'Pourquoi nous choisir','home'=>'Accueil','jobs'=>'Emplois','about'=>'Qui sommes-nous','faq'=>'FAQ carrières','prof'=>'Mon profil','vp'=>'Voir le profil','out'=>'Déconnexion','kw'=>'Recherche par mot-clé','loc'=>'Recherche par lieu','sj'=>'Chercher','clr'=>'Effacer','apply'=>'Postuler'],
 'sw'=>['why'=>'Kwa Nini Sisi','home'=>'Mwanzo','jobs'=>'Kazi','about'=>'Sisi ni Nani','faq'=>'Maswali','prof'=>'Wasifu Wangu','vp'=>'Tazama wasifu','out'=>'Toka','kw'=>'Tafuta kwa neno','loc'=>'Tafuta kwa eneo','sj'=>'Tafuta kazi','clr'=>'Futa','apply'=>'Omba sasa'],
];
function t($k) { global $T, $L; return $T[$L][$k] ?? $T['en'][$k] ?? $k; }
function current_user() {
    if (empty($_SESSION['uid'])) return null;
    $s = db()->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['uid']]);
    return $s->fetch() ?: null;
}
function csrf() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function check_csrf() { if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) { http_response_code(400); die('Invalid request. Go back and try again.'); } }
function captcha_q() { $a = random_int(2, 9); $b = random_int(2, 9); $_SESSION['cap'] = $a + $b; return "$a + $b"; }
function captcha_ok() { $ok = isset($_SESSION['cap']) && (int)($_POST['cap'] ?? -1) === $_SESSION['cap']; unset($_SESSION['cap']); return $ok; }
function go($url) { header('Location: ' . $url); exit; }
function art() { return '<svg viewBox="0 0 1200 460" role="img" aria-label="Illustration of five colleagues around a laptop"><rect width="1200" height="460" fill="#dfe8ff"/><rect y="370" width="1200" height="90" fill="#f4f6fb"/><rect x="450" y="340" width="300" height="40" rx="6" fill="#aab3c7"/><g><rect x="110" y="190" width="170" height="260" rx="60" fill="#12264f"/><circle cx="195" cy="125" r="55" fill="#7a4a30"/><path d="M140 120 Q195 45 250 120 Q245 170 258 220 L236 150 Q195 100 154 150 L132 220 Q145 170 140 120Z" fill="#1b1410"/></g><g><rect x="290" y="210" width="170" height="240" rx="60" fill="#3a2a2a"/><circle cx="375" cy="145" r="52" fill="#8b5a3c"/><path d="M325 138 Q375 82 425 138 Q415 110 375 100 Q335 110 325 138Z" fill="#6b4423"/></g><g><rect x="470" y="225" width="260" height="225" rx="70" fill="#6f7580"/><path d="M570 225 L600 300 L630 225Z" fill="#fff"/><circle cx="600" cy="165" r="60" fill="#5e3a26"/><path d="M547 155 Q600 90 653 155 Q648 128 600 120 Q552 128 547 155Z" fill="#150f0c"/></g><g><rect x="745" y="205" width="190" height="245" rx="60" fill="#a8c8f0"/><circle cx="840" cy="140" r="54" fill="#4e3020"/><rect x="802" y="132" width="32" height="17" rx="8" fill="none" stroke="#14213d" stroke-width="4"/><rect x="846" y="132" width="32" height="17" rx="8" fill="none" stroke="#14213d" stroke-width="4"/></g><g><rect x="950" y="200" width="170" height="250" rx="60" fill="#12264f"/><circle cx="1035" cy="135" r="52" fill="#6a4029"/><path d="M984 130 Q1035 70 1086 130 Q1078 98 1035 92 Q992 98 984 130Z" fill="#1b1410"/></g></svg>'; }
function page_head($title, $active = '') {
    global $L;
    $u = current_user();
    $nav = ['index.php'=>'home','about.php'=>'about','why.php'=>'why','faq.php'=>'faq','profile.php'=>'prof'];
    echo '<!DOCTYPE html><html lang="' . e($L) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . ' | Savanna Group</title><link rel="stylesheet" href="style.css?v=' . filemtime(__DIR__ . '/style.css') . '"></head><body>';
    echo '<header><div class="w top"><a class="logo" href="index.php"><svg width="42" height="42" viewBox="0 0 44 44" aria-hidden="true"><circle cx="22" cy="22" r="22" fill="#0f2a6b"/><path d="M8 28 Q22 12 36 28" stroke="#e8590c" stroke-width="4" fill="none" stroke-linecap="round"/><circle cx="22" cy="17" r="4" fill="#fff"/></svg>DANGOTE</a><nav aria-label="Main">';
    foreach ($nav as $f => $k) {
    if ($f === 'about.php') echo '<div class="dd"><button type="button" onclick="this.parentNode.classList.toggle(\'open\')">' . e(t('jobs')) . ' <span class="caret"></span></button><div class="menu"><a href="jobs.php">View All Jobs</a></div></div>';
    echo '<a href="' . $f . '"' . ($active === $f ? ' class="on"' : '') . '>' . e(t($k)) . '</a>';
}
    echo '</nav></div></header><div class="bar"><div class="w"><form method="get" action="lang.php"><input type="hidden" name="back" value="' . e(basename($_SERVER['SCRIPT_NAME']) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '')) . '"><select name="l" aria-label="Language" onchange="this.form.submit()">';
    foreach (['en'=>'English','fr'=>'Français','sw'=>'Kiswahili'] as $c => $n) echo '<option value="' . $c . '"' . ($L === $c ? ' selected' : '') . '>' . $n . '</option>';
    echo '</select><noscript><button>OK</button></noscript></form>';
    echo $u ? '<a href="logout.php">' . e($u['first']) . ' · ' . e(t('out')) . '</a>' : '<a href="signin.php">' . e(t('vp')) . '</a>';
    echo '</div></div><main class="w">';
}
function page_foot() {
        $footerText = 'Dangote Group is a conglomerate, operating across various industries including Cement, Sugar, Salt, Fertilizer, Oil and Gas and more';
    // PUT YOUR SOCIAL LINKS HERE (replace the # with your page address)
    $social = [
        'Facebook'  => 'https://www.facebook.com/dangotegroup?_rdc=1&_rdr',
        'X'         => 'https://x.com/DangoteGroup',
        'Instagram' => 'https://www.instagram.com/dangotegroup/',
        'YouTube'   => 'https://www.youtube.com/c/DangoteGroup',
        'LinkedIn'  => 'https://www.linkedin.com/company/dangotegroup/',
    ];
    $icons = [
        'Facebook'  => '<circle cx="12" cy="12" r="12" fill="currentColor"/><path fill="#fff" d="M13.5 21v-7h2.4l.4-3h-2.8V9.300c0-.9.3-1.400 1.500-1.400h1.400V5.200c-.3 0-1.100-.1-2.100-.1-2.100 0-3.500 1.300-3.500 3.600V11H8.400v3h2.400v7z"/>',
        'X'         => '<path fill="currentColor" d="M18.200 3h2.900l-6.300 7.200L22 21h-5.800l-4.500-5.900L6.500 21H3.600l6.700-7.700L3 3h5.900l4.100 5.400zm-1 16.300h1.600L7 4.600H5.300z"/>',
        'Instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.200" cy="6.800" r="1.200" fill="currentColor"/>',
        'YouTube'   => '<rect x="2" y="5" width="20" height="14" rx="4" fill="currentColor"/><path fill="#fff" d="M10 9l5 3-5 3z"/>',
        'LinkedIn'  => '<rect x="2" y="2" width="20" height="20" rx="3" fill="currentColor"/><circle cx="7.500" cy="7.800" r="1.600" fill="#fff"/><rect x="6" y="10.500" width="3" height="7.500" fill="#fff"/><path fill="#fff" d="M11 10.500h2.800v1.200c.5-.9 1.600-1.400 2.800-1.400 2.300 0 3 1.400 3 3.600V18h-3v-4c0-1-.3-1.700-1.300-1.700S14 13 14 14v4h-3z"/>',
    ];
    echo '</main><div class="social w">';
    foreach ($social as $name => $url)
        echo '<a href="' . e($url) . '" aria-label="' . e($name) . '" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" width="40" height="40" aria-hidden="true">' . $icons[$name] . '</svg></a>';
    // echo '</div><footer><div class="w">© ' . date('Y') . ' Savanna Group. All rights reserved.</div></footer></body></html>';
        echo '</div><div class="foot-text w"><p>' . e($footerText) . '</p></div></body></html>';
}
// function page_foot() { echo '</main><footer><div class="w">© ' . date('Y') . ' Savanna Group. All rights reserved.</div></footer></body></html>'; }
function strong_password($p) { return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $p); }
