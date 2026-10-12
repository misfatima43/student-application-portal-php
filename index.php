<?php
declare(strict_types=1);
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = 'Your session expired. Refresh the page and try again.';
    }
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $program = trim((string)($_POST['program'] ?? ''));
    $score = filter_var($_POST['academic_score'] ?? '', FILTER_VALIDATE_FLOAT);
    $statement = trim((string)($_POST['statement'] ?? ''));

    if ($name === '' || mb_strlen($name) > 120) $errors[] = 'Enter your name (up to 120 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if ($program === '' || mb_strlen($program) > 120) $errors[] = 'Select a program.';
    if ($score === false || $score < 0 || $score > 100) $errors[] = 'Academic score must be between 0 and 100.';
    if ($statement === '' || mb_strlen($statement) > 3000) $errors[] = 'Statement is required (up to 3000 characters).';

    if (!$errors) {
        try {
            require __DIR__ . '/config.php';
            $stmt = db()->prepare('INSERT INTO applications (full_name, email, program, academic_score, statement) VALUES (:name, :email, :program, :score, :statement)');
            $stmt->execute(['name'=>$name, 'email'=>$email, 'program'=>$program, 'score'=>$score, 'statement'=>$statement]);
            $success = true;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            error_log((string)$e);
            $errors[] = 'Application could not be saved. Check the local database setup and try again.';
        }
    }
}
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Scholarship Application | Student Portal</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header><a class="brand" href="#">Student<span>Portal</span></a><span>Scholarship applications</span></header>
<main>
<section class="intro"><p class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</p><h1>Apply for a<br><em>brighter future.</em></h1><p>Complete the form below to submit a scholarship application for review.</p></section>
<section class="panel">
<?php if ($success): ?>
<div class="success"><h2>Application submitted</h2><p>Your demo application was saved successfully. This is a local portfolio demonstration.</p><a href="index.php">Submit another</a></div>
<?php else: ?>
<h2>Application form</h2><p class="muted">Fields marked * are required.</p>
<?php if ($errors): ?><div class="errors" role="alert"><strong>Please check the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
<label>Full name *<input name="full_name" maxlength="120" required value="<?= h((string)($_POST['full_name'] ?? '')) ?>"></label>
<label>Email address *<input type="email" name="email" maxlength="190" required value="<?= h((string)($_POST['email'] ?? '')) ?>"></label>
<label>Program applying for *<select name="program" required><option value="">Choose a program</option><?php foreach (['Computer Science','Data Science','Business Administration','Engineering','Social Sciences'] as $p): ?><option <?= (($_POST['program'] ?? '') === $p) ? 'selected' : '' ?> value="<?= h($p) ?>"><?= h($p) ?></option><?php endforeach; ?></select></label>
<label>Academic score (%) *<input type="number" name="academic_score" min="0" max="100" step="0.01" required value="<?= h((string)($_POST['academic_score'] ?? '')) ?>"></label>
<label>Why are you applying? *<textarea name="statement" rows="5" maxlength="3000" required><?= h((string)($_POST['statement'] ?? '')) ?></textarea></label>
<button type="submit">Submit application →</button>
</form>
<?php endif; ?>
</section>
</main>
<footer>Portfolio demonstration · Do not submit real sensitive information</footer>
</body></html>
