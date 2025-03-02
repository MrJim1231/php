<?php
// Подключение конфигурации
include('../config.php');

// Подключение к базе данных
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Проверка подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получаем id товара из URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем информацию о товаре
$query = "SELECT products.*, categories.name AS category_name, categories.parent_id 
          FROM products
          JOIN categories ON products.category_id = categories.id
          WHERE products.id = $product_id";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $product_name = $mysqli->real_escape_string($product['name']);
    $parent_id = $product['parent_id'];

    // Получаем все товары с таким же именем и parent_id, включая group_id
    $sizes_query = "SELECT products.id, products.size, products.price, products.availability, products.quantity_in_stock, products.group_id
                    FROM products 
                    JOIN categories ON products.category_id = categories.id 
                    WHERE products.name = '$product_name' 
                    AND categories.parent_id = $parent_id";
    $sizes_result = $mysqli->query($sizes_query);

    $sizes = [];
    while ($row = $sizes_result->fetch_assoc()) {
        $sizes[$row['group_id']][] = $row; // Группируем по group_id
    }
} else {
    echo "<p>Товар не найден</p>";
    exit;
}

$mysqli->close();
?>
