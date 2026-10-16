<?php
require __DIR__ . '/inc.php';
$s = db()->prepare('SELECT * FROM jobs WHERE id=?'); $s->execute([(int)($_GET['id'] ?? 0)]); $j = $s->fetch();
page_head($j ? $j['title'] : 'Job not found', 'jobs.php');
if (!$j) { echo '<div class="sec"><p>This job is no longer available.</p><a class="btn g" href="jobs.php">Back to jobs</a></div>'; page_foot(); exit; }
$u = current_user(); $applied = false;
if ($u) { $a = db()->prepare('SELECT 1 FROM applications WHERE user_id=? AND job_id=?'); $a->execute([$u['id'], $j['id']]); $applied = (bool)$a->fetch(); }
?>
<div class="sec"><a class="mut" href="jobs.php">← All jobs</a>
<h1 style="margin-top:10px"><?= e($j['title']) ?></h1>
<div class="meta mut"><div><b>Company</b>Savanna Industries</div><div><b>Date published</b><?= e($j['published']) ?></div><div><b>Location</b><?= e($j['location']) ?></div><div><b>Department</b><?= e($j['dept']) ?></div></div>
<h2>Job description</h2><p><?= nl2br(e($j['description'])) ?></p>
<h2>Requirements</h2><ul><?php foreach (lines($j['requirements']) as $x) echo '<li>' . e($x) . '</li>'; ?></ul>
<h2>Skills and competencies</h2><ul><?php foreach (lines($j['skills']) as $x) echo '<li>' . e($x) . '</li>'; ?></ul>
<?php if (isset($_GET['done'])) echo '<p class="ok">Application submitted.</p>'; ?>
<form method="post" action="apply.php"><?= csrf_field() ?><input type="hidden" name="job" value="<?= $j['id'] ?>">
<button class="btn" <?= $applied ? 'disabled' : '' ?>><?= $applied ? 'Applied' : e(t('apply')) ?></button></form>
</div>
<?php page_foot();
