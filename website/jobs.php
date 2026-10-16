<?php
require __DIR__ . '/inc.php';
require __DIR__ . '/../shared/places.php';
$k = trim($_GET['k'] ?? ''); $l = trim($_GET['l'] ?? '');
$c = trim($_GET['c'] ?? ''); $y = trim($_GET['y'] ?? '');
$n = max(25, min(500, (int)($_GET['n'] ?? 25)));

$where = ' FROM jobs WHERE 1'; $args = [];
if ($k !== '') { $where .= ' AND (title LIKE ? OR dept LIKE ? OR description LIKE ? OR skills LIKE ?)'; array_push($args, "%$k%", "%$k%", "%$k%", "%$k%"); }
if ($l !== '') { $where .= ' AND location LIKE ?'; $args[] = "%$l%"; }
if ($c === 'East Africa (all)') {
    $or = []; foreach (array_keys($EAST) as $x) { $or[] = 'location LIKE ?'; $args[] = "%$x%"; }
    $where .= ' AND (' . implode(' OR ', $or) . ')';
} elseif ($c !== '') { $where .= ' AND location LIKE ?'; $args[] = "%$c%"; }
if ($y !== '') { $where .= ' AND location LIKE ?'; $args[] = "%$y%"; }

$cnt = db()->prepare('SELECT COUNT(*)' . $where); $cnt->execute($args); $total = (int)$cnt->fetchColumn();
$st = db()->prepare('SELECT *' . $where . ' ORDER BY published DESC, id DESC LIMIT ' . $n); $st->execute($args); $rows = $st->fetchAll();

$all = $SITES + $EAST + $OTHER;
$allCities = []; foreach ($all as $list) foreach ($list as $x) $allCities[] = $x;
$allCities = array_values(array_unique($allCities)); sort($allCities);
$eastCities = []; foreach ($EAST as $list) foreach ($list as $x) $eastCities[] = $x;
$eastCities = array_values(array_unique($eastCities)); sort($eastCities);
$map = $all; $map['East Africa (all)'] = $eastCities; $map[''] = $allCities;

$more = 'jobs.php?' . http_build_query(['k'=>$k, 'l'=>$l, 'c'=>$c, 'y'=>$y, 'n'=>$n + 25]);
page_head('Jobs', 'jobs.php');
function opts($arr, $sel) { foreach (array_keys($arr) as $x) echo '<option' . ($x === $sel ? ' selected' : '') . '>' . e($x) . '</option>'; }
?>
<!-- <p class="crumb"><a href="index.php">Home</a> | Open jobs at Savanna Industries</p> -->

<form method="get" action="jobs.php" class="searchband">
  <div>
    <label for="k"><?= e(t('kw')) ?></label>
    <input id="k" name="k" value="<?= e($k) ?>">
  </div>
  <div>
    <label for="l"><?= e(t('loc')) ?></label>
    <input id="l" name="l" value="<?= e($l) ?>">
  </div>
  <div class="act">
    <button class="btn" type="submit"><?= e(t('sj')) ?></button>
    <a href="jobs.php"><?= e(t('clr')) ?></a>
  </div>

  <details class="opts"<?= ($c !== '' || $y !== '') ? ' open' : '' ?>>
    <summary><span class="more">Show More Options</span><span class="fewer">Show Fewer Options</span></summary>
    <div class="panel"><div class="panel-in">
      <div>
        <label for="c">Location</label>
        <select id="c" name="c">
          <option value="">All</option>
          <optgroup label="Sites and offices"><?php opts($SITES, $c); ?></optgroup>
          <optgroup label="East Africa">
            <option<?= $c === 'East Africa (all)' ? ' selected' : '' ?>>East Africa (all)</option>
            <?php opts($EAST, $c); ?>
          </optgroup>
          <optgroup label="Other countries"><?php opts($OTHER, $c); ?></optgroup>
        </select>
      </div>
      <div>
        <label for="y">City</label>
        <select id="y" name="y"><option value="">All</option></select>
      </div>
    </div></div>
  </details>
</form>
<form method="post" action="alert.php" class="alertrow">
  <?= csrf_field() ?>
  <input type="hidden" name="k" value="<?= e($k) ?>"><input type="hidden" name="l" value="<?= e($l) ?>">
  <label for="days">Select how often (in days) to receive an alert:</label>
  <input id="days" name="days" type="number" min="1" max="365" value="7">
  <button class="btn" type="submit"><svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2" fill="none" stroke="#fff" stroke-width="2"/><path d="M3 7l9 7 9-7" fill="none" stroke="#fff" stroke-width="2"/></svg> Create Alert</button>
</form>
<?php if (($_GET['alert'] ?? '') === 'ok') echo '<p class="ok" style="text-align:center">Your job alert has been created.</p>'; ?>
<p class="count">Showing <?= $total ? 1 : 0 ?> to <?= count($rows) ?> of <?= $total ?> Jobs</p>

<?php foreach ($rows as $j): ?>
  <a class="job" href="job.php?id=<?= $j['id'] ?>">
    <h3><?= e($j['title']) ?></h3>
    <div class="meta">
      <div><b>Company</b>Savanna Industries</div>
      <div><b>Department</b><?= e($j['dept']) ?></div>
      <div><b>Location</b><?= e($j['location']) ?></div>
    </div>
  </a>
<?php endforeach; ?>

<?php if (!$rows) echo '<p class="mut">No jobs match your search. Select Clear to see all roles.</p>'; ?>
<?php if ($total > count($rows)): ?>
  <p style="text-align:center;margin:24px 0"><a class="btn" href="<?= e($more) ?>">More Search Results</a></p>
<?php endif; ?>

<script>
var MAP = <?= json_encode($map, JSON_UNESCAPED_UNICODE) ?>;
var chosenCity = <?= json_encode($y, JSON_UNESCAPED_UNICODE) ?>;
var cSel = document.getElementById('c'), ySel = document.getElementById('y');
function fillCities() {
  var list = MAP[cSel.value] || MAP[''];
  ySel.innerHTML = '<option value="">All</option>';
  list.forEach(function (name) {
    var o = document.createElement('option'); o.textContent = name;
    if (name === chosenCity) o.selected = true;
    ySel.appendChild(o);
  });
}
cSel.addEventListener('change', function () { chosenCity = ''; fillCities(); });
fillCities();
</script>
<?php page_foot();