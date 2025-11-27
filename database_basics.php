<?php
/*
-- Создание базы данных и таблицы books

CREATE DATABASE IF NOT EXISTS library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE library;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    isbn VARCHAR(20),
    pub_year INT,
    available TINYINT DEFAULT 1
);
*/

function getPdoConnection(): PDO
{
    $username = 'mori';      // или твой логин
    $password = 'fvthbrf900';
    $host = 'localhost';
    $dbname = 'library';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
}

// Получаем подключение (для проверки)
$pdo = getPdoConnection();

// === НАГЛЯДНАЯ ПРОВЕРКА ПОДКЛЮЧЕНИЯ ===
try {
    $stmt = $pdo->query("SELECT 1 AS test");
    $result = $stmt->fetch();
    if ($result && $result['test'] === 1) {
        echo "<p style='color: green; font-family: monospace;'>✅ Подключение к базе данных успешно!</p>";
    }
} catch (PDOException $e) {
    die("<p style='color: red;'>❌ Ошибка при тестовом запросе: " . htmlspecialchars($e->getMessage()) . "</p>");
}

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'books'");
    $tableExists = $stmt->fetch();
    if ($tableExists) {
        echo "<p style='color: green; font-family: monospace;'>✅ Таблица `books` найдена.</p>";
    } else {
        echo "<p style='color: orange; font-family: monospace;'>⚠️ Таблица `books` не найдена (её нужно создать).</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Ошибка при проверке таблицы: " . htmlspecialchars($e->getMessage()) . "</p>";
}

function addBook(string $title, string $author, string $isbn, int $year): int
{
    $pdo = getPdoConnection();
    $sql = "INSERT INTO books (title, author, isbn, pub_year) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $author, $isbn, $year]);
    return (int)$pdo->lastInsertId();
}

// Пример добавления книги (закомментирован, чтобы не дублировалась при каждом запуске)
// $id = addBook('1984', 'Джордж Оруэлл', '978-0-452-28423-4', 1949);
// echo "<p>Книга добавлена с ID: $id</p>";

function findBooksByAuthor(string $author): array
{
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE author = ?");
    $stmt->execute([$author]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllAvailableBooks(): array
{
    $pdo = getPdoConnection();
    $stmt = $pdo->query("SELECT * FROM books WHERE available = 1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function setBookAvailability(int $bookId, bool $available): void
{
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("UPDATE books SET available = :available WHERE id = :bookId");
    $stmt->execute([
        ':available' => (int)$available,
        ':bookId'    => $bookId
    ]);
}

function transferStock(int $fromId, int $toId, int $amount): void
{
    $pdo = getPdoConnection();
    $pdo->beginTransaction();

    try {
        $stmt1 = $pdo->prepare("UPDATE books SET available = available - :amount WHERE id = :fromId");
        $stmt1->execute([':amount' => $amount, ':fromId' => $fromId]);

        $stmt2 = $pdo->prepare("UPDATE books SET available = available + :amount WHERE id = :toId");
        $stmt2->execute([':amount' => $amount, ':toId' => $toId]);

        $pdo->commit();
    } catch (Exception $e) { // ← PDOException — наследник Exception, так что ловится корректно
        $pdo->rollback();
        throw $e;
    }
}

// ❌ БЫЛО: echo findBooksByAuthor("' OR '1'='1"); → нельзя выводить массив через echo
// ✅ ИСПРАВЛЕНО: корректный вывод в HTML

$maliciousAuthor = "' OR '1'='1";
$booksByMalicious = findBooksByAuthor($maliciousAuthor);

echo "<h3>Поиск по потенциально опасному автору: " . htmlspecialchars($maliciousAuthor) . "</h3>";
if (empty($booksByMalicious)) {
    echo "<p>Нет книг с таким автором (как и ожидалось — инъекция не сработала!)</p>";
} else {
    foreach ($booksByMalicious as $book) {
        echo "<p>📖 <strong>" . htmlspecialchars($book['title']) . "</strong> — " . htmlspecialchars($book['author'] ?? 'неизвестен') . "</p>";
    }
}

// === ДОПОЛНИТЕЛЬНЫЙ ДЕМО-БЛОК: вывод всех доступных книг ===
echo "<h3>Все доступные книги:</h3>";
$availableBooks = getAllAvailableBooks();
if (empty($availableBooks)) {
    echo "<p>Нет доступных книг.</p>";
} else {
    foreach ($availableBooks as $book) {
        echo "<p>📚 ID {$book['id']}: <strong>" . htmlspecialchars($book['title']) . "</strong> (" . htmlspecialchars($book['author'] ?? 'автор не указан') . ") — " . ($book['available'] ? 'в наличии' : 'недоступна') . "</p>";
    }
}

// === Пример использования setBookAvailability ===
// setBookAvailability(1, false); // сделает книгу с id=1 недоступной

// === Пример использования transferStock (осторожно: меняет данные!) ===
// transferStock(1, 2, 1); // переместит 1 единицу со склада книги 1 на склад книги 2

// Вторая версия подключения (для демонстрации)
function getPdoConnection2(): PDO
{
    $env = 'dev'; // ← меняй на 'prod' для продакшена
    $username = 'mori';
    $password = 'fvthbrf900';
    $host = 'localhost';
    $dbname = 'library';

    try {
        return new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $e) {
        if ($env === 'dev') {
            die("<pre style='color:red;background:#ffecec;padding:10px;'>
❌ Ошибка подключения (DEV):
" . htmlspecialchars($e->getMessage()) . "
</pre>");
        } else {
            error_log("[PROD DB ERROR] " . $e->getMessage());
            http_response_code(500);
            die("Внутренняя ошибка сервера.");
        }
    }
}
?>