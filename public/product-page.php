<?php
// Включаем файл для получения данных о товаре
include('product-details.php');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подробнее о товаре</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product-details.css">
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
            <p><strong>Наличие:</strong> <span id="availability"><?php echo $product['availability'] ? 'В наличии' : 'Нет в наличии'; ?></span></p>
            <p><strong>Количество на складе:</strong> <span id="stock"><?php echo $product['quantity_in_stock']; ?></span></p>
            <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">

            <form action="cart.php" method="post">
                <h4>Выберите размер комплекта:</h4>
                <div class="size-groups">
                    <?php
                    foreach ($sizes as $group_id => $group_sizes) :
                        foreach ($group_sizes as $data) :
                            $size = preg_replace('/\s*\(арт\d+\)/i', '', $data['size']);
                            $size = preg_replace('/\b50\*70\b|\b70\*70\b/', '', $size);
                            $size = trim($size);

                            if (strpos($data['size'], '50*70') !== false) {
                                ?>
                                <div class="size-group size-group-50x70" style="display: none;">
                                    <span class="size-btn"
                                          onclick="updatePrice('<?php echo number_format($data['price'], 2, '.', ''); ?>', '<?php echo $data['availability']; ?>', '<?php echo $data['quantity_in_stock']; ?>', this)">
                                        <?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <?php
                            }
                        endforeach;

                        foreach ($group_sizes as $data) :
                            $size = preg_replace('/\s*\(арт\d+\)/i', '', $data['size']);
                            $size = preg_replace('/\b50\*70\b|\b70\*70\b/', '', $size);
                            $size = trim($size);

                            if (strpos($data['size'], '70*70') !== false) {
                                ?>
                                <div class="size-group size-group-70x70" style="display: none;">
                                    <span class="size-btn"
                                          onclick="updatePrice('<?php echo number_format($data['price'], 2, '.', ''); ?>', '<?php echo $data['availability']; ?>', '<?php echo $data['quantity_in_stock']; ?>', this)">
                                        <?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <?php
                            }
                        endforeach;
                    endforeach;
                    ?>
                </div>

                <!-- Добавляем надпись и кнопки выбора размеров -->
                <h4>Выбор размера наволочек:</h4>
                <div class="btn-container">
                    <button class="size-btn-group size-btn-group-50x70" type="button" onclick="toggleSizeGroup('50x70')">50*70</button>
                    <button class="size-btn-group size-btn-group-70x70" type="button" onclick="toggleSizeGroup('70x70')">70*70</button>
                </div>

                <!-- Кнопка добавления в корзину теперь внизу -->
                <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
            </form>
        </div>
    </section>
</main>
<?php include('footer.php'); ?>

<script src="../assets/js/product-details.js"></script>
</body>
</html>
