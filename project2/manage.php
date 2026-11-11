<?php
require __DIR__ . '/bootstrap.php';
require_login(); // Must run before rendering any HTML.
$pageTitle='Basket Digital | HR Dashboard';$activePage='manage';
$dbError='';$records=[];$filterRef=get_text('job_ref');
$first=get_text('first_name');$last=get_text('last_name');
$sort=get_text('sort') ?: 'EOInumber';$dir=strtoupper(get_text('dir') ?: 'DESC');
$allowedSort=['EOInumber'=>'EOInumber','job_ref'=>'job_ref','first_name'=>'first_name','last_name'=>'last_name','status'=>'status','created_at'=>'created_at'];
if (!isset($allowedSort[$sort])) $sort='EOInumber';
if (!in_array($dir,['ASC','DESC'],true)) $dir='DESC';
try {
    $pdo=db();create_eoi_if_missing($pdo);
    if (($_SERVER['REQUEST_METHOD']??'')==='POST') {
       if (!csrf_valid($_POST['csrf']??null)) {
           flash('error','Invalid session token. Refresh the page and try again.');
           redirect('manage.php');
       }
       $action=post_text('action');
       if ($action==='update_status') {
          $eoiNo=filter_var(post_text('eoi_number'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
          $status=post_text('status');
          if (!$eoiNo || !in_array($status,['New','Current','Final'],true)) flash('error','Invalid EOI number or status.');
          else {
             $check = $pdo->prepare('SELECT status FROM eoi WHERE EOInumber = :number');
             $check->execute(['number' => $eoiNo]);
             $currentStatus = $check->fetchColumn();
             if ($currentStatus === false) {
                 flash('error', 'No matching EOI number found.');
             } elseif ($currentStatus === $status) {
                 flash('success', 'This EOI already has the selected status.');
             } else {
                 $stmt = $pdo->prepare('UPDATE eoi SET status = :status WHERE EOInumber = :number');
                 $stmt->execute(['status' => $status, 'number' => $eoiNo]);
                 flash('success', 'EOI status updated.');
             }
          }
       } elseif ($action==='delete_by_ref') {
          $ref=strtoupper(post_text('delete_ref'));
          if (!preg_match('/^[A-Z0-9]{5}$/D',$ref) || (string)($_POST['confirm_delete']??'')!=='yes') {
             flash('error','Choose a valid job reference and tick the confirmation checkbox before deleting.');
          } else {
             $stmt=$pdo->prepare('DELETE FROM eoi WHERE job_ref = :ref');$stmt->execute(['ref'=>$ref]);
             flash('success',$stmt->rowCount().' application(s) deleted for '.$ref.'.');
          }
       } else flash('error','Unknown management action.');
       redirect('manage.php');
    }
    $where=[];$params=[];
    if ($filterRef!=='') {$where[]='job_ref = :ref';$params['ref']=$filterRef;}
    if ($first!=='') {$where[]='first_name LIKE :first';$params['first']='%'.$first.'%';}
    if ($last!=='') {$where[]='last_name LIKE :last';$params['last']='%'.$last.'%';}
    $sql='SELECT * FROM eoi'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY '.$allowedSort[$sort].' '.$dir;
    $stmt=$pdo->prepare($sql);$stmt->execute($params);$records=$stmt->fetchAll();
    $jobRefs=$pdo->query('SELECT reference, title FROM jobs ORDER BY reference')->fetchAll();
} catch (RuntimeException|PDOException $ex) { $dbError='Database unavailable. Check that MySQL is running in XAMPP.';$jobRefs=[]; }
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad">
  <div class="dashboard-heading"><div><p class="eyebrow">Restricted HR area</p><h1>Application management</h1><p>Signed in as <strong><?= e($_SESSION['hr_username'] ?? 'manager') ?></strong>.</p></div>
    <form method="post" action="logout.php"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button class="button button-outline" type="submit">Log out</button></form>
  </div>
  <?php render_flash(); ?>
  <?php if ($dbError): ?><div class="alert alert-error" role="alert"><?= e($dbError) ?></div><?php endif; ?>
  <section class="card" aria-labelledby="filter-title"><h2 id="filter-title">Find and sort EOIs</h2>
    <form class="filter-grid" method="get" action="manage.php">
      <div class="form-group"><label for="job_ref">Job reference</label><input name="job_ref" id="job_ref" maxlength="5" value="<?= e($filterRef) ?>" placeholder="e.g. FE902"></div>
      <div class="form-group"><label for="first_name">First name contains</label><input name="first_name" id="first_name" value="<?= e($first) ?>"></div>
      <div class="form-group"><label for="last_name">Last name contains</label><input name="last_name" id="last_name" value="<?= e($last) ?>"></div>
      <div class="form-group"><label for="sort">Sort by</label><select name="sort" id="sort">
        <?php foreach (['EOInumber'=>'EOI number','job_ref'=>'Job reference','first_name'=>'First name','last_name'=>'Last name','status'=>'Status','created_at'=>'Date submitted'] as $field=>$name): ?>
           <option value="<?= e($field) ?>" <?= $sort===$field?'selected':'' ?>><?= e($name) ?></option>
        <?php endforeach; ?></select></div>
      <div class="form-group"><label for="dir">Order</label><select name="dir" id="dir"><option value="ASC" <?= $dir==='ASC'?'selected':'' ?>>Ascending</option><option value="DESC" <?= $dir==='DESC'?'selected':'' ?>>Descending</option></select></div>
      <div class="form-group filter-actions"><button type="submit">Search and sort</button><a class="button button-outline" href="manage.php">List all EOIs</a></div>
    </form>
  </section>
  <section class="section-pad" aria-labelledby="results-title"><h2 id="results-title">Application results (<?= count($records) ?>)</h2>
    <?php if (!$records && !$dbError): ?><div class="card">No matching applications were found.</div><?php endif; ?>
    <?php if ($records): ?><div class="table-scroll"><table class="data-table management-table"><caption>Saved application records matching the selected filters</caption>
      <thead><tr><th scope="col">EOI #</th><th scope="col">Job</th><th scope="col">Name</th><th scope="col">DOB</th><th scope="col">Gender</th><th scope="col">Address</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Skills</th><th scope="col">Other skills</th><th scope="col">Submitted</th><th scope="col">Status / change</th></tr></thead>
      <tbody><?php foreach ($records as $record): ?>
        <tr><th scope="row"><?= e($record['EOInumber']) ?></th><td><?= e($record['job_ref']) ?></td>
        <td><?= e($record['first_name'].' '.$record['last_name']) ?></td><td><?= e(date('d/m/Y',strtotime($record['dob']))) ?></td><td><?= e($record['gender']) ?></td>
        <td><?= e($record['street'].', '.$record['suburb'].', '.$record['state'].' '.$record['postcode']) ?></td>
        <td><a href="mailto:<?= e($record['email']) ?>"><?= e($record['email']) ?></a></td><td><?= e($record['phone']) ?></td>
        <td><?= e(implode(', ',json_decode($record['skills'],true)?:[])) ?></td><td><?= e($record['other_skills']) ?></td><td><?= e($record['created_at']) ?></td>
        <td><form method="post" action="manage.php" class="status-form">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update_status"><input type="hidden" name="eoi_number" value="<?= e($record['EOInumber']) ?>">
          <label for="status-<?= e($record['EOInumber']) ?>">Status</label><select id="status-<?= e($record['EOInumber']) ?>" name="status">
           <?php foreach (['New','Current','Final'] as $status): ?><option value="<?= $status ?>" <?= $record['status']===$status?'selected':'' ?>><?= $status ?></option><?php endforeach; ?>
          </select><button type="submit">Update</button></form></td></tr>
      <?php endforeach; ?></tbody></table></div><?php endif; ?>
  </section>
  <section class="card danger-zone" aria-labelledby="delete-title"><h2 id="delete-title">Delete EOIs by job reference</h2>
    <p>This action permanently removes <strong>all applications</strong> for the selected job reference. Use test records only.</p>
    <form action="manage.php" method="post" class="delete-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_by_ref">
      <div class="form-group"><label for="delete_ref">Job reference to delete</label><select name="delete_ref" id="delete_ref" required><option value="">Choose reference...</option>
        <?php foreach ($jobRefs as $job): ?><option value="<?= e($job['reference']) ?>"><?= e($job['reference'].' — '.$job['title']) ?></option><?php endforeach; ?>
      </select></div>
      <label class="choice"><input type="checkbox" name="confirm_delete" value="yes" required> I understand this will permanently delete all EOIs for this job.</label>
      <button class="button button-danger" type="submit">Delete matching EOIs</button>
    </form>
  </section>
</main>
<?php require __DIR__ . '/footer.inc'; ?>