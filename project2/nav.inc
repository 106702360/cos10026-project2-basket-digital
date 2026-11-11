<nav class="site-nav" aria-label="Main navigation">
  <ul>
    <?php foreach ([
        'index'=>['index.php','Home'],
        'jobs'=>['jobs.php','Jobs'],
        'apply'=>['apply.php','Apply'],
        'about'=>['about.php','Our Team'],
        'login'=>[logged_in() ? 'manage.php' : 'login.php', logged_in() ? 'HR Dashboard' : 'HR Login']
    ] as $key => [$url, $label]): ?>
      <li><a href="<?= e($url) ?>" <?= (($activePage ?? '') === $key || ($key === 'login' && ($activePage ?? '') === 'manage')) ? 'aria-current="page"' : '' ?>><?= e($label) ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>