<?php
$username = 'mori';  
$password = 'fvthbrf900';  

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=library;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("❌ Ошибка подключения: " . htmlspecialchars($e->getMessage()));
}

echo "newtext";
// === 1. Получить все доступные книги ===
$stmt = $pdo->query("SELECT * FROM books WHERE available = 1");
$availableBooks = $stmt->fetchAll();

// === 2. Книги после 2000 года ===
$stmt = $pdo->prepare("SELECT * FROM books WHERE pub_year > :year AND available = :available");
$stmt->execute(['year' => 2000, 'available' => 1]);
$modernBooks = $stmt->fetchAll();

// === 3. Добавить новую книгу ===
$newBook = [
    'title' => 'Атомные привычки',
    'author' => 'Джеймс Клир',
    'isbn' => '978-5-04-116503-7',
    'pub_year' => 2018
];

$stmt = $pdo->prepare(
    "INSERT INTO books (title, author, isbn, pub_year) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$newBook['title'], $newBook['author'], $newBook['isbn'], $newBook['pub_year']]);
$newBookId = $pdo->lastInsertId();

// === 4. Обновить список доступных книг (чтобы увидеть новую) ===
$stmt = $pdo->query("SELECT * FROM books WHERE available = 1");
$availableBooksAfterInsert = $stmt->fetchAll();

// === Вывод в HTML ===
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Работа с книгами в БД</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; color: #333; }
        h2 { color: #2c3e50; margin-top: 30px; }
        ul { padding-left: 20px; }
        li { margin: 6px 0; }
        .success { color: #27ae60; font-weight: bold; }
        .note { background: #e8f4fc; padding: 10px; border-left: 4px solid #3498db; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>📚 Демонстрация работы с таблицей `books`</h1>

    <!-- 1. Все доступные книги -->
    <h2>1. Все доступные книги</h2>
    <?php if ($availableBooks): ?>
        <ul>
            <?php foreach ($availableBooks as $book): ?>
                <li>
                    <strong><?= htmlspecialchars($book['title']) ?></strong>
                    <?php if ($book['author']): ?> — <?= htmlspecialchars($book['author']) ?><?php endif; ?>
                    <?php if ($book['pub_year']): ?> (<?= $book['pub_year'] ?>)<?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нет доступных книг.</p>
    <?php endif; ?>

    <!-- 2. Книги после 2000 года -->
    <h2>2. Книги, изданные после 2000 года</h2>
    <?php if ($modernBooks): ?>
        <ul>
            <?php foreach ($modernBooks as $book): ?>
                <li>
                    <?= htmlspecialchars($book['title']) ?> 
                    <?php if ($book['author']): ?> — <?= htmlspecialchars($book['author']) ?><?php endif; ?>
                    (<?= $book['pub_year'] ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нет книг после 2000 года.</p>
    <?php endif; ?>

    <!-- 3. Добавление новой книги -->
    <h2>3. Добавление новой книги</h2>
    <div class="note">
        ✅ Добавлена книга: <strong><?= htmlspecialchars($newBook['title']) ?></strong><br>
        ID новой записи: <span class="success"><?= $newBookId ?></span>
    </div>

    <!-- 4. Список после вставки -->
    <h2>4. Список доступных книг (после добавления)</h2>
    <ul>
        <?php foreach ($availableBooksAfterInsert as $book): ?>
            <li>
                <strong><?= htmlspecialchars($book['title']) ?></strong>
                <?php if ($book['author']): ?> — <?= htmlspecialchars($book['author']) ?><?php endif; ?>
                <?php if ($book['pub_year']): ?> (<?= $book['pub_year'] ?>)<?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>