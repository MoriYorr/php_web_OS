<?php
// ========= ВСЕ ТВОИ ФУНКЦИИ (БЕЗ ИЗМЕНЕНИЙ) =========
// (оставим их как есть — они уже исправлены выше)
function validateEmail(string $email): ?string {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    return null;
}

function validateName(string $name): ?string {
    if (preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]{2,50}$/u', $name)) {
        return $name;
    }
    return null;
}

function validateAge(int $age): ?int {
    if ($age > 0 && $age < 120) {
        return $age;
    }
    return null;
}

function escapeHtml(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        secureSessionStart();
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isValidImageFile(string $tmpPath): bool {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if (!$finfo) {
        return false;
    }
    $mimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    $allowedTypes = ['image/jpeg', 'image/png'];
    return in_array($mimeType, $allowedTypes, true);
}

function generateSafeFileName(string $originalName): string {
    $lowerName = strtolower($originalName);
    if (str_ends_with($lowerName, '.jpg') || str_ends_with($lowerName, '.jpeg')) {
        $extension = '.jpg';
    } elseif (str_ends_with($lowerName, '.png')) {
        $extension = '.png';
    } else {
        return '';
    }
    $randomName = bin2hex(random_bytes(16));
    return $randomName . $extension;
}

function isFileSizeValid(int $size, int $maxBytes = 2 * 1024 * 1024): bool {
    return $size > 0 && $size <= $maxBytes;
}

function saveUploadedFile(array $file, string $uploadDir): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmpPath = $file['tmp_name'];
    $originalName = $file['name'];
    $fileSize = $file['size'];
    if (!is_uploaded_file($tmpPath)) return null;
    if (!isFileSizeValid($fileSize)) return null;
    if (!isValidImageFile($tmpPath)) return null;
    $safeName = generateSafeFileName($originalName);
    if ($safeName === '') return null;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) return null;
    }
    $targetPath = $uploadDir . '/' . $safeName;
    if (!move_uploaded_file($tmpPath, $targetPath)) return null;
    return $safeName;
}

function secureSessionStart(): void {
    if (session_status() !== PHP_SESSION_NONE) return;
    ini_set('session.cookie_httponly', '1');
    $useSecureCookies = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    ini_set('session.cookie_secure', $useSecureCookies ? '1' : '0');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_lifetime', '0');
    session_start();
    if (!isset($_SESSION['_authenticated']) || !$_SESSION['_authenticated']) {
        session_regenerate_id(true);
    }
}

// ========= ЗАПУСК СЕССИИ =========
secureSessionStart();
if (!isset($_SESSION['csrf_token'])) {
    generateCsrfToken();
}

// ========= ОСНОВНАЯ ЛОГИКА ФОРМЫ РЕГИСТРАЦИИ =========
$errors = [];
$old = ['email' => '', 'name' => '', 'age' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['test'])) {
    // ... (оставим твою логику формы без изменений)
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($csrfToken)) {
        $errors['csrf'] = 'Неверный CSRF-токен';
    }

    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $age = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT) ?: 0;

    $old['email'] = $email;
    $old['name'] = $name;
    $old['age'] = (string)$age;

    if (empty($errors)) {
        if (($validatedEmail = validateEmail($email)) === null) {
            $errors['email'] = 'Некорректный email';
        }
        if (($validatedName = validateName($name)) === null) {
            $errors['name'] = 'Имя должно содержать 2–50 букв (латиница/кириллица) и пробелы';
        }
        if (($validatedAge = validateAge($age)) === null) {
            $errors['age'] = 'Возраст должен быть от 1 до 119';
        }

        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $avatarPath = saveUploadedFile($_FILES['avatar'], __DIR__ . '/../uploads');
            if ($avatarPath === null) {
                $errors['avatar'] = 'Невозможно загрузить аватар (проверьте тип, размер и права)';
            }
        }
    }

    if (empty($errors)) {
        echo "<h2>✅ Данные успешно сохранены!</h2>\n";
        echo "<p><strong>Email:</strong> " . escapeHtml($validatedEmail) . "</p>\n";
        echo "<p><strong>Имя:</strong> " . escapeHtml($validatedName) . "</p>\n";
        echo "<p><strong>Возраст:</strong> " . escapeHtml((string)$validatedAge) . "</p>\n";
        if ($avatarPath) {
            echo "<p><strong>Аватар сохранён:</strong> " . escapeHtml($avatarPath) . "</p>\n";
        } else {
            echo "<p><em>Аватар не загружен</em></p>\n";
        }
        exit;
    }
}

// ========= ТЕСТ-ПАНЕЛЬ: ОБРАБОТКА ТЕСТОВЫХ ЗАПРОСОВ =========
$testResults = [];

if (isset($_GET['test'])) {
    $test = $_GET['test'];

    switch ($test) {
        case 'validateEmail':
            $input = $_GET['input'] ?? 'test@example.com';
            $result = validateEmail($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'validateEmail';
            break;

        case 'validateName':
            $input = $_GET['input'] ?? 'Алексей';
            $result = validateName($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'validateName';
            break;

        case 'validateAge':
            $input = (int)($_GET['input'] ?? 25);
            $result = validateAge($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'validateAge';
            break;

        case 'escapeHtml':
            $input = $_GET['input'] ?? '<script>alert("XSS")</script>';
            $result = escapeHtml($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'escapeHtml';
            break;

        case 'generateCsrfToken':
            $oldToken = $_SESSION['csrf_token'] ?? null;
            $newToken = generateCsrfToken();
            $testResults['old'] = $oldToken;
            $testResults['new'] = $newToken;
            $testResults['valid'] = validateCsrfToken($newToken);
            $testResults['type'] = 'generateCsrfToken';
            break;

        case 'generateSafeFileName':
            $input = $_GET['input'] ?? 'Моё Фото.JPEG';
            $result = generateSafeFileName($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'generateSafeFileName';
            break;

        case 'isFileSizeValid':
            $input = (int)($_GET['input'] ?? 1500000);
            $result = isFileSizeValid($input);
            $testResults['input'] = $input;
            $testResults['result'] = $result;
            $testResults['type'] = 'isFileSizeValid';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация + Тест функций</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        .error { color: red; font-size: 0.9em; }
        label { display: block; margin: 8px 0 4px; }
        input, button, select { padding: 6px; margin: 4px 0; }
        .form-group { margin-bottom: 12px; }
        .test-panel { background: #f9f9f9; padding: 15px; border-radius: 6px; margin-top: 30px; }
        .test-result { margin-top: 10px; padding: 10px; background: #eef; border-left: 4px solid #4a9; }
        .invalid { background: #fee; border-left-color: #c33; }
        .valid { background: #efe; border-left-color: #3a3; }
        pre { background: #eee; padding: 8px; border-radius: 4px; overflow-x: auto; }
        h2 { border-bottom: 1px solid #ddd; padding-bottom: 8px; }
    </style>
</head>
<body>

<!-- ========== ФОРМА РЕГИСТРАЦИИ ========== -->
<h2>🔹 Форма регистрации</h2>
<?php if (!empty($errors['csrf'])): ?>
    <p class="error"><?= escapeHtml($errors['csrf']) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['csrf_token']) ?>">

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= escapeHtml($old['email']) ?>" required>
        <?php if (!empty($errors['email'])): ?><div class="error"><?= escapeHtml($errors['email']) ?></div><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" value="<?= escapeHtml($old['name']) ?>" required>
        <?php if (!empty($errors['name'])): ?><div class="error"><?= escapeHtml($errors['name']) ?></div><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="age">Возраст:</label>
        <input type="number" id="age" name="age" value="<?= escapeHtml($old['age']) ?>" min="1" max="119" required>
        <?php if (!empty($errors['age'])): ?><div class="error"><?= escapeHtml($errors['age']) ?></div><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="avatar">Аватар (необязательно):</label>
        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png">
        <?php if (!empty($errors['avatar'])): ?><div class="error"><?= escapeHtml($errors['avatar']) ?></div><?php endif; ?>
    </div>

    <button type="submit">Отправить</button>
</form>

<!-- ========== ТЕСТ-ПАНЕЛЬ ========== -->
<div class="test-panel">
    <h2>🧪 Тест функций безопасности</h2>
    <p>Проверь, как работают твои функции на разных входных данных.</p>

    <?php if (!empty($testResults)): ?>
        <div class="test-result <?= $testResults['result'] !== null && $testResults['result'] !== false ? 'valid' : 'invalid' ?>">
            <strong>Функция:</strong> <?= escapeHtml($testResults['type']) ?><br>
            <strong>Вход:</strong> <code><?= escapeHtml((string)$testResults['input']) ?></code><br>
            <strong>Результат:</strong>
            <?php if (isset($testResults['result'])): ?>
                <?php if (is_bool($testResults['result'])): ?>
                    <code><?= $testResults['result'] ? 'true' : 'false' ?></code>
                <?php else: ?>
                    <code><?= escapeHtml((string)$testResults['result']) ?></code>
                <?php endif; ?>
            <?php else: ?>
                <code>null</code>
            <?php endif; ?>
            <?php if (isset($testResults['valid'])): ?>
                <br><strong>Токен валиден:</strong> <?= $testResults['valid'] ? '✅ да' : '❌ нет' ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="GET" style="margin-top: 15px;">
        <input type="hidden" name="test" id="test-function" value="">
        <label>Выберите функцию для теста:</label>
        <select onchange="document.getElementById('test-function').value=this.value; this.form.submit();">
            <option value="">— Выберите —</option>
            <option value="validateEmail">validateEmail</option>
            <option value="validateName">validateName</option>
            <option value="validateAge">validateAge</option>
            <option value="escapeHtml">escapeHtml</option>
            <option value="generateCsrfToken">generateCsrfToken</option>
            <option value="generateSafeFileName">generateSafeFileName</option>
            <option value="isFileSizeValid">isFileSizeValid</option>
        </select>
    </form>

    <?php if (isset($_GET['test'])): ?>
        <?php
        $test = $_GET['test'];
        $defaultInput = match($test) {
            'validateEmail' => 'user@example.com',
            'validateName' => 'Иван Петров',
            'validateAge' => '25',
            'escapeHtml' => '<b>Привет!</b>',
            'generateSafeFileName' => 'Фото.jpg',
            'isFileSizeValid' => '1500000',
            default => '',
        };
        ?>
        <form method="GET" style="margin-top: 15px;">
            <input type="hidden" name="test" value="<?= escapeHtml($test) ?>">
            <div class="form-group">
                <label>Введите тестовое значение:</label>
                <input type="text" name="input" value="<?= escapeHtml($_GET['input'] ?? $defaultInput) ?>" style="width: 100%;">
            </div>
            <button type="submit">Протестировать</button>
        </form>
    <?php endif; ?>

    <h3 style="margin-top: 25px;">📌 Примеры использования:</h3>
    <ul>
        <li><code>validateEmail("bad-email")</code> → <code>null</code></li>
        <li><code>validateName("Алексей Иванович")</code> → <code>"Алексей Иванович"</code></li>
        <li><code>escapeHtml("&lt;script&gt;")</code> → <code>&amp;lt;script&amp;gt;</code></li>
        <li><code>generateSafeFileName("Моё фото.JPEG")</code> → <code>"a1b2c3...jpg"</code></li>
    </ul>
</div>

</body>
</html>