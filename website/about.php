<?php
require __DIR__ . '/inc.php';
page_head('Who We Are', 'about.php');

// ================= EDIT YOUR CONTENT HERE =================
$heroTitle = 'Who we are';
$heroText  = 'We are a leading conglomerate employing over 30,000 people across Africa and meeting the needs of millions.';

// 10 tiles: [label, image file]
$tiles = [
  ['Manufacturing','images/tile1.jpg'], ['Logistics','images/tile2.jpg'], ['Technology','images/tile3.jpg'],
  ['Retail','images/tile4.jpg'],        ['Packaging','images/tile5.jpg'],
  ['Warehousing','images/tile6.jpg'],   ['Fleet','images/tile7.jpg'],
  ['Foundation','images/tile8.jpg'],    ['Property','images/tile9.jpg'], ['Energy','images/tile10.jpg'],
];

$splitImage = 'images/abt.jpg.png';
$splitTitle = 'We do things differently';
$splitText  = 'Write a short paragraph here about how your company works and what it believes in.';

$joinTitle = 'Join the Winning Team';
$joinParas = [
  'We are thrilled that you are considering joining our team. Our mission is to touch the lives of people by providing their basic needs. Working with the Dangote Group is more than just a career, it is meaningful work that improves the lives of millions of people.',
  'We believe in fostering an enabling, inclusive, and collaborative work environment where talents are enhanced and valued. At Dangote, our vision is to become the leading provider of essential daily needs in sub-Saharan Africa.',
  'Explore the available job opportunities and discover how you can contribute your skills towards the actualisation of this vision. We look forward to meeting you and the possibility of you becoming a part of the Dangote family.',
];

$band = 'We are at the forefront of African development. We\'re not just building businesses; we\'re building a sustainable future for the continent. Learn about our sustainability initiatives, and discover the potential of investing with Dangote Industries.';

// 3 cards: [title, text, image, link]
$cards = [
  ['Our Businesses','Dangote Group continues to grow its vision of becoming the leading provider of essential needs in Africa','images/bsn.jpg.png','jobs.php'],
  ['Sustainability','We are all about creating ventures that serves the interest of stakeholders, communities and the people concerned','images/sus.jpg.png','jobs.php'],
   ['Investor Relations','By being part of the Dangote brand, you\'re putting yourself on the path of profit','images/car.jpg.png','investors.php'],
];
   // ==========================================================
?>
<div class="ab-hero full">
  <div class="ab-text"><h1><?= e($heroTitle) ?></h1><p><?= e($heroText) ?></p></div>
   <img class="ab-side" src="images/who.jpg.png" alt="Our businesses">
</div>

<div class="ab-split">
  <div class="ab-pic" style="background-image:url('<?= e($splitImage) ?>')"></div>
  <div class="ab-copy"><h2><?= e($splitTitle) ?></h2><p><?= nl2br(e($splitText)) ?></p></div>
</div>

<div class="ab-join">
  <h2><?= e($joinTitle) ?></h2>
  <?php foreach ($joinParas as $p) echo '<p>' . e($p) . '</p>'; ?>
</div>

<div class="ab-band"><p><?= e($band) ?></p></div>

<div class="ab-cards">
  <?php foreach ($cards as $c): ?>
    <div class="ab-card">
      <div class="ab-cardpic" style="background-image:url('<?= e($c[2]) ?>')"></div>
      <h3><?= e($c[0]) ?></h3><p><?= e($c[1]) ?></p>
      <a href="<?= e($c[3]) ?>">Read more</a>
    </div>
  <?php endforeach; ?>
</div>
<!-- <div class="dd dd-right">
  <!-- Dropdown toggle and menu -->
<!-- </div> --> -->

<?php page_foot();

