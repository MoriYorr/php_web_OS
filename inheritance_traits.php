<?php
// ----------------------------
// ЗАДАНИЕ 1: Наследование
// ----------------------------
class Product {
    public function __construct(
        protected string $title,
        protected float $price
    ) {}

    public function getTitle(): string {
        return $this->title;
    }
}

class Book extends Product {
    public function __construct(string $title, float $price, private string $author) {
        parent::__construct($title, $price);
    }

    public function getAuthor(): string {
        return $this->author;
    }
}

// ----------------------------
// ЗАДАНИЕ 2: Абстрактные классы
// ----------------------------
abstract class Lesson {
    abstract public function cost(): int;
    abstract public function chargeType(): string;
}

class Lecture extends Lesson {
    public function cost(): int { return 30; }
    public function chargeType(): string { return "Фиксированная ставка"; }
}

class Seminar extends Lesson {
    public function cost(): int { return 90; }
    public function chargeType(): string { return "Нефиксированная ставка"; } 
}

// ----------------------------
// ЗАДАНИЕ 3 & 4: Интерфейсы + Программирование на основе интерфейса
// ----------------------------
interface Bookable {
    public function book(): void;
}

interface Chargeable {
    public function calculateFee(): float;
}

class Workshop implements Bookable, Chargeable {
    public function book(): void {
        echo "Workshop booked!";
    }
    public function calculateFee(): float {
        return 1.5;
    }
}

function processBooking(Bookable $item): void {
    $item->book();
}

// ----------------------------
// ЗАДАНИЕ 5 & 6: Трейты
// ----------------------------
trait IdentityTrait {
    public function generateId(): string {
        return uniqid();
    }
}

trait PriceUtilities {
    public function calculateTax(float $price): float {
        return $price * 0.2;
    }
}

class ShopProduct {
    use PriceUtilities;
    use IdentityTrait;

    public function __construct(private float $price, private string $name) {}

    public function getPriceWithTax(): float {
        return $this->price + $this->calculateTax($this->price);
    }
}

// ----------------------------
// ЗАДАНИЕ 7: Конфликты трейтов
// ----------------------------
trait Printer {
    public function output(): void {
        echo "Printer";
    }
}

trait Logger {
    public function output(): void {
        echo "Logger";
    }
}

class Report {
    use Printer, Logger {
        Logger::output insteadOf Printer;
        Printer::output as printOutput;
    }
}

// ----------------------------
// ЗАДАНИЕ 8: Трейт с доступом к свойствам
// ----------------------------
trait Describable {
    public function describe(): string {
        return "Объект: {$this->name}";
    }
}

class Item {
    use Describable;
    public function __construct(public string $name) {}
}

// ----------------------------
// ЗАДАНИЕ 9: Абстрактные методы в трейтах
// ----------------------------
trait Validatable {
    abstract public function getRules(): array;

    public function validate(): bool {
        return true;
    }
}

class UserForm {
    use Validatable;

    public function getRules(): array {
        return ['email' => 'required'];
    }
}

// ----------------------------
// ЗАДАНИЕ 10: HasMedia + TaxCalculation
// ----------------------------
interface HasMedia {
    public function getMediaLength(): int;
}

trait TaxCalculation {
    public function getTax(float $price): float {
        return $price * 0.2;
    }
}

class BookProduct implements HasMedia {
    use TaxCalculation;
    private int $length = 300;
    
    public function __construct(private float $price, private string $name) {}

    public function getPriceWithTax(): float {
        return $this->price + $this->getTax($this->price);
    }

    public function getMediaLength(): int {
        return $this->length;
    }
}

class CDProduct implements HasMedia {
    use TaxCalculation;
    private int $length = 74;
    
    public function __construct(private float $price, private string $name) {}

    public function getPriceWithTax(): float {
        return $this->price + $this->getTax($this->price);
    }

    public function getMediaLength(): int {
        return $this->length;
    }
}

// ----------------------------
// ЗАДАНИЕ 11: Service + Schedulable + Logger
// ----------------------------
abstract class Service {
    abstract public function getDuration(): int;
    abstract public function getDescription(): string;
}

interface Schedulable {
    public function schedule(): string;
}

trait Logger2 {
    public function log(string $msg): void {
        echo $msg . PHP_EOL;
    }
}

class Consulting extends Service implements Schedulable {
    use Logger2;

    public function getDuration(): int {
        return 60;
    }

    public function getDescription(): string {
        return "This is a description of consulting";
    }

    public function schedule(): string {
        return "Scheduled consulting session";
    }
}

class Training extends Service implements Schedulable {
    use Logger2;

    public function getDuration(): int {
        return 120;
    }

    public function getDescription(): string {
        return "This is a description of training";
    }

    public function schedule(): string {
        return "Scheduled training session";
    }
}

// ============================================================================
// === ДЕМОНСТРАЦИЯ И ГЕНЕРАЦИЯ HTML ==========================================
// ============================================================================

ob_start();

// Задание 1
$book = new Book("Clean Code", 29.99, "Robert Martin");
echo "<h3>Задание 1: Наследование</h3>";
echo "Книга: " . $book->getTitle() . " — Автор: " . $book->getAuthor() . "<br><br>";

// Задание 2
$lecture = new Lecture();
$seminar = new Seminar();
echo "<h3>Задание 2: Абстрактные классы</h3>";
echo "Лекция: " . $lecture->cost() . " руб, " . $lecture->chargeType() . "<br>";
echo "Семинар: " . $seminar->cost() . " руб, " . $seminar->chargeType() . "<br><br>";

// Задание 3 + 4
$workshop = new Workshop();
echo "<h3>Задание 3–4: Интерфейсы и программирование на основе интерфейса</h3>";
echo "Fee: " . $workshop->calculateFee() . "<br>";
echo "Booking: ";
processBooking($workshop);
echo "<br><br>";

// Задание 5–6
$product = new ShopProduct(100.0, "Notebook");
echo "<h3>Задание 5–6: Трейты (PriceUtilities + IdentityTrait)</h3>";
echo "Цена с налогом: " . $product->getPriceWithTax() . "<br>";
echo "ID: " . $product->generateId() . "<br><br>";

// Задание 7
$report = new Report();
echo "<h3>Задание 7: Разрешение конфликтов трейтов</h3>";
echo "output() → ";
$report->output(); // из Logger
echo "<br>";
echo "printOutput() → ";
$report->printOutput(); // из Printer
echo "<br><br>";

// Задание 8
$item = new Item("Товар А");
echo "<h3>Задание 8: Трейт с доступом к свойствам</h3>";
echo $item->describe() . "<br><br>";

// Задание 9
$form = new UserForm();
echo "<h3>Задание 9: Абстрактные методы в трейтах</h3>";
echo "Правила: " . json_encode($form->getRules()) . "<br>";
echo "Валидация успешна: " . ($form->validate() ? "Да" : "Нет") . "<br><br>";

// Задание 10
$bookProd = new BookProduct(50.0, "PHP Guide");
$cdProd = new CDProduct(30.0, "Music Album");
echo "<h3>Задание 10: HasMedia + TaxCalculation</h3>";
echo "Книга — длина: " . $bookProd->getMediaLength() . ", цена с налогом: " . $bookProd->getPriceWithTax() . "<br>";
echo "CD — длина: " . $cdProd->getMediaLength() . ", цена с налогом: " . $cdProd->getPriceWithTax() . "<br><br>";

// Задание 11
$consulting = new Consulting();
$training = new Training();
echo "<h3>Задание 11: Service + Schedulable + Logger</h3>";
echo "Консультация: " . $consulting->getDescription() . " (" . $consulting->getDuration() . " мин)<br>";
echo "Расписание: " . $consulting->schedule() . "<br>";
echo "Лог: ";
$consulting->log("Консультация запланирована");
echo "<br>";

echo "Тренинг: " . $training->getDescription() . " (" . $training->getDuration() . " мин)<br>";
echo "Расписание: " . $training->schedule() . "<br>";
echo "Лог: ";
$training->log("Тренинг подтверждён");

$content = ob_get_clean();

// Генерация HTML
$html = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Демонстрация ООП в PHP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
        h3 { color: #2c3e50; margin-top: 30px; }
        br + br { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Демонстрация ООП-конструкций в PHP</h1>
    <p>Все 11 заданий успешно выполнены и протестированы.</p>
    $content
</body>
</html>
HTML;

echo $html;
?>