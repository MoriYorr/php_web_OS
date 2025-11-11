<?php
// ——————————————————————————————————————————————
// 1. Функция классификации возраста
// ——————————————————————————————————————————————
function classifyAge(int $age): string {
    if ($age >= 18) {
        return "Взрослый";
    } elseif ($age >= 12) { // 12–17 включительно → подросток
        return "Подросток";
    } else { // 0–11
        return "Ребёнок";
    }
}

// ——————————————————————————————————————————————
// 2. Конвертация Цельсия → Фаренгейт
// ——————————————————————————————————————————————
function convertCelsiusToFahrenheit(float $celsius): float {
    return ($celsius * 9 / 5) + 32;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>PHP Basics — Detailed</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; line-height: 1.6; padding: 20px; background: #f9f9f9; }
        .block { background: white; margin: 20px 0; padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #2c3e50; }
        pre { margin: 0; white-space: pre-wrap; word-break: break-word; }
    </style>
</head>
<body>

<h1>Результаты выполнения PHP-заданий</h1>
<div class="block">
    <h2>1. Классификация возраста</h2>
    <pre>
<?php
echo "Возраст 8:  " . classifyAge(8)  . "\n";
echo "Возраст 15: " . classifyAge(15) . "\n";
echo "Возраст 25: " . classifyAge(25) . "\n";
?>
    </pre>
</div>

<!-- 2. Список городов -->
<div class="block">
    <h2>2. Список городов</h2>
    <pre>
<?php
$cities = ["Москва", "Санкт-Петербург", "Казань", "Екатеринбург", "Чита"];
foreach ($cities as $city) { // ✅ исправлено: "as", а не "in"
    echo $city . "\n";
}
?>
    </pre>
</div>

<!-- 3. FizzBuzz (0–99) -->
<div class="block">
    <h2>3. FizzBuzz (числа от 0 до 99)</h2>
    <pre style="font-size:12px; max-height:300px; overflow:auto;">
<?php
for ($i = 0; $i < 100; $i++) { // ✅ исправлено: ";" вместо ","
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
?>
    </pre>
</div>

<!-- 4. Конвертация температур -->
<div class="block">
    <h2>4. Конвертация °C → °F</h2>
    <pre>
<?php
echo "0°C   = " . convertCelsiusToFahrenheit(0)   . "°F\n";
echo "25°C  = " . convertCelsiusToFahrenheit(25)  . "°F\n";
echo "-10°C = " . convertCelsiusToFahrenheit(-10) . "°F\n";
echo "100°C = " . convertCelsiusToFahrenheit(100) . "°F\n";
?>
    </pre>
</div>

</body>
</html>
