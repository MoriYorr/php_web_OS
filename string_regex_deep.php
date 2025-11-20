<?php

// ----------------------------
// Ваши функции (без изменений)
// ----------------------------

error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateEmailTemplate(string $name, string $product): string {
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Письмо</title>
</head>
<body>
    <h1>Добрый день. Меня зовут $name</h1>
    <p>Я пишу вам по поводу $product</p>
</body>
</html>
HTML;
    $nowdoc = <<<'EOT'
Переменная $name не подставится!
EOT;

    // Эту строку убираем из вывода, чтобы не мешала
    // echo $nowdoc;

    return $html;
}

function getFirstAndLastChar(string $str): array {
    if ($str === '') {
        return ['first' => '', 'last' => ''];
    }

    $first = mb_substr($str, 0, 1, 'UTF-8');
    $last  = mb_substr($str, -1, 1, 'UTF-8');

    return ['first' => $first, 'last' => $last];
}

function buildFullName(string $first, string $last): string {
    return trim($first) . " " . trim($last);
}

function toTitleCase(string $phrase): string {
    $words = explode(" ", $phrase);
    $result = [];
    foreach ($words as $word) {
        $firstChar = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
        $rest = mb_strtolower(mb_substr($word, 1, null, 'UTF-8'), 'UTF-8');
        $result[] = $firstChar . $rest;
    }
    return implode(" ", $result);
}

function extractFileName(string $path): string {
    $lastSlashPos = strrpos($path, '/');
    if ($lastSlashPos === false) {
        return $path;
    }
    return substr($path, $lastSlashPos + 1);
}

function tagListToCSV(array $tags): string {
    return implode(',', $tags);
}

function csvToTagList(string $csv): array {
    return array_map('trim', explode(',', $csv));
}

function safeEcho(string $userInput): string {
    return htmlspecialchars($userInput, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function buildSearchUrl(string $query): string {
    return "https://example.com/search?q=" . rawurlencode($query);
}

function validatePassword(string $pass): bool {
    if (strlen($pass) < 8) {
        return false;
    }
    return (bool) preg_match('/^(?=.*[A-Z])(?=.*\d).*$/i', $pass);
}

function extractEmails(string $text): array {
    preg_match_all('/\b[\w.-]+@[\w.-]+\.\w+\b/i', $text, $matches);
    return $matches[0]; // исправлено: возвращаем только найденные email
}

function highlightNumbers(string $text): string {
    // Сначала заменяем десятичные (иначе целые "съедят" часть десятичных)
    $text = preg_replace('/[-+]?\d*\.\d+/', '<span class="number decimal">ДЕСЯТИЧНОЕ</span>', $text);
    $text = preg_replace('/[-+]?\d+/', '<span class="number integer">ЦЕЛОЕ</span>', $text);
    return $text;
}

// ----------------------------
// Генерация HTML-страницы
// ----------------------------

// Начало HTML
$output = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тестирование функций</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        h2 { color: #2c3e50; margin-top: 30px; }
        pre, code { background: #fff; padding: 12px; border-radius: 6px; overflow-x: auto; }
        .number { font-weight: bold; padding: 2px 6px; border-radius: 4px; }
        .number.integer { background: #e3f2fd; color: #0d47a1; }
        .number.decimal { background: #f3e5f5; color: #4a148c; }
        .result { margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Результаты работы ваших функций</h1>
HTML;

// Функция для добавления блока в вывод
function addBlock(&$output, string $title, $result, bool $isHtml = false) {
    $output .= "<h2>$title</h2>";
    if ($isHtml) {
        $output .= "<div class='result'>$result</div>";
    } else {
        $output .= "<pre>" . htmlspecialchars(print_r($result, true), ENT_QUOTES, 'UTF-8') . "</pre>";
    }
}

// 1. generateEmailTemplate
$emailHtml = generateEmailTemplate("Алексей", "новый курс по PHP");
addBlock($output, "1. generateEmailTemplate — шаблон письма", $emailHtml, true);

// 2. getFirstAndLastChar
$chars = getFirstAndLastChar("Привет😊");
addBlock($output, "2. getFirstAndLastChar — первый и последний символ", $chars);

// 3. buildFullName
$fullName = buildFullName("   Alex    ", "    Gordon    ");
addBlock($output, "3. buildFullName — полное имя", $fullName);

// 4. toTitleCase
$title = toTitleCase("Fhjkhgdf fgkjhd fDJFHKJDS");
addBlock($output, "4. toTitleCase — заглавные буквы в словах", $title);

// 5. extractFileName
$filename = extractFileName("/var/www/site/index.php");
addBlock($output, "5. extractFileName — имя файла из пути", $filename);

// 6. tagListToCSV
$csv = tagListToCSV(["php", "html", "css"]);
addBlock($output, "6. tagListToCSV — теги в CSV", $csv);

// 7. csvToTagList
$tags = csvToTagList("php, html , css ");
addBlock($output, "7. csvToTagList — CSV в теги", $tags);

// 8. safeEcho
$safe = safeEcho('<script>alert("XSS")</script>');
addBlock($output, "8. safeEcho — безопасный вывод", $safe);

// 9. buildSearchUrl
$url = buildSearchUrl("hello world & привет");
addBlock($output, "9. buildSearchUrl — формирование URL", $url);

// 10. validatePassword
$passValid = validatePassword("MyPass123");
$passInvalid = validatePassword("short1");
addBlock($output, "10. validatePassword — проверка пароля (MyPass123)", $passValid);
addBlock($output, "10b. validatePassword — проверка пароля (short1)", $passInvalid);

// 11. extractEmails
$emails = extractEmails("Свяжитесь: user@site.com или ADMIN@EXAMPLE.ORG");
addBlock($output, "11. extractEmails — извлечение email", $emails);

// 12. highlightNumbers
$numberText = highlightNumbers("Цены: 99 рублей, скидка -15%, итого 3.14");
addBlock($output, "12. highlightNumbers — подсветка чисел", $numberText, true);

// Завершение HTML
$output .= '</body></html>';

// Вывод
echo $output;
?>