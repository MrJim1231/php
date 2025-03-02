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

// Проверяем подключение
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// === 1. ДОБАВЛЕНИЕ КАТЕГОРИЙ ===
foreach ($xml->shop->categories->category as $category) {
    $category_id = (int)$category['id'];
    $category_name = $mysqli->real_escape_string((string)$category);
    $parent_id = (int)$category['parentId']; // Получаем родительскую категорию

    // Проверяем, есть ли родительская категория в базе
    if ($parent_id != 0) {
        $checkParentQuery = "SELECT id FROM categories WHERE id = $parent_id";
        $parentResult = $mysqli->query($checkParentQuery);

        // Если родительская категория не существует, добавляем её
        if ($parentResult->num_rows == 0) {
            // Допустим, вы хотите добавить родительскую категорию с названием "Неизвестная категория"
            $parentCategoryName = 'Неизвестная категория'; // Можно взять это из XML, если оно присутствует
            $mysqli->query("INSERT INTO categories (id, name, parent_id) VALUES ($parent_id, '$parentCategoryName', NULL)");
        }
    }

    // Проверяем, есть ли категория в базе
    $checkQuery = "SELECT id FROM categories WHERE id = $category_id";
    $result = $mysqli->query($checkQuery);

    if ($result->num_rows == 0) {
        // Вставляем новую категорию с родительским идентификатором
        $mysqli->query("INSERT INTO categories (id, name, parent_id) VALUES ($category_id, '$category_name', $parent_id)");
    }
}

// === 2. ДОБАВЛЕНИЕ ТОВАРОВ ===
foreach ($xml->shop->offers->offer as $offer) {
    $product_id = (int)$offer['id'];
    $category_id = (int)$offer->categoryId;
    $name = $mysqli->real_escape_string($offer->name);
    $description = $mysqli->real_escape_string($offer->description);
    $price = (float)$offer->price;
    $image = $mysqli->real_escape_string($offer->picture);

    // Извлекаем дополнительные параметры
    $availability = ($offer->available == 'true') ? 1 : 0; // Наличие товара
    $quantity_in_stock = (int)$offer->quantity_in_stock; // Количество на складе
    $weight = (float)$offer->weight; // Вес товара

    // Извлекаем размер из тега <param name="Размер">
    $size = '';
    foreach ($offer->param as $param) {
        if ((string)$param['name'] == 'Размер') {
            $size = $mysqli->real_escape_string((string)$param); // Сохраняем размер
            break;
        }
    }

    // Проверяем, есть ли товар в базе
    $checkQuery = "SELECT id FROM products WHERE id = $product_id";
    $result = $mysqli->query($checkQuery);

    if ($result->num_rows == 0) {
        // Вставляем новый товар с дополнительными параметрами
        $mysqli->query("INSERT INTO products (id, category_id, name, description, price, image, size, availability, quantity_in_stock, weight) 
                        VALUES ($product_id, $category_id, '$name', '$description', $price, '$image', '$size', $availability, $quantity_in_stock, $weight)");
    }
}

// Закрываем соединение с базой
$mysqli->close();
echo "Категории и товары добавлены!";
?>
