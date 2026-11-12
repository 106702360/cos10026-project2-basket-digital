<?php
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO {
    static $connection;
    if ($connection instanceof PDO) return $connection;

    global $dbHost, $dbName, $dbUser, $dbPassword;
    try {
        $connection = new PDO(
            "mysql:host={$dbHost};charset=utf8mb4",
            $dbUser,
            $dbPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        $safeName = '`' . str_replace('`', '``', $dbName) . '`';
        $connection->exec("CREATE DATABASE IF NOT EXISTS {$safeName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $connection->exec("USE {$safeName}");
        setup_database($connection);
        return $connection;
    } catch (PDOException $exception) {
        $connection = null;
        error_log('Basket Digital database: ' . $exception->getMessage());
        throw new RuntimeException('Database connection failed. Check that MySQL is running in XAMPP.');
    }
}

function setup_database(PDO $pdo): void {
    // Initialise the demo database on first use.
    if ($pdo->query("SHOW TABLES LIKE 'project_meta'")->fetchColumn() !== false) return;

    $sql = file_get_contents(__DIR__ . '/database.sql');
    if ($sql === false) throw new RuntimeException('The database.sql file is missing.');
    $sql = preg_replace('/^\s*--[^\r\n]*/m', '', $sql);
    $statements = preg_split('/;\s*(?=\r?\n|$)/', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement !== '') $pdo->exec($statement);
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS project_meta (
        id TINYINT UNSIGNED PRIMARY KEY,
        version SMALLINT UNSIGNED NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec('INSERT INTO project_meta (id, version) VALUES (1, 2) ON DUPLICATE KEY UPDATE version = 2');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_valid($value): bool {
    return is_string($value) && hash_equals(csrf_token(), $value);
}
function redirect(string $url): void {
    header('Location: ' . $url, true, 303);
    exit;
}
function flash(string $kind, string $message): void {
    $_SESSION['flash'] = ['kind' => $kind, 'message' => $message];
}
function render_flash(): void {
    if (empty($_SESSION['flash'])) return;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $kind = $flash['kind'] === 'success' ? 'success' : 'error';
    $role = $kind === 'error' ? 'alert' : 'status';
    echo '<div class="alert alert-' . e($kind) . '" role="' . $role . '">' . e($flash['message']) . '</div>';
}
function logged_in(): bool {
    return !empty($_SESSION['hr_user_id']);
}
function require_login(): void {
    if (!logged_in()) redirect('login.php');
    header('Cache-Control: private, no-store');
}
function old(string $key): string {
    $value = $_SESSION['old_form'][$key] ?? '';
    return is_string($value) ? $value : '';
}
function stored_skills(): array {
    $value = $_SESSION['old_form']['skills'] ?? [];
    return is_array($value) ? $value : [];
}
function get_text(string $key): string {
    $value = $_GET[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}
function post_text(string $key): string {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim(stripslashes($value)) : '';
}
function text_length(string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}
function field_error_attributes(string $field, string $helpId = ''): string {
    $errors = $_SESSION['form_errors'] ?? [];
    $hasError = is_array($errors) && isset($errors[$field]);
    $descriptions = trim($helpId . ($hasError ? ' error-' . $field : ''));

    $attributes = $descriptions !== '' ? ' aria-describedby="' . e($descriptions) . '"' : '';
    return $attributes . ($hasError ? ' aria-invalid="true"' : '');
}

function form_field_error(string $field): void {
    $message = $_SESSION['form_errors'][$field] ?? null;
    if (is_string($message)) {
        echo '<small class="field-error" id="error-' . e($field) . '">' . e($message) . '</small>';
    }
}

function create_eoi_if_missing(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS eoi (
        EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        job_ref CHAR(5) NOT NULL,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        dob DATE NOT NULL,
        gender VARCHAR(24) NOT NULL,
        street VARCHAR(40) NOT NULL,
        suburb VARCHAR(40) NOT NULL,
        state VARCHAR(10) NOT NULL,
        postcode CHAR(4) NOT NULL,
        email VARCHAR(254) NOT NULL,
        phone VARCHAR(12) NOT NULL,
        skills TEXT NOT NULL,
        other_skills TEXT NULL,
        status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_job_ref (job_ref),
        INDEX idx_names (last_name, first_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}
