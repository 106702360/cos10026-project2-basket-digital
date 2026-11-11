<?php
require __DIR__ . '/bootstrap.php';
$pageTitle = 'Basket Digital | Digital Retail Careers';
$activePage = 'index';
require __DIR__ . '/header.inc';
?>
<main id="main-content">
  <section class="hero container" aria-labelledby="hero-title">
    <div class="hero-copy">
      <p class="eyebrow">Careers in digital retail</p>
      <h1 id="hero-title">Build better shopping experiences with us.</h1>
      <p class="lead">Join Basket Digital, an e-commerce technology team connecting talented developers, designers, and digital specialists with meaningful work.</p>
      <div class="actions"><a class="button" href="jobs.php">Browse open roles</a><a class="button button-outline" href="about.php">Meet the team</a></div>
    </div>
    <img class="hero-photo" src="images/home_image.jpg" alt="Digital team working together at computer screens" width="1100" height="660">
  </section>
  <section class="container section-pad" aria-labelledby="find-title">
    <div class="card search-card">
      <div><p class="eyebrow">Find your next opportunity</p><h2 id="find-title">Search jobs</h2></div>
      <form class="search-form" action="jobs.php" method="get" role="search">
        <label for="q">Job title, keyword or reference</label>
        <div class="search-row"><input type="search" id="q" name="q" placeholder="e.g. frontend, FE902"><button type="submit">Search jobs</button></div>
      </form>
    </div>
  </section>
  <section class="container section-pad" aria-labelledby="departments-title">
    <p class="eyebrow">Where we work</p><h2 id="departments-title">Departments hiring</h2>
    <div class="table-scroll"><table class="data-table"><caption>Digital retail hiring focus areas</caption>
      <thead><tr><th scope="col">Department</th><th colspan="2" scope="colgroup">Focus areas and recruitment status</th></tr></thead>
      <tbody><tr><th scope="row" rowspan="2">User Experience</th><td>Accessible storefront design</td><td>Recruiting</td></tr>
      <tr><td>Product discovery and layout</td><td>Recruiting</td></tr>
      <tr><th scope="row">Web Development</th><td>Customer-facing applications</td><td>Recruiting</td></tr></tbody></table></div>
  </section>
  <section class="container section-pad mission-grid" aria-labelledby="purpose-title">
    <div class="card"><p class="eyebrow">Our vision</p><h2 id="purpose-title">Commerce for everyone</h2><p>Make digital shopping simple, inclusive and reliable, whatever device or ability a customer has.</p></div>
    <div class="card"><p class="eyebrow">Our mission</p><h2>People create progress</h2><p>Bring thoughtful technology professionals together to build accessible, secure and useful e-commerce services.</p></div>
  </section>
</main>
<?php require __DIR__ . '/footer.inc'; ?>
