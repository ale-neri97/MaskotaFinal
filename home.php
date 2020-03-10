<?php
// Get the 4 most recently added products
$stmt = $pdo->prepare('SELECT * FROM productos LIMIT 4');
$stmt->execute();
$recently_added_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Home')?>

<div class="featured">
    <h2>+KOTA</h2>
    <!-- <p>Essential gadgets for everyday use</p> -->
</div>
<div class="recentlyadded content-wrapper">
    <h2>Recently Added Products</h2>
    <div class="products">
        <?php foreach ($recently_added_products as $product): ?>
        <a href="index.php?page=product&id_producto=<?=$product['id_producto']?>" class="product">
            <img src="<?=$product['imagen']?>" width="200" height="200" alt="<?=$product['nombre']?>">
            <span class="name"><?=$product['nombre']?></span>
            <span class="price">
                &dollar;<?=$product['precio']?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>