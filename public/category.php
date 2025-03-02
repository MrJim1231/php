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

// Получаем товары, принадлежащие этой категории
$query = "SELECT * FROM products WHERE category_id = $category_id";
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
            // Проверка, есть ли товары в выбранной категории
            if ($result->num_rows > 0) {
                echo "<h2>Товары категории: " . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</h2>";
                echo "<div class='products-list'>";
                
                // Выводим товары
                while ($product = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<h3>" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                    echo "<p>" . htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<p>Цена: " . number_format($product['price'], 2, '.', '') . " грн</p>";
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
