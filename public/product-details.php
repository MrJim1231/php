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
$query = "SELECT products.*, categories.name AS category_name FROM products
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
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Описание:</strong> <?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Цена:</strong> <?php echo number_format($product['price'], 2, '.', '') . " грн"; ?></p>
                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" />
            </div>
        </section>
    </main>

    <!-- Подвал -->
    <?php include('footer.php'); ?>

</body>
</html>
