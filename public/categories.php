<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории товаров</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/categories.css">
</head>
<body>
    
    <!-- navbar.php -->
    <?php include('navbar.php'); ?>

    <!-- Основной контент -->
    <main>
        <section class="categories">
            <h2>Категории товаров</h2>
            <div class="categories-list">
                <?php
                include('../config.php');

                $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
                if ($mysqli->connect_error) {
                    die("Ошибка подключения: " . $mysqli->connect_error);
                }

                // Запрос для получения всех категорий, кроме исключенных
                $query = "SELECT * FROM categories WHERE name NOT IN ('Неизвестная категория', 'Півтора-спальний', 'Двоспальний', 'Євро', 'Сімейний', 'Індивідуальний пошив', 'Іедивідуальний пошив')";
                $result = $mysqli->query($query);

                if ($result->num_rows > 0) {
                    while ($category = $result->fetch_assoc()) {
                        echo "<div class='category'>";
                        echo "<h3>" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                        echo "<p>" . htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<img src='" . $category['image'] . "' alt='" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "' />";
                        echo "<a href='/myshop/public/category.php?id=" . $category['id'] . "' class='btn'>Смотреть товары</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Категории не найдены</p>";
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
