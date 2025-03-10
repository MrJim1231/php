<?php
// URL файла YML
$url = "https://backend.mydrop.com.ua/vendor/api/export/products/prom/yml?public_api_key=7cbe3718003f120a0fa58cc327e6bdd508667edf&price_field=price&param_name=%D0%A0%D0%B0%D0%B7%D0%BC%D0%B5%D1%80&stock_sync=true&category_id=17670&platform=prom&file_format=yml&use_import_ids=false&with_hidden=false";

// Загружаем YML-файл
$xml = simplexml_load_file($url);

if (!$xml) {
    die("Ошибка загрузки YML");
}

// Подключаем базу данных
include('../config.php');
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// === ДОБАВЛЕНИЕ КАТЕГОРИЙ ===
foreach ($xml->shop->categories->category as $category) {
    $category_id = (string)$category['id'];
    $category_name = $mysqli->real_escape_string((string)$category);
    $parent_id = isset($category['parentId']) ? (string)$category['parentId'] : null;

    $checkQuery = "SELECT id FROM categories WHERE id = '$category_id'";
    $result = $mysqli->query($checkQuery);

    if ($result->num_rows == 0) {
        $mysqli->query("INSERT INTO categories (id, name, parent_id) VALUES ('$category_id', '$category_name', " . ($parent_id ? "'$parent_id'" : "NULL") . ")");
    }
}

// === ДОБАВЛЕНИЕ ТОВАРОВ ===
$totalProducts = 0;

foreach ($xml->shop->offers->offer as $offer) {
    $product_id = (string)$offer['id'];
    $group_id = isset($offer['group_id']) ? (string)$offer['group_id'] : null;
    $category_id = (string)$offer->categoryId;
    $name = $mysqli->real_escape_string($offer->name);
    $description = $mysqli->real_escape_string($offer->description);
    $price = (float)$offer->price;
    $image = $mysqli->real_escape_string($offer->picture);
    $availability = ($offer['available'] == 'true') ? 1 : 0;
    $quantity_in_stock = isset($offer->quantity_in_stock) ? (int)$offer->quantity_in_stock : 0;
    $weight = isset($offer->weight) ? (float)$offer->weight : 0.0;

    // Ищем параметр "Размер"
    $size = '';
    foreach ($offer->param as $param) {
        if ((string)$param['name'] == 'Размер') {
            $size = $mysqli->real_escape_string((string)$param);
            break;
        }
    }

    // Проверяем, есть ли товар в базе
    $checkQuery = "SELECT id FROM products WHERE id = '$product_id'";
    $result = $mysqli->query($checkQuery);

    if ($result->num_rows == 0) {
        $mysqli->query("INSERT INTO products (id, group_id, category_id, name, description, price, image, size, availability, quantity_in_stock, weight) 
                        VALUES ('$product_id', " . ($group_id ? "'$group_id'" : "NULL") . ", '$category_id', '$name', '$description', $price, '$image', '$size', $availability, $quantity_in_stock, $weight)");
        $totalProducts++;
    }
}

$mysqli->close();

echo "Общее количество товаров: " . count($xml->shop->offers->offer) . "<br>";
echo "Добавлено в базу: " . $totalProducts . "<br>";
echo "Категории и товары обновлены!";
?>
