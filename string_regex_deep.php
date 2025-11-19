<?php

function generateEmailTemplate(string $name, string $product): string{
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

echo $nowdoc;

return $html;
}

echo generateEmailTemplate("Алексей", "продукт");



function getFirstAndLastChar(string $str): array
{
    if ($str === '') {
        return ['first' => '', 'last' => ''];
    }

    $first = mb_substr($str, 0, 1, 'UTF-8');
    $last  = mb_substr($str, -1, 1, 'UTF-8');

    return ['first' => $first, 'last' => $last];
}

$result = getFirstAndLastChar("Привет😊");
foreach($result as $r){
    echo $r;
}

function buildFullName(string $first, string $last): string{
    return trim($first) . " " . trim($last);
}

echo buildFullName("   Alex    ", "    Gordon    ");

echo "Hello";

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
echo toTitleCase("Fhjkhgdf fgkjhd fDJFHKJDS");

function extractFileName(string $path): string{
    
}
?>