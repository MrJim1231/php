<?php
// Подключение конфигурации для работы с базой данных
include('../config.php');

// Подключение к базе данных
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Проверка подключения к базе данных
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

// Проверяем, есть ли товар с таким id
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Если товар не найден
    echo "<p>Товар не найден</p>";
    exit;
}

// Получаем все товары с таким же именем из других подкатегорий
$product_name = $product['name']; // Извлекаем имя товара
$parent_id = $product['parent_id']; // Получаем parent_id текущей категории товара
$related_query = "SELECT products.*, categories.name AS category_name FROM products 
                  JOIN categories ON products.category_id = categories.id 
                  WHERE products.name = '$product_name' 
                  AND categories.parent_id = $parent_id 
                  AND products.id != $product_id"; // Исключаем текущий товар
$related_result = $mysqli->query($related_query);

// Закрытие соединения с базой данных
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подробнее о товаре</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/product-details.css">
</head>
<body>
    <!-- Навигация -->
    <?php include('navbar.php'); ?>

    <!-- Основной контент -->
    <main>
        <section class="product-details">
            <h2>Подробнее о товаре</h2>
            <div class="product">
                <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><strong>Категория:</strong> <?php echo isset($product['category_name']) ? htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') : 'Категория не найдена'; ?></p>
                <p><strong>Описание:</strong> <?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Цена:</strong> <?php echo number_format($product['price'], 2, '.', '') . " грн"; ?></p>
                <p><strong>Размер:</strong> <?php echo htmlspecialchars($product['size'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Наличие:</strong> <?php echo $product['availability'] ? 'В наличии' : 'Нет в наличии'; ?></p>
                <p><strong>Количество на складе:</strong> <?php echo $product['quantity_in_stock']; ?></p>
                <p><strong>Вес:</strong> <?php echo $product['weight'] . ' кг'; ?></p>
                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" />
            </div>

            <!-- Похожие товары -->
            <h3>Похожие товары</h3>
            <div class="related-products">
                <?php
                if ($related_result->num_rows > 0) {
                    while ($related_product = $related_result->fetch_assoc()) {
                        // Используем такую же верстку для похожих товаров
                        echo "<div class='product'>";
                        echo "<h3>" . htmlspecialchars($related_product['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                        echo "<p><strong>Категория:</strong> " . htmlspecialchars($related_product['category_name'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p><strong>Описание:</strong> " . htmlspecialchars($related_product['description'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p><strong>Цена:</strong> " . number_format($related_product['price'], 2, '.', '') . " грн</p>";
                        echo "<p><strong>Размер:</strong> " . htmlspecialchars($related_product['size'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p><strong>Наличие:</strong> " . ($related_product['availability'] ? 'В наличии' : 'Нет в наличии') . "</p>";
                        echo "<p><strong>Количество на складе:</strong> " . $related_product['quantity_in_stock'] . "</p>";
                        echo "<p><strong>Вес:</strong> " . $related_product['weight'] . " кг</p>";
                        echo "<img src='" . $related_product['image'] . "' alt='" . htmlspecialchars($related_product['name'], ENT_QUOTES, 'UTF-8') . "' />";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Нет похожих товаров.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Подвал -->
    <?php include('footer.php'); ?>

</body>
</html>
