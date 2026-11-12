<?php
require __DIR__ . '/bootstrap.php';
if (empty($_SESSION['confirmed_eoi'])) redirect('apply.php');
$number=(int)$_SESSION['confirmed_eoi']; unset($_SESSION['confirmed_eoi']);
$pageTitle='Basket Digital | Application Received';$activePage='apply';
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad narrow">
  <section class="card confirmation" aria-labelledby="confirm-title">
    <p class="eyebrow">Success</p><h1 id="confirm-title">Application received</h1>
    <p>Thank you. Your Expression of Interest has been saved in the local database.</p>
    <p class="eoi-number">Your EOI number: <strong><?= e($number) ?></strong></p>
    <p>The application status is <strong>New</strong>. Keep this reference for your records.</p>
    <a class="button" href="jobs.php">Back to jobs</a>
  </section>
</main>
<?php require __DIR__ . '/footer.inc'; ?>