<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/products.css">
</head>
<body>
    
    <!-- navbar.php -->
    <?php include('navbar.php'); ?>

    <!-- Основной контент -->
    <main>
        <!-- Секция с количеством товаров -->
        <section class="product-count">
            <?php
            include('../config.php');

            $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

            if ($mysqli->connect_error) {
                die("Ошибка подключения: " . $mysqli->connect_error);
            }

            // Запрос для подсчета всех товаров
            $countQuery = "SELECT COUNT(*) AS total_products FROM products";
            $countResult = $mysqli->query($countQuery);
            $totalProducts = $countResult->fetch_assoc()['total_products'];

            echo "<h1>Всего товаров в магазине: $totalProducts</h1>";

            $mysqli->close();
            ?>
        </section>

        <!-- Секция с продуктами -->
        <section class="featured-products">
            <h2>Рекомендуемые товары</h2>
            <div class="products-list">
                <?php
                // Повторно подключаемся к базе данных для получения продуктов
                $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

                if ($mysqli->connect_error) {
                    die("Ошибка подключения: " . $mysqli->connect_error);
                }

                $query = "SELECT products.*, categories.name AS category_name FROM products
                          JOIN categories ON products.category_id = categories.id
                          LIMIT 6";
                $result = $mysqli->query($query);

                if ($result->num_rows > 0) {
                    while ($product = $result->fetch_assoc()) {
                        echo "<div class='product'>";
                        echo "<h3>" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                        echo "<p>" . htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p>Цена: " . number_format($product['price'], 2, '.', '') . " грн</p>";
                        echo "<p>Категория: " . htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<img src='" . $product['image'] . "' alt='" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "' />";
                        echo "<a href='/myshop/public/product-details.php?id=" . $product['id'] . "' class='btn'>Подробнее</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Товары не найдены</p>";
                }

                $mysqli->close();
                ?>
            </div>
        </section>
    </main>

    <!-- Подвал -->
    <?php include('footer.php'); ?>

</body>
</html>
