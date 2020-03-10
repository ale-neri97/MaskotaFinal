<?php
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id_producto'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id_producto = ?');
    $stmt->execute([$_GET['id_producto']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Simple error to display if the id for the product doesn't exists (array is empty)
        die ('¡El producto no existe!');
    }
} else {
    // Simple error to display if the id wasn't specified
    die ('¡El producto no existe!');
}
?>

<?=template_header('Product')?>

<div class="product content-wrapper">
    <img src="<?=$product['imagen']?>" width="500" height="500" alt="<?=$product['nombre']?>">
    <div>
        <h1 class="name"><?=$product['nombre']?></h1>
        <span class="price">
            &dollar;<?=$product['precio']?>
        </span>
        <form action="index.php?page=cart" method="post">
            <input type="number" name="quantity" value="1" min="1" max="<?=$product['cantidad']?>" placeholder="Cantidad" required>
            <input type="hidden" name="product_id" value="<?=$product['id_producto']?>">
            <input type="submit" value="Agregar al carrito">
        </form>
    </div>
</div>

<?=template_footer()?>