<?php

// ======================
// КЛАССЫ (с исправлением критических ошибок)
// ======================

class Person {
    public string $name = "";
    public int $age = 0;
}

class Product {
    public string $title = "";
    protected int $stock = 0;
    private float $price = 0.0;

    public function setPrice(float $price): void {
        $this->price = $price;
    }

    public function getPrice(): float {
        return $this->price;
    }
}

class Greeter {
    public function __construct(
        public string $greeting
    ) {}
    public function greet(string $name): string {
        return $this->greeting . ", " . $name . "!";
    }
}

class Book {
    public function __construct(
        private string $title,
        private string $author,
        private int $year
    ) {}
    public function getInfo(): string {
        return "\"$this->title\" ($this->author, $this->year)";
    }
}

class BankAccount {
    private float $balance = 0.0;

    public function deposit(float $amount) {
        if ($amount > 0) {
            $this->balance += $amount;
        }
    }

    public function withdraw(float $amount): bool {
        if ($this->balance - $amount >= 0) { // >= 0, чтобы можно было обнулить счёт
            $this->balance -= $amount;
            return true;
        }
        return false;
    }

    public function getBalance(): float {
        return $this->balance; // исправлена опечатка: balnce → balance
    }
}

class ShopProduct {
    public function __construct(private string $title, private string $producer, private int $price) {}

    public function getSummaryLine(): string {
        return "{$this->title} ({$this->producer}): {$this->price}";
    }
}

class Counter {
    private static int $count = 0;

    public function __construct() {
        self::$count++;
    }

    public static function getCount(): int {
        return self::$count; // исправлено: $count → self::$count
    }
}

// Исправленный класс User
class User {
    public function __construct(
        private string $email,
        private string $name,
        private string $createdAt // храним как строку для простоты
    ) {}

    public function getEmail(): string {
        return $this->email;
    }

    public function getInfo(): string {
        return "$this->name ($this->email), зарегистрирован: $this->createdAt";
    }
}

// ======================
// ГЕНЕРАЦИЯ КРАСИВОЙ HTML-СТРАНИЦЫ
// ======================

$output = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Демонстрация классов</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        h1 {
            font-weight: 700;
            font-size: 2.25rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            border-left: 4px solid #3b82f6;
        }
        .card h2 {
            margin: 0 0 1rem 0;
            font-size: 1.375rem;
            color: #1e40af;
            font-weight: 600;
        }
        .result {
            font-size: 1.125rem;
            padding: 0.75rem 0;
            color: #1f2937;
        }
        .result-string {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
            background: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .key-value {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .key-value:last-child {
            border-bottom: none;
        }
        .key {
            font-weight: 600;
            color: #334155;
        }
        .value {
            color: #0f172a;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Демонстрация классов PHP</h1>
            <div class="subtitle">Примеры создания объектов и вызова методов</div>
        </header>
HTML;

// Вспомогательные функции для красивого вывода
function addCardString(&$output, string $title, string $text) {
    $output .= "
        <div class='card'>
            <h2>{$title}</h2>
            <div class='result'>
                <div class='result-string'>{$text}</div>
            </div>
        </div>
    ";
}

function addCardKeyValue(&$output, string $title, array $data) {
    $lines = '';
    foreach ($data as $key => $value) {
        $lines .= "<div class='key-value'>
            <span class='key'>{$key}</span>
            <span class='value'>" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "</span>
        </div>";
    }
    $output .= "
        <div class='card'>
            <h2>{$title}</h2>
            <div class='result'>{$lines}</div>
        </div>
    ";
}

// --- Примеры ---

// 1. Person
$p1 = new Person();
$p1->name = "Denis";
$p1->age = 10;

$p2 = new Person();
$p2->name = "Misha";
$p2->age = 12;

addCardKeyValue($output, "1. Person", [
    'Denis' => "Возраст: {$p1->age} лет",
    'Misha' => "Возраст: {$p2->age} лет"
]);

// 2. Product
$prod = new Product();
$prod->title = "Зеркальная камера";
$prod->setPrice(25000.50);
addCardKeyValue($output, "2. Product", [
    'Название' => $prod->title,
    'Цена' => number_format($prod->getPrice(), 2, ',', ' ') . ' ₽'
]);

// 3. Greeter
$greet = new Greeter("Привет");
addCardString($output, "3. Greeter", $greet->greet("Алексей"));

// 4. Book
$book = new Book("1984", "Джордж Оруэлл", 1949);
addCardString($output, "4. Book", $book->getInfo());

// 5. BankAccount
$acc = new BankAccount();
$acc->deposit(1000);
$acc->withdraw(300);
$success = $acc->withdraw(800) ? '✅ Успешно' : '❌ Отказ';
addCardKeyValue($output, "5. BankAccount", [
    'Баланс после операций' => number_format($acc->getBalance(), 2, ',', ' ') . ' ₽',
    'Попытка снять 800 ₽' => $success
]);

// 6. ShopProduct
$shopProd = new ShopProduct("Книга: PHP 8", "Иванов И.И.", 1200);
addCardString($output, "6. ShopProduct", $shopProd->getSummaryLine());

// 7. Counter
new Counter();
new Counter();
new Counter();
addCardString($output, "7. Counter", "Всего создано объектов: " . Counter::getCount());

// 8. User
$user = new User("anna@example.com", "Анна", date('d.m.Y'));
addCardString($output, "8. User", $user->getInfo());

// Завершение
$output .= '
    </div>
</body>
</html>';

echo $output;
?>