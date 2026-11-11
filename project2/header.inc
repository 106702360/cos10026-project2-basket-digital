<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore jobs at Basket Digital. Discover retail technology roles, submit an application and meet our student development team.">
    <meta name="author" content="COS10026 Group G05">
    <title><?= e($pageTitle ?? 'Basket Digital | Careers') ?></title>
    <meta name="theme-color" content="#102b3c">
    <link rel="stylesheet" href="styles/style.css?v=20261110">
    <style>
        /* A small page-level style makes the current navigation item easier to identify. */
        .site-nav a[aria-current="page"] { text-decoration: underline; text-underline-offset: .3em; }
    </style>
</head>
<body class="page-<?= e($activePage ?? 'other') ?>">
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="index.php" aria-label="Basket Digital - Home">
            <img src="images/logo.png" width="58" height="58" alt="" style="object-fit: contain;">
            <span><strong>Basket Digital</strong><small>Retail careers, made human.</small></span>
        </a>
        <?php require __DIR__ . '/nav.inc'; ?>
    </div>
</header>