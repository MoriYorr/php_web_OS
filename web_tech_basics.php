<?php
echo "Hello";
function dumpRequestInfo(): void{
    echo '<pre>';
    echo 'Метод запроса: ' . htmlspecialchars($_SERVER['REQUEST_METHOD']) . "\n";
    echo 'URI: ' . htmlspecialchars($_SERVER['REQUEST_URI']) . "\n";
    if (!empty($_GET)) {
    echo "GET-параметры:\n";
    echo htmlspecialchars(print_r($_GET, true)) . "\n";
}

if (!empty($_POST)) {
    echo "POST-параметры:\n";
    echo htmlspecialchars(print_r($_POST, true)) . "\n";
}
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
    echo 'Браузер: ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT']) . "\n";
}
    echo '</pre>';
}

dumpRequestInfo();
echo "Hello";
?>