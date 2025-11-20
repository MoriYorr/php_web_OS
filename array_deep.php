<?php
function getBookTitles(array $books): array {
    $titles = [];
    foreach ($books as $book) {
        $titles[] = $book["title"];
    }
    return $titles;
}

function hasBookByAuthor(array $books, string $author): bool {
    foreach ($books as $book) {
        if (isset($book['author']) && $book['author'] === $author) {
            return true;
        }
    }
    return false;
}

function addDefaultYear(array $books, int $defaultYear): array {
    $result = [];
    foreach ($books as $book) {
        // Если ключ "year" отсутствует или значение null/пустое — ставим год по умолчанию
        if (!isset($book['year']) || $book['year'] === null || $book['year'] === '') {
            $book['year'] = $defaultYear;
        }
        $result[] = $book;
    }
    return $result;
}

function filterBooksByYear(array $books, int $minYear): array {
    $result = [];
    foreach($books as $book){
        if(isset($book["year"]) && $book["year"] > $minYear){
            $result[] = $book;
        }
    }
    return $result;
}

function mapBooksToPairs(array $books): array{
    $result = [];
    foreach($books as $book){
        if(isset($book["year"])){
            $result[] = $book["title"] . "(" . $book["author"] . ", " . $book["year"] . ")";
        }
        else{
            $result[] = $book["title"] . "(" . $book["author"] . ", неизвестен)";
        }
    }
    return $result;
}

function sortBooks(array $books): array {
    usort($books, function($a, $b) {
        if ($a['year'] !== $b['year']) {
            return $a['year'] <=> $b['year'];
        }
        return strcmp($a['title'], $b['title']);
    });
    return $books;
}

function groupBy(array $items, string $key): array {
    $result = [];
    foreach ($items as $item) {
        if (isset($item[$key])) {
            $result[$item[$key]][] = $item;
        }
    }
    return $result;
}

$stack = [];

function stackPush(array &$stack, mixed $value){
    $stack[] = $value;
}

function stackPop(array &$stack): mixed {
    if (empty($stack)) {
        return null; 
    }
    return array_pop($stack); 
}

$queue = [];

function queueEnqueue(array &$queue, mixed $value){
    array_push($queue, $value);
}

function queueDequeue(array &$queue): mixed {
    if (empty($queue)) {
        return null;
    }
    return array_shift($queue);
}

function safeGet(array $array, string|int $key, mixed $default = null): mixed{
    if(isset($array[$key])){
        return $array[$key];
    }
    return $default;
}

function isAssociative(array $array): bool {
    if (empty($array)) {
        return false;
    }
    $keys = array_keys($array);
    foreach ($keys as $key) {
        if (!is_int($key)) {
            return true;
        }
    }
    $expectedKeys = range(0, count($array) - 1);
    return $keys !== $expectedKeys;
}

$books = [
    ['title' => '1984', 'author' => 'Джордж Оруэлл', 'year' => 1949],
    ['title' => 'Мастер и Маргарита', 'author' => 'Михаил Булгаков'], // без года
    ['title' => 'Гарри Поттер и философский камень', 'author' => 'Дж. К. Роулинг', 'year' => 1997],
    ['title' => 'Война и мир', 'author' => 'Лев Толстой', 'year' => 1869],
    ['title' => 'Dune', 'author' => 'Фрэнк Герберт', 'year' => 1965]
];

$html = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Демонстрация функций для работы с книгами и структурами данных</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #fafafa; }
        h2 { color: #2c3e50; margin-top: 30px; }
        pre { background: #fff; padding: 15px; border-radius: 6px; overflow-x: auto; }
        .result { margin: 10px 0; padding: 12px; background: #f8f9fa; border-left: 4px solid #3498db; }
    </style>
</head>
<body>
    <h1>Результаты работы функций</h1>
HTML;

// Вспомогательная функция для добавления блока
function addResultBlock(&$html, string $title, $result) {
    $html .= "<h2>{$title}</h2>";
    $html .= "<div class='result'><pre>" . htmlspecialchars(print_r($result, true), ENT_QUOTES, 'UTF-8') . "</pre></div>";
}

// 1. getBookTitles
$titles = getBookTitles($books);
addResultBlock($html, "1. Названия всех книг", $titles);

// 2. hasBookByAuthor
$hasOrwell = hasBookByAuthor($books, 'Джордж Оруэлл');
$hasPushkin = hasBookByAuthor($books, 'А. С. Пушкин');
addResultBlock($html, "2. Есть ли книга у автора?", [
    'Джордж Оруэлл' => $hasOrwell,
    'А. С. Пушкин' => $hasPushkin
]);

// 3. addDefaultYear
$booksWithYear = addDefaultYear($books, 2025);
addResultBlock($html, "3. Книги с годом по умолчанию (2025)", $booksWithYear);

// 4. filterBooksByYear
$recentBooks = filterBooksByYear($books, 1950);
addResultBlock($html, "4. Книги новее 1950 года", $recentBooks);

// 5. mapBooksToPairs
$pairs = mapBooksToPairs($books);
addResultBlock($html, "5. Книги в формате 'Название (Автор, год)'", $pairs);

// 6. sortBooks
$sortedBooks = sortBooks($books);
addResultBlock($html, "6. Книги, отсортированные по году и названию", $sortedBooks);

// 7. groupBy — по автору
$byAuthor = groupBy($books, 'author');
addResultBlock($html, "7. Группировка по автору", $byAuthor);

// 8. groupBy — по году (книги без года пропущены)
$byYear = groupBy($books, 'year');
addResultBlock($html, "8. Группировка по году", $byYear);

// 9. safeGet — пример использования
$firstBookTitle = safeGet($books[0], 'title', 'Без названия');
$firstBookGenre = safeGet($books[0], 'genre', 'Не указан');
addResultBlock($html, "9. Безопасное получение значения (safeGet)", [
    'title' => $firstBookTitle,
    'genre (по умолчанию)' => $firstBookGenre
]);

// 10. isAssociative — проверка массивов
$isBooksAssoc = isAssociative($books);
$isSimpleList = isAssociative(['a', 'b', 'c']);
$isSparse = isAssociative([0 => 'a', 2 => 'c']);
addResultBlock($html, "10. Проверка: ассоциативный ли массив?", [
    'Массив книг' => $isBooksAssoc, // true — содержит нечисловые ключи? Нет, но ключи 0..4 → false? Нет! $books — индексированный → false
    '["a","b","c"]' => $isSimpleList, // false
    '[0=>"a", 2=>"c"]' => $isSparse // true
]);

// 11. Стек (stack) — демонстрация
$stackDemo = [];
stackPush($stackDemo, "Первый");
stackPush($stackDemo, "Второй");
$popped1 = stackPop($stackDemo);
$popped2 = stackPop($stackDemo);
$popped3 = stackPop($stackDemo); // null
addResultBlock($html, "11. Работа со стеком (LIFO)", [
    'После push: два элемента',
    'Первый pop' => $popped1, // "Второй"
    'Второй pop' => $popped2, // "Первый"
    'Третий pop (пусто)' => $popped3 // null
]);

// 12. Очередь (queue) — демонстрация
$queueDemo = [];
queueEnqueue($queueDemo, "Первый в очереди");
queueEnqueue($queueDemo, "Второй в очереди");
$dequeued1 = queueDequeue($queueDemo);
$dequeued2 = queueDequeue($queueDemo);
$dequeued3 = queueDequeue($queueDemo); // null
addResultBlock($html, "12. Работа с очередью (FIFO)", [
    'После enqueue: два элемента',
    'Первый dequeue' => $dequeued1, // "Первый в очереди"
    'Второй dequeue' => $dequeued2, // "Второй в очереди"
    'Третий dequeue (пусто)' => $dequeued3 // null
]);

// Завершение HTML
$html .= '</body></html>';

// Вывод
echo $html;
?>
