<?php
require __DIR__ . '/bootstrap.php';
$pageTitle='Basket Digital | Group G05';$activePage='about';
$members=[];$dbError='';
try {$members=db()->query('SELECT * FROM about ORDER BY display_order, id')->fetchAll();}
catch (RuntimeException|PDOException $ex) {$dbError='Team details are unavailable. Start MySQL in XAMPP and refresh this page.';}
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad narrow">
  <p class="eyebrow">Behind the website</p><h1>Meet Group G05</h1>
  <p class="lead">We are a student team building a recruitment website for Basket Digital. This second project adds PHP and MySQL to the website we created in Part 1.</p>
  <section class="card" aria-labelledby="schedule-title"><h2 id="schedule-title">Our class schedule</h2>
   <ul><li>COS10026 Web Technology Project<ul><li>Lecture: Monday, 4:00 PM – 6:00 PM</li><li>Practical: Thursday, 2:00 PM – 4:00 PM</li></ul></li></ul>
  </section>
  <figure class="team-photo"><img src="images/group_photo.jpg" alt="Two Group G05 team members standing together outdoors" width="900" height="500"><figcaption>The Group G05 team behind the Basket Digital website.</figcaption></figure>
  <section class="section-pad" aria-labelledby="team-title"><h2 id="team-title">Member contributions and quotes</h2>
    <?php if ($dbError): ?><div class="alert alert-error" role="alert"><?= e($dbError) ?></div><?php endif; ?>
    <dl class="contribution-list"><?php foreach ($members as $member): ?>
      <div class="card"><dt><?= e($member['student_name']) ?> <span class="student-id">ID: <?= e($member['student_id']) ?></span></dt>
        <dd><strong>Project 1:</strong> <?= e($member['part1_contribution']) ?></dd>
        <dd><strong>Project 2:</strong> <?= e($member['part2_contribution']) ?></dd>
        <dd><strong>Quote:</strong> <span lang="bn"><?= e($member['quote_original']) ?></span> — <em><?= e($member['quote_translation']) ?></em></dd>
      </div>
    <?php endforeach; ?></dl>
  </section>
  <section class="section-pad" aria-labelledby="facts-title"><h2 id="facts-title">Team fun facts</h2>
    <div class="table-scroll"><table class="data-table fun-table"><caption>Hometowns and personal interests</caption><thead><tr><th scope="col">Name</th><th scope="col">Hometown</th><th scope="col">Dream job</th><th scope="col">Coding snack</th></tr></thead>
    <tbody><?php foreach ($members as $member): ?><tr><th scope="row"><?= e($member['student_name']) ?></th><td><?= e($member['hometown']) ?></td><td><?= e($member['dream_job']) ?></td><td><?= e($member['coding_snack']) ?></td></tr><?php endforeach; ?></tbody></table></div>
  </section>
  <section class="card project-notes" aria-labelledby="project-notes-title">
    <h2 id="project-notes-title">Project acknowledgements</h2>
    <p>Our original layout and images were carried over from Project 1. Generative AI tools assisted with some code drafting, troubleshooting and accessibility review. The team is responsible for testing, adapting and explaining its finished work.</p>
  </section>
</main>
<?php require __DIR__ . '/footer.inc'; ?>