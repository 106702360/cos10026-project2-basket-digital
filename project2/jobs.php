<?php
require __DIR__ . '/bootstrap.php';
$pageTitle = 'Basket Digital | Job Vacancies';
$activePage = 'jobs';
$q = get_text('q');
$jobs = []; $dbError = '';
try {
$pdo = db();
if ($q !== '') {
$stmt = $pdo->prepare('SELECT * FROM jobs WHERE reference LIKE :q1 OR title LIKE :q2 OR description LIKE :q3 OR responsibilities LIKE :q4 OR essential_requirements LIKE :q5 ORDER BY title');
$search = '%' . mb_safe_substr($q, 0, 100) . '%';
$stmt->execute(['q1'=>$search,'q2'=>$search,'q3'=>$search,'q4'=>$search,'q5'=>$search]);
$jobs = $stmt->fetchAll();
} else {
$jobs = $pdo->query('SELECT * FROM jobs ORDER BY title')->fetchAll();
}
} catch (RuntimeException|PDOException $ex) { $dbError = 'Jobs could not be loaded. Start MySQL in XAMPP and refresh this page.'; }
function mb_safe_substr(string $text, int $start, int $length): string { return function_exists('mb_substr') ? mb_substr($text,$start,$length) : substr($text,$start,$length); }
function detail_list($stored, string $type): void {
$items = json_decode((string)$stored, true);
if (!is_array($items)) $items=[];
echo '<' . $type . '>';
foreach ($items as $item) echo '<li>' . e($item) . '</li>';
echo '</' . $type . '>';
}
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad">
<p class="eyebrow">Career opportunities</p><h1>Find work worth doing</h1>
<p class="lead">Explore roles on our retail technology team. Job details and search results are loaded from our database.</p>
<form class="search-form card" method="get" action="jobs.php" role="search">
<label for="q">Search by title, job reference or keyword</label>
<div class="search-row"><input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Try FE902 or design"><button type="submit">Search</button><a class="button button-outline" href="jobs.php">Clear</a></div>
</form>
<?php if ($dbError): ?><div class="alert alert-error" role="alert"><?= e($dbError) ?></div><?php endif; ?>
<div class="jobs-layout">
<div class="jobs-content">
<?php if (!$jobs && !$dbError): ?><div class="card"><h2>No matching roles</h2><p>Try another keyword or <a href="jobs.php">view all jobs</a>.</p></div><?php endif; ?>
<?php foreach ($jobs as $job): ?>
<section class="card job-card" aria-labelledby="job-<?= e($job['reference']) ?>">
<p class="job-ref">Reference <?= e($job['reference']) ?></p>
<h2 id="job-<?= e($job['reference']) ?>"><?= e($job['title']) ?></h2>
<p><?= e($job['description']) ?></p>
<?php if (!empty($job['image_path'])): ?><img class="job-image" src="<?= e($job['image_path']) ?>" alt="<?= e($job['image_alt']) ?>" loading="lazy" width="800" height="420"><?php endif; ?>
<dl class="job-facts"><div><dt>Salary</dt><dd><?= e($job['salary']) ?></dd></div><div><dt>Reports to</dt><dd><?= e($job['reporting_to']) ?></dd></div></dl>
<h3>Key responsibilities</h3><?php detail_list($job['responsibilities'],'ul'); ?>
<h3>Essential requirements</h3><?php detail_list($job['essential_requirements'],'ol'); ?>
<h3>Preferred requirements</h3><?php detail_list($job['preferable_requirements'],'ul'); ?>
<?php if (!empty($job['salary_source_url'])): ?><p class="source">Salary research: <a target="_blank" rel="noopener noreferrer" href="<?= e($job['salary_source_url']) ?>"><?= e($job['salary_source_name']) ?></a></p><?php endif; ?>
<a class="button" href="apply.php?ref=<?= rawurlencode($job['reference']) ?>">Apply for <?= e($job['reference']) ?></a>
</section>
<?php endforeach; ?>
</div>
<aside class="jobs-sidebar card" aria-label="Career support information">
<h2>Build something useful</h2>
<p>Work with people who care about accessible design, clear communication and reliable online shopping experiences.</p>
<h2>Application steps</h2>
<ol><li>Choose a suitable role</li><li>Submit your application</li><li>Attend an interview</li><li>Discuss the next steps</li></ol>
</aside>
</div>
</main>
<?php require __DIR__ . '/footer.inc'; ?>