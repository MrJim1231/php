<?php
// Подключение конфигурации для работы с базой данных
include('../config.php');

// Подключение к базе данных
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Проверка подключения к базе данных
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получаем id категории из URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем все подкатегории для этой категории
$subcategories_query = "SELECT id, name FROM categories WHERE parent_id = $category_id";
$subcategories_result = $mysqli->query($subcategories_query);

// Формируем список всех категорий, включая подкатегории
$all_categories = [$category_id]; // Сначала добавляем выбранную категорию
$subcategories = [];
while ($subcategory = $subcategories_result->fetch_assoc()) {
    $all_categories[] = $subcategory['id']; // Добавляем id подкатегорий
    $subcategories[] = $subcategory; // Добавляем информацию о подкатегориях для вывода
}

// Получаем товары, принадлежащие выбранной категории и её подкатегориям
$categories_list = implode(',', $all_categories); // Преобразуем массив в строку для SQL-запроса
$query = "SELECT * FROM products WHERE category_id IN ($categories_list)";
$result = $mysqli->query($query);

// Получаем информацию о категории
$category_query = "SELECT name FROM categories WHERE id = $category_id";
$category_result = $mysqli->query($category_query);
$category = $category_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары категории</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/category.css">
</head>
<body>
    <!-- Навигация -->
    <?php include('navbar.php'); ?>

    <!-- Основной контент -->
    <main>
        <section class="category-products">
            <?php
            // Проверка, есть ли товары в выбранной категории или её подкатегориях
            if ($result->num_rows > 0) {
                echo "<h2>Товары категории: " . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . " (ID: " . $category_id . ")</h2>";
                echo "<div class='products-list'>";
                
                // Выводим подкатегории
                if (count($subcategories) > 0) {
                    echo "<h3>Подкатегории:</h3><ul>";
                    foreach ($subcategories as $subcategory) {
                        echo "<li>" . htmlspecialchars($subcategory['name'], ENT_QUOTES, 'UTF-8') . " (ID: " . $subcategory['id'] . ")</li>";
                    }
                    echo "</ul>";
                }

                // Выводим товары
                while ($product = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<h3>" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                    echo "<p><strong>Категория:</strong> " . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . " (ID: " . $category_id . ")</p>";
                    
                    // Добавляем информацию о подкатегориях для товара
                    echo "<p><strong>Подкатегория:</strong> ";
                    $product_category_query = "SELECT c.name, c.id FROM categories c WHERE c.id = " . $product['category_id'];
                    $product_category_result = $mysqli->query($product_category_query);
                    $product_category = $product_category_result->fetch_assoc();
                    echo htmlspecialchars($product_category['name'], ENT_QUOTES, 'UTF-8') . " (ID: " . $product_category['id'] . ")</p>";
                    
                    echo "<p>" . htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<p><strong>Цена:</strong> " . number_format($product['price'], 2, '.', '') . " грн</p>";
                    echo "<p><strong>Размер:</strong> " . htmlspecialchars($product['size'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<p><strong>Наличие:</strong> " . ($product['availability'] ? 'В наличии' : 'Нет в наличии') . "</p>";
                    echo "<p><strong>Количество на складе:</strong> " . $product['quantity_in_stock'] . "</p>";
                    echo "<p><strong>Вес:</strong> " . $product['weight'] . " кг</p>";
                    echo "<img src='" . $product['image'] . "' alt='" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "' />";
                    echo "<a href='/myshop/public/product-details.php?id=" . $product['id'] . "' class='btn'>Подробнее</a>";
                    echo "</div>";
                }
                
                echo "</div>";
            } else {
                echo "<p>Товары не найдены для этой категории</p>";
            }
            ?>
        </section>
    </main>

    <!-- Подвал -->
    <?php include('footer.php'); ?>

    <!-- Закрытие соединения с базой данных -->
    <?php $mysqli->close(); ?>
</body>
</html>
<!-- work -->