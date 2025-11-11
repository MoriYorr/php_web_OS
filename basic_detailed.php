<?php
// ——————————————————————————————————————————————
// 1. Функция классификации возраста
// ——————————————————————————————————————————————
function classifyAge(int $age): string {
    if ($age >= 18) return "Взрослый";
    if ($age >= 12) return "Подросток";
    return "Ребёнок";
}

// ——————————————————————————————————————————————
// 2. Конвертация Цельсия → Фаренгейт
// ——————————————————————————————————————————————
function convertCelsiusToFahrenheit(float $celsius): float {
    return ($celsius * 9 / 5) + 32;
}

// ——————————————————————————————————————————————
// 3. Генерация контента
// ——————————————————————————————————————————————
ob_start();

echo "<h2>1. Классификация возраста</h2><pre>";
echo "Возраст 8:  " . classifyAge(8)  . "\n";
echo "Возраст 15: " . classifyAge(15) . "\n";
echo "Возраст 25: " . classifyAge(25) . "\n";
echo "</pre>";

echo "<h2>2. Список городов</h2><pre>";
$cities = ["Москва", "Санкт-Петербург", "Казань", "Екатеринбург", "Чита"];
foreach ($cities as $city) {
    echo $city . "\n";
}
echo "</pre>";

echo "<h2>3. FizzBuzz (0–99)</h2><pre style='font-size:12px; max-height:300px; overflow:auto;'>";
for ($i = 0; $i < 100; $i++) {
    if ($i % 3 === 0 && $i % 5 === 0) echo "[$i] FizzBuzz\n";
    elseif ($i % 3 === 0) echo "[$i] Fizz\n";
    elseif ($i % 5 === 0) echo "[$i] Buzz\n";
    else echo "[$i] $i\n";
}
echo "</pre>";

echo "<h2>4. Конвертация °C → °F</h2><pre>";
echo "0°C   = " . convertCelsiusToFahrenheit(0)   . "°F\n";
echo "25°C  = " . convertCelsiusToFahrenheit(25)  . "°F\n";
echo "-10°C = " . convertCelsiusToFahrenheit(-10) . "°F\n";
echo "100°C = " . convertCelsiusToFahrenheit(100) . "°F\n";
echo "</pre>";

$content = ob_get_clean();

// ——————————————————————————————————————————————
// 4. Финальный вывод — минималистичная обёртка
// ——————————————————————————————————————————————
echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Результаты</title>
  <style>
    body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
    pre { background: #fff; padding: 12px; border-radius: 4px; overflow-x: auto; }
    h2 { color: #2c3e50; margin-top: 1.5em; }
  </style>
</head>
<body>
  <h1>Результаты выполнения PHP-заданий</h1>
  $content
</body>
</html>
HTML;