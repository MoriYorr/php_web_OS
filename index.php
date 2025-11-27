<?php
$json = file_get_contents('file.json');
$data = json_decode($json, true);

function buildTree($data): array {
    $tree = [];
    foreach ($data as $item) {
        $parentId = $item['parent_id'] ?? null;
        $tree[$parentId][] = $item;
    }
    return $tree;
}

function printTreeHtml($tree, $parentId = null): void {
    if (empty($tree[$parentId])) {
        return;
    }

    echo "<ul>\n";
    foreach ($tree[$parentId] as $item) {
        $currentId = $item['file_id'];
        $hasChildren = !empty($tree[$currentId]);

        echo "<li>";
        if ($hasChildren) {
            echo "[DIR]   <strong>{$item['name']}</strong>"; 
            printTreeHtml($tree, $currentId); 
        } else {
            echo "[FILE] {$item['name']}"; 
        }
        echo "</li>\n";
    }
    echo "</ul>\n";
}

header('Content-Type: text/html; charset=utf-8');

$tree = buildTree($data);
echo "<html><head><title>File Tree</title></head><body>\n";
echo "<h2>File Structure</h2>\n";
printTreeHtml($tree);
echo "</body></html>\n";
?>