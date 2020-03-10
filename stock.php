<?php 
// The current page, in the URL this will appear as index.php?page=products&p=1, index.php?page=products&p=2, etc... 
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1; 
// Select products ordered by the date added 
$stmt = $pdo->prepare('SELECT * FROM productos'); 
// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT 
$stmt->execute(); 
// Fetch the products from the database and return the result as an Array 
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); 
 
// Get the total number of products 
$total_products = $pdo->query('SELECT * FROM productos')->rowCount(); 
?> 
 
<?=template_header('Stock')?> 
 
<div class="products content-wrapper"> 
    <h1>Inventario</h1> 
    <p><?=$total_products?> Productos</p> 
    <div class="products-wrapper"> 
        <?php foreach ($products as $product): ?> 
        <a href="index.php?page=updateProduct&id_producto=<?=$product['id_producto']?>" class="product"> 
            <img src="<?=$product['imagen']?>" width="200" height="200" alt="<?=$product['nombre']?>"> 
            <span class="name"><?=$product['nombre']?></span> 
            <span class="price"> 
                &dollar;<?=$product['precio']?> 
            </span> 
        </a> 
        <?php endforeach; ?> 
    </div> 
</div>