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

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $product_name = $product['name'];
    $parent_id = $product['parent_id'];

    // Получаем все товары с таким же именем и parent_id
    $sizes_query = "SELECT products.id, products.size, products.price FROM products 
                    JOIN categories ON products.category_id = categories.id 
                    WHERE products.name = '$product_name' 
                    AND categories.parent_id = $parent_id";
    $sizes_result = $mysqli->query($sizes_query);

    $sizes = [];
    while ($row = $sizes_result->fetch_assoc()) {
        $sizes[] = $row;
    }
} else {
    echo "<p>Товар не найден</p>";
    exit;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подробнее о товаре</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product-details.css">
    <script>
        function updatePrice(price) {
            document.getElementById('price').innerText = price + ' грн';
        }
    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <main>
        <section class="product-details">
            <h2>Подробнее о товаре</h2>
            <div class="product">
                <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Описание:</strong> <?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Цена:</strong> <span id="price"><?php echo number_format($product['price'], 2, '.', ''); ?> грн</span></p>
                <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <form action="cart.php" method="post">
                    <label for="size">Выберите размер:</label>
                    <select name="size" id="size" onchange="updatePrice(this.options[this.selectedIndex].getAttribute('data-price'))">
                        <?php foreach ($sizes as $size) : ?>
                            <option value="<?php echo htmlspecialchars($size['size'], ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo number_format($size['price'], 2, '.', ''); ?>">
                                <?php echo htmlspecialchars($size['size'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="submit">Добавить в корзину</button>
                </form>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>