<?php
require __DIR__ . '/inc.php';
page_head('Investor Relations', 'investors.php');

// ================= EDIT YOUR CONTENT HERE =================
$heroTitle = 'Investor Relations';
$heroText  = 'Partner with us and grow with a business that serves millions.';
$heroImage = 'images/invest.jpg';

$introTitle = 'Why invest with us';
$introParas = [
  'Write your first paragraph about the investment opportunity here.',
  'Write your second paragraph about your growth, strategy or results here.',
];

// 3 facts: [big number or word, label]
$facts = [
  ['30,000+','People employed'],
  ['12','Countries we serve'],
  ['25 years','Of growth'],
];

$band = 'One strong sentence about your commitment to investors goes here.';

// Contact block
$contactTitle = 'Talk to our investor relations team';
$contactText  = 'Email investors@yourcompany.com or call +254 700 000 000.';
// ==========================================================
?>
<div class="ab-hero">
  <div class="ab-text"><h1><?= e($heroTitle) ?></h1><p><?= e($heroText) ?></p></div>
  <img class="ab-side" src="<?= e($heroImage) ?>" alt="">
</div>

<div class="ab-join">
  <h2><?= e($introTitle) ?></h2>
  <?php foreach ($introParas as $p) echo '<p>' . e($p) . '</p>'; ?>
</div>

<div class="facts">
  <?php foreach ($facts as $f): ?>
    <div><b><?= e($f[0]) ?></b><span><?= e($f[1]) ?></span></div>
  <?php endforeach; ?>
</div>

<div class="ab-band"><p><?= e($band) ?></p></div>

<div class="ab-join">
  <h2><?= e($contactTitle) ?></h2>
  <p><?= e($contactText) ?></p>
</div>
<?php page_foot();