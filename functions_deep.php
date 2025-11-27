<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Функция проверки, является ли число простым
function isPrime(int $n): bool {
    if ($n <= 1) {
        return false;
    }
    if ($n == 2) {
        return true;
    }
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($n % $i == 0) {
            return false;
        }
    }
    return true;
}

// Обёртка для вывода строки
function isPrimeString(int $n): string {
    return isPrime($n) ? "True" : "False";
}

// Числа Фибоначчи (рекурсивно)
function fibonacci(int $n): int {
    if ($n == 0) {
        return 0;
    }
    if ($n == 1 || $n == 2) {
        return 1;
    }
    return fibonacci($n - 1) + fibonacci($n - 2);
}

// Форматирование телефона
function formatPhone(string $phone): string {
    if (strlen($phone) != 11 || $phone[0] != '8') {
        return 'Телефон должен быть формата 8XXXXXXXXXX';
    }
    $firstPart  = substr($phone, 1, 3);
    $secondPart = substr($phone, 4, 3);
    $thirdPart  = substr($phone, 7, 2);
    $fourthPart = substr($phone, 9, 2);
    return "+7 ($firstPart) $secondPart-$thirdPart-$fourthPart";
}

// Запоминающий факториал
function memorizedFactorial(int $n): int {
    static $memory = [];
    if (array_key_exists($n, $memory)) {
        return $memory[$n];
    }
    $result = 1;
    for ($i = 2; $i <= $n; $i++) {
        $result *= $i;
    }
    $memory[$n] = $result;
    return $result;
}

// Создание пользователя
function createUser(string $name, string $email, int $age, bool $isActive = true): string {
    return "Пользователь: $name, Email: $email, Возраст: $age, Активен: " . ($isActive ? 'Да' : 'Нет');
}

// Фабрика счётчика
function makeCounter(): callable {
    $count = 0;
    return function() use (&$count) {
        $count++;
        return $count;
    };
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Примеры функций PHP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #2c3e50; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<h1>Примеры использования функций</h1>

<!-- isPrime и isPrimeString -->
<h2>1. Проверка на простое число</h2>
<pre>
Проверка 1: <?= isPrimeString(1) ?>  
Проверка 2: <?= isPrimeString(2) ?>  
Проверка 3: <?= isPrimeString(3) ?>  
Проверка 4: <?= isPrimeString(4) ?>
</pre>

<!-- fibonacci -->
<h2>2. Числа Фибоначчи</h2>
<pre>
fibonacci(0): <?= fibonacci(0) ?>  
fibonacci(1): <?= fibonacci(1) ?>  
fibonacci(5): <?= fibonacci(5) ?>  
fibonacci(10): <?= fibonacci(10) ?>
</pre>

<!-- formatPhone -->
<h2>3. Форматирование телефона</h2>
<pre>
formatPhone("89123456789"): <?= htmlspecialchars(formatPhone("89123456789")) ?>  
formatPhone("123"): <?= htmlspecialchars(formatPhone("123")) ?>
</pre>

<!-- Фильтрация чётных чисел -->
<h2>4. Фильтрация чётных чисел из массива</h2>
<?php
$input = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$evenNumbers = array_filter($input, fn($v) => $v % 2 === 0);
?>
<pre>
Исходный массив: [<?= implode(', ', $input) ?>]  
Чётные числа: [<?= implode(', ', $evenNumbers) ?>]
</pre>

<!-- memorizedFactorial -->
<h2>5. Факториал с мемоизацией</h2>
<?php
// Сброс статической переменной невозможно, поэтому демонстрируем два вызова подряд
ob_start();
echo "Факториал 5: " . memorizedFactorial(5) . "\n";
echo "Повторный вызов факториала 5: " . memorizedFactorial(5) . "\n";
echo "Факториал 6: " . memorizedFactorial(6) . "\n";
$outputFactorial = ob_get_clean();
?>
<pre><?= htmlspecialchars($outputFactorial) ?></pre>

<!-- createUser -->
<h2>6. Создание пользователя</h2>
<pre>
<?= htmlspecialchars(createUser("Hi", "@hello", 100, false)) ?>
</pre>

<!-- makeCounter -->
<h2>7. Замыкание-счётчик</h2>
<?php
$counter = makeCounter();
$count1 = $counter(); // 1
$count2 = $counter(); // 2
$count3 = $counter(); // 3
?>
<pre>
Первый вызов: <?= $count1 ?>  
Второй вызов: <?= $count2 ?>  
Третий вызов: <?= $count3 ?>
</pre>

</body>
</html>