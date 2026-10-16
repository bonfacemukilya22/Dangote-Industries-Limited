<?php
require __DIR__ . '/inc.php';
require __DIR__ . '/../shared/places.php';

// ================= EDIT YOUR CONTENT HERE =================
$bannerImage = 'images/index.jpg.png';   // your existing banner picture

// 3 cards: [title, text, image, link]
$cards = [
  ['Who We Are','We believe in fostering an enabling, inclusive, and collaborative work environment where talents are enhanced and valued','images/wwa.jpg.png','about.php'],
  ['Why Choose Us','Meeting the basic needs of people across the world is at the core of what we do at the Dangote Group','images/wcu.jpg.png','why.php'],
  ['Diversity & Inclusion','We strive to create an inclusive culture where all employees feel heard, supported, and included ','images/di.jpg.png','about.php'],
];

$learnImage = 'images/learn.jpg.png';
$learnTitle = 'Learning with the Dangote Academy';
$learnText  = 'We have several collaborations and partnerships, that help us achieve world-class standards in learning and development';
$learnLink  = 'why.php';

$updTitle = 'Get Job Updates';
$updText  = 'Join our network and stay updated on upcoming events and opportunities by providing your email address.';
$updImage = 'images/background.jpg.png';
// ==========================================================

$latest = db()->query('SELECT * FROM jobs ORDER BY published DESC, id DESC LIMIT 3')->fetchAll();

// Map pins: one pin per city, with the number of jobs there
$names = array_keys($COORDS); usort($names, function ($a, $b) { return strlen($b) - strlen($a); });
$found = [];
foreach (db()->query('SELECT id,title,location FROM jobs')->fetchAll() as $j)
    foreach ($names as $city) if (stripos($j['location'], $city) !== false) { $found[$city][] = ['id'=>$j['id'], 'title'=>$j['title']]; break; }
$pins = [];
foreach ($found as $city => $list) $pins[] = ['city'=>$city, 'lat'=>$COORDS[$city][0], 'lng'=>$COORDS[$city][1], 'n'=>count($list), 'jobs'=>array_slice($list, 0, 5)];

page_head('Home', 'index.php');
?>
<form method="get" action="jobs.php" class="searchband">
  <div><label for="k"><?= e(t('kw')) ?></label><input id="k" name="k"></div>
  <div><label for="l"><?= e(t('loc')) ?></label><input id="l" name="l"></div>
  <div class="act"><button class="btn" type="submit"><?= e(t('sj')) ?></button><a href="jobs.php"><?= e(t('clr')) ?></a></div>
</form>

<div class="banner">
  <div class="banner-text">
    <h1><?= e(setting('hero_title')) ?></h1>
    <p><?= e(setting('hero_text')) ?></p>
  </div>
  <img src="<?= e($bannerImage) ?>" alt="Our team at work">
</div>

<div class="ab-cards">
  <?php foreach ($cards as $c): ?>
    <div class="ab-card">
      <div class="ab-cardpic" style="background-image:url('<?= e($c[2]) ?>')"></div>
      <h3><?= e($c[0]) ?></h3><p><?= e($c[1]) ?></p>
      <a href="<?= e($c[3]) ?>">Read more...</a>
    </div>
  <?php endforeach; ?>
</div>

<div class="learn">
  <div class="learn-pic" style="background-image:url('<?= e($learnImage) ?>')"></div>
  <div><h2><?= e($learnTitle) ?></h2><p><?= e($learnText) ?></p><a href="<?= e($learnLink) ?>">Find out more</a></div>
</div>


<div class="viewband"><a class="btn" href="jobs.php">View All Jobs</a></div>

<div class="updates" id="updates" style="background-image:url('<?= e($updImage) ?>')">
  <form method="post" action="subscribe.php" class="upbox">
    <?= csrf_field() ?>
    <h2><?= e($updTitle) ?></h2>
    <p><?= e($updText) ?></p>
    <?php if (($_GET['sub'] ?? '') === 'ok') echo '<p class="subok">Thank you. You are on the list.</p>';
          if (($_GET['sub'] ?? '') === 'bad') echo '<p class="subbad">Please enter a valid email address.</p>'; ?>
    <label class="sr" for="subemail">Email address</label>
    <input id="subemail" name="email" type="email" placeholder="Enter Email Address" required>
    <button class="btn" type="submit">Keep Me Updated</button>
  </form>
</div>

<div class="mapwrap">
  <form method="get" action="jobs.php" class="mapsearch">
    <input name="k" placeholder="Search jobs" aria-label="Search jobs">
    <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" width="20" height="20"><circle cx="10" cy="10" r="6.5" fill="none" stroke="#fff" stroke-width="2.5"/><path d="M15 15l6 6" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg></button>
  </form>
  <div id="jobmap"></div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
var PINS = <?= json_encode($pins, JSON_UNESCAPED_UNICODE) ?>;
var map = L.map('jobmap', {scrollWheelZoom: false, zoomControl: false, minZoom: 2, worldCopyJump: true}).setView([8, 22], 3);
L.control.zoom({position: 'bottomright'}).addTo(map);
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {maxZoom: 16, attribution: 'Tiles &copy; Esri'}).addTo(map);
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Reference/MapServer/tile/{z}/{y}/{x}', {maxZoom: 16}).addTo(map);
// L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {subdomains: 'abcd', maxZoom: 18, attribution: '&copy; OpenStreetMap contributors &copy; CARTO'}).addTo(map);
// var map = L.map('jobmap', {scrollWheelZoom: false, minZoom: 2, worldCopyJump: true}).setView([8, 22], 3);
// L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18, attribution: '&copy; OpenStreetMap contributors'}).addTo(map);
PINS.forEach(function (p) {
  var icon = L.divIcon({className: 'pin', iconSize: [0, 0], iconAnchor: [0, 0],
    html: '<span class="pin-n">' + p.n + (p.n === 1 ? ' Job' : ' Jobs') + '</span><span class="pin-i"></span>'});
  var box = document.createElement('div');
  var h = document.createElement('b'); h.textContent = p.city; box.appendChild(h);
  p.jobs.forEach(function (j) {
    var a = document.createElement('a'); a.href = 'job.php?id=' + j.id; a.textContent = j.title; a.style.display = 'block'; box.appendChild(a);
  });
  var all = document.createElement('a'); all.href = 'jobs.php?y=' + encodeURIComponent(p.city);
  all.textContent = 'See all ' + p.n + (p.n === 1 ? ' job' : ' jobs'); all.style.cssText = 'display:block;margin-top:6px;font-weight:700'; box.appendChild(all);
  L.marker([p.lat, p.lng], {icon: icon}).addTo(map).bindPopup(box);
});
map.on('click', function () { map.scrollWheelZoom.enable(); });
</script>
<?php page_foot();