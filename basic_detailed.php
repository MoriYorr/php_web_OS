<?php
// ——————————————————————————————————————————————
// 1. Классификация возраста — версия с if
// ——————————————————————————————————————————————
function classifyAge(int $age): string {
    if ($age >= 18) return "Взрослый";
    if ($age >= 12) return "Подросток";
    return "Ребёнок";
}

// ——————————————————————————————————————————————
// 2. Классификация возраста — версия с match
// ——————————————————————————————————————————————
function classifyAgeMatch(int $age): string {
    return match (true) {
        $age >= 18 => "Взрослый",
        $age >= 12 => "Подросток",
        default    => "Ребёнок"
    };
}

// ——————————————————————————————————————————————
// 3. Конвертация Цельсия → Фаренгейт
// ——————————————————————————————————————————————
function convertCelsiusToFahrenheit(float $celsius): float {
    return ($celsius * 9 / 5) + 32;
}

// ——————————————————————————————————————————————
// 4. Получение имени пользователя по ID
// ——————————————————————————————————————————————
function getUserName(int|string $id): string|false {
    if ($id === 1) {
        return "Администратор";
    }
    if ($id === "guest") {
        return "Гость";
    }
    return false;
}

// ——————————————————————————————————————————————
// Генерация контента
// ——————————————————————————————————————————————
ob_start();

// 1. Тест classifyAge (if-версия)
echo "<h2>1. Классификация возраста — if-версия</h2><pre>";
echo "Возраст 8:  " . classifyAge(8)  . "\n";
echo "Возраст 15: " . classifyAge(15) . "\n";
echo "Возраст 25: " . classifyAge(25) . "\n";
echo "</pre>";

// 2. Тест classifyAgeMatch (match-версия)
echo "<h2>2. Классификация возраста — match-версия</h2><pre>";
echo "Возраст 8:  " . classifyAgeMatch(8)  . "\n";
echo "Возраст 15: " . classifyAgeMatch(15) . "\n";
echo "Возраст 25: " . classifyAgeMatch(25) . "\n";
echo "</pre>";

// 3. Список городов
echo "<h2>3. Список городов</h2><pre>";
$cities = ["Москва", "Санкт-Петербург", "Казань", "Екатеринбург", "Чита"];
foreach ($cities as $city) {
    echo $city . "\n";
}
echo "</pre>";

// 4. FizzBuzz (0–99)
echo "<h2>4. FizzBuzz (0–99)</h2><pre style='font-size:12px; max-height:300px; overflow:auto;'>";
for ($i = 0; $i < 100; $i++) {
    if ($i % 3 === 0 && $i % 5 === 0) {
        echo "[$i] FizzBuzz\n";
    } elseif ($i % 3 === 0) {
        echo "[$i] Fizz\n";
    } elseif ($i % 5 === 0) {
        echo "[$i] Buzz\n";
    } else {
        echo "[$i] $i\n";
    }
}
echo "</pre>";

// 5. Тест convertCelsiusToFahrenheit
echo "<h2>5. Конвертация °C → °F</h2><pre>";
echo "0°C   = " . convertCelsiusToFahrenheit(0)   . "°F\n";
echo "25°C  = " . convertCelsiusToFahrenheit(25)  . "°F\n";
echo "-10°C = " . convertCelsiusToFahrenheit(-10) . "°F\n";
echo "100°C = " . convertCelsiusToFahrenheit(100) . "°F\n";
echo "</pre>";

// 6. Тест getUserName с проверкой === false
echo "<h2>6. Тест getUserName (с проверкой === false)</h2><pre>";
$testCases = [1, "guest", "1", 2, "admin", 0, ""];

foreach ($testCases as $case) {
    $result = getUserName($case);
    if ($result === false) {
        echo "getUserName(" . var_export($case, true) . ") => false (корректно)\n";
    } else {
        echo "getUserName(" . var_export($case, true) . ") => \"" . $result . "\"\n";
    }
}
echo "</pre>";

$content = ob_get_clean();

// ——————————————————————————————————————————————
// Финальная HTML-страница (полностью генерируется PHP)
// ——————————————————————————————————————————————
echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Результаты PHP-заданий</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      padding: 20px;
      background: #f9f9fb;
      color: #333;
      line-height: 1.5;
    }
    h1 {
      color: #2c3e50;
      border-bottom: 2px solid #eee;
      padding-bottom: 10px;
    }
    h2 {
      color: #3498db;
      margin-top: 1.8em;
    }
    pre {
      background: #fff;
      padding: 14px;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      overflow-x: auto;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <h1>Результаты выполнения PHP-заданий</h1>
  $content
</body>
</html>
HTML;
?>