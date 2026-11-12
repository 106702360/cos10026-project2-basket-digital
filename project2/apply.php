<?php
require __DIR__ . '/bootstrap.php';
$pageTitle = 'Basket Digital | Apply for a Job';
$activePage = 'apply';
$jobOptions = [];
$dbError = '';

try {
    $jobOptions = db()->query('SELECT reference, title FROM jobs ORDER BY title')->fetchAll();
} catch (RuntimeException|PDOException $ex) {
    $dbError = 'Start MySQL in XAMPP and refresh this page to enable applications.';
}

$requestedRef = $_GET['ref'] ?? '';
$selectedRef = old('job-ref') ?: (is_string($requestedRef) ? $requestedRef : '');
$formErrors = $_SESSION['form_errors'] ?? [];
require __DIR__ . '/header.inc';
?>
<main id="main-content" class="container section-pad narrow">
    <p class="eyebrow">Take the next step</p>
    <h1>Apply for a role</h1>
    <p class="lead">Fields marked * are required. We check all details on the PHP server when you submit.</p>
    <?php render_flash(); ?>
    <?php if ($dbError): ?>
        <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
    <?php endif; ?>

    <?php if (is_array($formErrors) && $formErrors): ?>
        <div id="form-errors" class="alert alert-error" role="alert">
            <strong>Please correct these details:</strong>
            <ul>
                <?php foreach ($formErrors as $field => $message): ?>
                    <?php if ($field === 'database'): ?>
                        <li><?= e($message) ?></li>
                    <?php else: ?>
                        <li><a href="#<?= e($field) ?>"><?= e($message) ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="process_eoi.php" method="post" novalidate class="card apply-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="job-ref">Job reference *</label>
            <select name="job-ref" id="job-ref" required<?= field_error_attributes('job-ref', 'job-help') ?>>
                <option value="">Choose a job...</option>
                <?php foreach ($jobOptions as $job): ?>
                    <option value="<?= e($job['reference']) ?>"<?= $selectedRef === $job['reference'] ? ' selected' : '' ?>>
                        <?= e($job['reference'] . ' — ' . $job['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small id="job-help">Select the five-character reference for the job you want.</small>
            <?php form_field_error('job-ref'); ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="first-name">First name *</label>
                <input id="first-name" name="first-name" maxlength="20" type="text" autocomplete="given-name" required value="<?= e(old('first-name')) ?>"<?= field_error_attributes('first-name') ?>>
                <?php form_field_error('first-name'); ?>
            </div>
            <div class="form-group">
                <label for="last-name">Last name *</label>
                <input id="last-name" name="last-name" maxlength="20" type="text" autocomplete="family-name" required value="<?= e(old('last-name')) ?>"<?= field_error_attributes('last-name') ?>>
                <?php form_field_error('last-name'); ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="dob">Date of birth (dd/mm/yyyy) *</label>
                <input id="dob" name="dob" type="text" inputmode="numeric" placeholder="dd/mm/yyyy" maxlength="10" required value="<?= e(old('dob')) ?>"<?= field_error_attributes('dob', 'dob-help') ?>>
                <small id="dob-help">Example: 08/04/2003</small>
                <?php form_field_error('dob'); ?>
            </div>
            <fieldset id="gender" class="form-group"<?= field_error_attributes('gender') ?>>
                <legend>Gender *</legend>
                <?php foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer-not-to-say' => 'Prefer not to say'] as $value => $label): ?>
                    <label class="choice"><input type="radio" name="gender" value="<?= e($value) ?>"<?= old('gender') === $value ? ' checked' : '' ?> required> <?= e($label) ?></label>
                <?php endforeach; ?>
                <?php form_field_error('gender'); ?>
            </fieldset>
        </div>

        <div class="form-group">
            <label for="street">Street address *</label>
            <input id="street" name="street" type="text" maxlength="40" autocomplete="street-address" required value="<?= e(old('street')) ?>"<?= field_error_attributes('street', 'street-help') ?>>
            <small id="street-help">3–40 characters. Include a street or building name; a building number is optional.</small>
            <?php form_field_error('street'); ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="suburb">Suburb or town *</label>
                <input id="suburb" name="suburb" type="text" maxlength="40" autocomplete="address-level2" required value="<?= e(old('suburb')) ?>"<?= field_error_attributes('suburb') ?>>
                <?php form_field_error('suburb'); ?>
            </div>
            <div class="form-group">
                <label for="state">Australian state / territory *</label>
                <select id="state" name="state" autocomplete="address-level1" required<?= field_error_attributes('state') ?>>
                    <option value="">Choose...</option>
                    <?php foreach (['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'] as $state): ?>
                        <option value="<?= e($state) ?>"<?= old('state') === $state ? ' selected' : '' ?>><?= e($state) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php form_field_error('state'); ?>
            </div>
            <div class="form-group">
                <label for="postcode">Postcode (4 digits) *</label>
                <input id="postcode" name="postcode" type="text" inputmode="numeric" maxlength="4" autocomplete="postal-code" placeholder="e.g. 3000" required value="<?= e(old('postcode')) ?>"<?= field_error_attributes('postcode') ?>>
                <?php form_field_error('postcode'); ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input id="email" name="email" type="email" maxlength="254" autocomplete="email" required value="<?= e(old('email')) ?>"<?= field_error_attributes('email') ?>>
                <?php form_field_error('email'); ?>
            </div>
            <div class="form-group">
                <label for="phone">Phone number *</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" maxlength="12" autocomplete="tel" required value="<?= e(old('phone')) ?>"<?= field_error_attributes('phone', 'phone-help') ?>>
                <small id="phone-help">Use 8–12 digits without spaces or symbols.</small>
                <?php form_field_error('phone'); ?>
            </div>
        </div>

        <fieldset id="skills"<?= field_error_attributes('skills') ?>>
            <legend>Skills (choose at least one) *</legend>
            <div class="choices">
                <?php foreach (['HTML' => 'HTML5', 'CSS' => 'CSS3', 'JS' => 'JavaScript', 'UI' => 'UI/UX Design'] as $value => $label): ?>
                    <label class="choice"><input type="checkbox" name="skills[]" value="<?= e($value) ?>"<?= in_array($value, stored_skills(), true) ? ' checked' : '' ?>> <?= e($label) ?></label>
                <?php endforeach; ?>
            </div>
            <?php form_field_error('skills'); ?>
        </fieldset>

        <div class="form-group">
            <label for="other-skills">Other skills (optional)</label>
            <textarea id="other-skills" name="other-skills" rows="4" maxlength="1500"<?= field_error_attributes('other-skills') ?>><?= e(old('other-skills')) ?></textarea>
            <?php form_field_error('other-skills'); ?>
        </div>

        <p class="privacy-note">Your application details are used for this class demonstration and stored in a local MySQL database. Please use dummy applicant details during testing.</p>
        <button type="submit"<?= $dbError ? ' disabled' : '' ?>>Submit application</button>
    </form>
</main>
<?php
unset($_SESSION['old_form'], $_SESSION['form_errors']);
require __DIR__ . '/footer.inc';
