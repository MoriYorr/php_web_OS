<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// ——————————————————————————————
// Задание 4: Класс Book с JsonSerializable
// ——————————————————————————————
class Book implements JsonSerializable {
    public function __construct(
        public string $isbn,
        public string $title,
        public array $authors
    ) {}

    public function jsonSerialize(): array {
        return [
            'isbn' => $this->isbn,
            'title' => $this->title,
            'authors' => $this->authors
        ];
    }
}

// ——————————————————————————————
// Защита от XXE (актуально для PHP < 8.0)
// ——————————————————————————————
if (PHP_VERSION_ID < 80000) {
    libxml_disable_entity_loader(true);
}
libxml_use_internal_errors(true);

// ——————————————————————————————
// Задание 1: Создание books.xml (если не существует)
// ——————————————————————————————
$booksXmlPath = __DIR__ . '/books.xml';
if (!file_exists($booksXmlPath)) {
    $xmlContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<catalog>
  <book isbn="978-5-4461-1488-7">
    <title>Создаем динамические веб-сайты на PHP</title>
    <authors>
      <author>Кевин Татро</author>
      <author>Питер Макинтайр</author>
    </authors>
  </book>
  <book isbn="978-5-97060-569-1">
    <title>PHP и MySQL. Искусство программирования</title>
    <authors>
      <author>Люк Веллинг</author>
      <author>Лора Томсон</author>
    </authors>
  </book>
  <book isbn="978-5-4461-1972-1">
    <title>Изучаем PHP 8</title>
    <authors>
      <author>Робин Никсон</author>
    </authors>
  </book>
</catalog>
XML;
    file_put_contents($booksXmlPath, $xmlContent);
}

// ——————————————————————————————
// Задание 2: Парсинг XML через SimpleXML
// ——————————————————————————————
function loadBooksFromXml(string $filename): array {
    if (!file_exists($filename)) {
        throw new RuntimeException("File not found: $filename", 500);
    }

    $xml = simplexml_load_file($filename);
    if ($xml === false) {
        $errors = libxml_get_errors();
        libxml_clear_errors();
        $msg = "XML parse error in $filename";
        throw new RuntimeException($msg, 500);
    }

    $books = [];
    foreach ($xml->book as $book) {
        $authors = [];
        foreach ($book->authors->author as $author) {
            $authors[] = (string)$author;
        }
        $books[] = [
            'isbn' => (string)$book['isbn'],
            'title' => (string)$book->title,
            'authors' => $authors
        ];
    }
    return $books;
}

// ——————————————————————————————
// Задание 3: Вывод книг в HTML-таблице
// ——————————————————————————————
function renderBooksAsHtmlTable(array $books): string {
    $html = "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; margin:20px 0;'>";
    $html .= "<thead><tr><th>ISBN</th><th>Название</th><th>Авторы</th></tr></thead><tbody>";
    foreach ($books as $book) {
        $isbn = htmlspecialchars($book['isbn'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $title = htmlspecialchars($book['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $authors = htmlspecialchars(implode(', ', $book['authors']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html .= "<tr><td>$isbn</td><td>$title</td><td>$authors</td></tr>";
    }
    $html .= "</tbody></table>";
    return $html;
}

// ——————————————————————————————
// Задание 6: Приём JSON от клиента
// ——————————————————————————————
function getJsonInput(): ?array {
    $input = file_get_contents('php://input');
    if ($input === false) {
        http_response_code(400);
        return null;
    }
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        return null;
    }
    return $data;
}

// ——————————————————————————————
// Задание 7: Приём XML от клиента
// ——————————————————————————————
function getXmlInput(): ?SimpleXMLElement {
    $input = file_get_contents('php://input');
    if ($input === false) {
        http_response_code(400);
        return null;
    }
    $xml = simplexml_load_string($input);
    if ($xml === false) {
        libxml_clear_errors();
        http_response_code(400);
        return null;
    }
    return $xml;
}

// ——————————————————————————————
// Задание 8: Преобразование XML в массив (рекурсивно)
// ——————————————————————————————
function xmlToArray(SimpleXMLElement $xml): array {
    $result = [];

    foreach ($xml->children() as $name => $child) {
        $childArray = xmlToArray($child);

        // Если у дочернего элемента нет своих детей — извлекаем значение
        if (count($child->children()) === 0) {
            $value = (string)$child;
        } else {
            $value = $childArray;
        }

        if (isset($result[$name])) {
            if (!is_array($result[$name]) || !isset($result[$name][0])) {
                $result[$name] = [$result[$name]];
            }
            $result[$name][] = $value;
        } else {
            $result[$name] = $value;
        }
    }

    // Если у текущего узла вообще нет детей — возвращаем пустой массив
    // (или можно вернуть ['_text' => (string)$xml], но по заданию — не нужно)
    if (empty($result) && count($xml->children()) === 0) {
        return []; // или выбросить, но лучше не вызывать на листах
    }

    return $result;
}

// ——————————————————————————————
// Задание 5: API-эндпоинт /api/books.json
// ——————————————————————————————
if ($_SERVER['REQUEST_URI'] === '/api/books.json') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $books = loadBooksFromXml('books.xml');
        $bookObjects = array_map(fn($b) => new Book($b['isbn'], $b['title'], $b['authors']), $books);
        echo json_encode($bookObjects, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (RuntimeException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ——————————————————————————————
// Основной вывод: красивая HTML-страница (как в твоём примере)
// ——————————————————————————————
try {
    $books = loadBooksFromXml('books.xml');
} catch (RuntimeException $e) {
    http_response_code(500);
    exit('Ошибка загрузки XML: ' . htmlspecialchars($e->getMessage()));
}

// Функция для безопасного вывода
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Генерация HTML
$html = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог книг — XML → HTML → JSON API</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: #f9f9f9; }
        h1, h2 { color: #2c3e50; }
        table { width: 100%; max-width: 900px; margin: 20px auto; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f1f1f1; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 16px; border-radius: 6px; overflow-x: auto; margin: 15px 0; }
        .note { background: #e8f4fc; padding: 12px; border-left: 4px solid #3498db; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>📚 Каталог книг из books.xml</h1>
HTML;

$html .= renderBooksAsHtmlTable($books);

$html .= "<div class='note'><strong>💡 API:</strong> Открой <a href='/api/books.json' target='_blank'><code>/api/books.json</code></a> для получения JSON-представления.</div>";

// Дополнительно: вывод массива книг в читаемом виде
$html .= "<h2>Структура данных (массив книг)</h2>";
$html .= "<pre>" . h(print_r($books, true)) . "</pre>";

$html .= "<h2>Класс Book → JSON</h2>";
$bookObjects = array_map(fn($b) => new Book($b['isbn'], $b['title'], $b['authors']), $books);
$jsonExample = json_encode($bookObjects[0], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$html .= "<pre>" . h($jsonExample) . "</pre>";

$html .= "<h2>Демонстрация: xmlToArray()</h2>";
$xmlRaw = simplexml_load_file('books.xml');
$converted = xmlToArray($xmlRaw);
$html .= "<pre>" . h(print_r($converted, true)) . "</pre>";

$html .= '</body></html>';
echo $html;