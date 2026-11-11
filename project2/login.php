<?php
require __DIR__ . '/bootstrap.php';
if (logged_in()) redirect('manage.php');
$pageTitle='Basket Digital | HR Manager Login';$activePage='login';
$error='';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_valid($_POST['csrf']??null)) $error='Invalid session token. Refresh and try again.';
    elseif (!empty($_SESSION['login_block_until']) && time() < $_SESSION['login_block_until']) $error='Too many attempts. Try again in a few minutes.';
    else {
        $username=post_text('username');
        $password=$_POST['password']??'';
        if (!is_string($password)) $password='';
        try {
            $stmt=db()->prepare('SELECT id,username,password_hash FROM users WHERE username = :user LIMIT 1');
            $stmt->execute(['user'=>$username]);
            $user=$stmt->fetch();
            if ($user && password_verify($password,$user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['hr_user_id']=(int)$user['id'];
                $_SESSION['hr_username']=$user['username'];
                unset($_SESSION['login_failures'],$_SESSION['login_block_until']);
                redirect('manage.php');
            }
            $error='Incorrect username or password.';
            $_SESSION['login_failures']=($_SESSION['login_failures']??0)+1;
            if ($_SESSION['login_failures']>=5) {
                $_SESSION['login_block_until']=time()+300;
                $_SESSION['login_failures']=0;
            }
        } catch (RuntimeException|PDOException $ex) { $error='Database unavailable. Start MySQL in XAMPP and try again.'; }
    }
}
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad narrow">
  <section class="card login-card"><p class="eyebrow">Team access</p><h1>HR manager sign in</h1>
    <p>Only authorised HR staff can view or manage applicant information.</p>
    <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php" class="apply-form" novalidate>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-group"><label for="username">Username</label><input id="username" name="username" autocomplete="username" required maxlength="50"></div>
      <div class="form-group"><label for="password">Password</label><input type="password" id="password" name="password" autocomplete="current-password" required></div>
      <button type="submit">Sign in securely</button>
    </form>
    <p class="privacy-note">This area is for the authorised HR manager. Applications shown here are demonstration records.</p>
  </section>
</main>
<?php require __DIR__ . '/footer.inc'; ?>