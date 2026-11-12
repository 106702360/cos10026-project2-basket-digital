<?php
/** Search endpoint: forwards GET query to the database-backed jobs page. */
require __DIR__ . '/bootstrap.php';
$q=get_text('q');
redirect('jobs.php'.($q!==''?'?q='.rawurlencode(substr($q,0,100)):''));