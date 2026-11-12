<?php
require __DIR__ . '/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') redirect('apply.php');
if (!csrf_valid($_POST['csrf'] ?? null)) {
    flash('error','The form session expired. Refresh the application page and try again.');
    redirect('apply.php');
}
$keys=['job-ref','first-name','last-name','dob','gender','street','suburb','state','postcode','email','phone','other-skills'];
$form=[];
foreach($keys as $key) $form[$key]=post_text($key);
$skills = $_POST['skills'] ?? [];
if (!is_array($skills)) $skills=[];
$skills = array_values(array_filter($skills,'is_string'));
$form['skills']=$skills;
$errors=[];
if (!preg_match('/^[a-zA-Z0-9]{5}$/D',$form['job-ref'])) $errors['job-ref']='Choose a five-character alphanumeric job reference.';
foreach (['first-name'=>'First name', 'last-name'=>'Last name'] as $key => $label) {
    $name = $form[$key];
    if (text_length($name) < 1 || text_length($name) > 20 ||
        !preg_match('/^[\p{L}\p{M}]+(?:[ \x{0027}\x{2019}-][\p{L}\p{M}]+)*$/uD', $name)) {
        $errors[$key] = $label . ' must contain only letters (spaces, apostrophes and hyphens allowed), up to 20 characters.';
    }
}
if (!preg_match('~^(\d{2})/(\d{2})/(\d{4})$~D',$form['dob'],$dateParts)
    || !checkdate((int)($dateParts[2]??0),(int)($dateParts[1]??0),(int)($dateParts[3]??0))) {
    $errors['dob']='Enter a real date in dd/mm/yyyy format.';
} else {
    $dobISO=sprintf('%04d-%02d-%02d',(int)$dateParts[3],(int)$dateParts[2],(int)$dateParts[1]);
    if ($dobISO > date('Y-m-d') || (int)$dateParts[3] < 1900) $errors['dob']='Date of birth cannot be in the future or before 1900.';
}
if (!in_array($form['gender'],['male','female','other','prefer-not-to-say'],true)) $errors['gender']='Select a gender option.';
if (text_length($form['street']) < 3 || text_length($form['street']) > 40 ||
    !preg_match('/\p{L}/u', $form['street']) || preg_match('/[\x00-\x1f\x7f<>]/', $form['street'])) {
    $errors['street'] = 'Enter a street address of 3–40 characters, including at least one letter.';
}
if (text_length($form['suburb']) < 2 || text_length($form['suburb']) > 40 ||
    !preg_match('/\p{L}/u', $form['suburb']) || preg_match('/[\x00-\x1f\x7f<>]/', $form['suburb'])) {
    $errors['suburb'] = 'Enter a suburb or town of 2–40 characters, including letters.';
}
$states = ['VIC','NSW','QLD','NT','WA','SA','TAS','ACT'];
if (!in_array($form['state'], $states, true)) {
    $errors['state'] = 'Choose an Australian state or territory.';
}
if (!preg_match('/^[0-9]{4}$/D', $form['postcode'])) {
    $errors['postcode'] = 'Enter a four-digit Australian postcode.';
}
if (strlen($form['email']) > 254 || !filter_var($form['email'],FILTER_VALIDATE_EMAIL)) $errors['email']='Enter a valid email address.';
if (!preg_match('/^[0-9]{8,12}$/D', $form['phone'])) {
    $errors['phone'] = 'Phone must contain 8–12 digits with no spaces or symbols.';
}
$allowedSkills=['HTML','CSS','JS','UI'];
$skills=array_values(array_unique($skills));
if (!$skills || count(array_diff($skills,$allowedSkills))>0) $errors['skills']='Choose at least one skill from the available options.';
if (text_length($form['other-skills'])>1500) $errors['other-skills']='Other skills must not exceed 1500 characters.';

if (!$errors) {
  try {
    $pdo=db();
    create_eoi_if_missing($pdo);
    $jobCheck=$pdo->prepare('SELECT reference FROM jobs WHERE reference = :ref');
    $jobCheck->execute(['ref'=>$form['job-ref']]);
    if (!$jobCheck->fetch()) $errors['job-ref']='That job reference is not in the jobs database.';
    if (!$errors) {
      $statement=$pdo->prepare('INSERT INTO eoi (job_ref,first_name,last_name,dob,gender,street,suburb,state,postcode,email,phone,skills,other_skills) VALUES (:job_ref,:first_name,:last_name,:dob,:gender,:street,:suburb,:state,:postcode,:email,:phone,:skills,:other_skills)');
      $statement->execute([
       'job_ref'=>$form['job-ref'],'first_name'=>$form['first-name'],'last_name'=>$form['last-name'],
       'dob'=>$dobISO,'gender'=>$form['gender'],'street'=>$form['street'],'suburb'=>$form['suburb'],
       'state'=>$form['state'],'postcode'=>$form['postcode'],'email'=>$form['email'],'phone'=>$form['phone'],
       'skills'=>json_encode($skills,JSON_UNESCAPED_UNICODE),'other_skills'=>$form['other-skills']]);
      $_SESSION['confirmed_eoi']=(int)$pdo->lastInsertId();
      unset($_SESSION['old_form'],$_SESSION['form_errors']);
      redirect('confirmation.php');
    }
  } catch (RuntimeException|PDOException $exception) {
    error_log('EOI processing failed: '.$exception->getMessage());
    $errors['database']='Unable to save the application. Check the local database connection and try again.';
  }
}
$_SESSION['old_form']=$form;
$_SESSION['form_errors']=$errors;
redirect('apply.php');