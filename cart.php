<?php
// If the user clicked the add to cart button on the product page we can check for the form data
if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    // Prepare the SQL statement, we basically are checking if the product exists in our databaser
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id_producto = ?');
    $stmt->execute([$_POST['product_id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if ($product && $quantity > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            if (array_key_exists($product_id, $_SESSION['cart'])) {
                // Product exists in cart so just update the quantity
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                // Product is not in cart so add it
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
            // There are no products in cart, this will add the first product to cart
            $_SESSION['cart'] = array($product_id => $quantity);
        }
    }
    // Prevent form resubmission...
    header('location: index.php?page=cart');
    exit;

}
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Remove the product from the shopping cart
    unset($_SESSION['cart'][$_GET['remove']]);
}
// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Loop through the post data so we can update the quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'cantidad') !== false && is_numeric($v)) {
            $id = str_replace('cantidad-', '', $k);
            $quantity = (int)$v;
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    // Prevent form resubmission...
    header('location: index.php?page=cart');
    exit;
}

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id_producto IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['precio'] * (int)$products_in_cart[$product['id_producto']];
    }
}

// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
$errores = 0;
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try{
        $clientName = $_POST['nombre'];
        $newClient =  $pdo->prepare("INSERT INTO Clientes (nombre,fechas) VALUES(:name,NOW());");
        $newSell = $pdo->prepare("INSERT INTO Ventas (id_empleado,id_cliente,id_estacion,total,pagado,fechas) VALUES(:employee,:client,:station,:total,:paid,NOW());");
        $individualSell = $pdo->prepare("INSERT INTO Ventas_partida (id_venta,id_producto,cantidad,precio,fechas) VALUES(:sellId,:product,:quantity,:price,NOW());");
        $removeFromStock =  $pdo->prepare("UPDATE Productos SET cantidad = :cantidad WHERE id_producto = :id_producto");
        $pdo->beginTransaction();
        $newClient->execute(['name' => $clientName]);
        $clientId = $pdo->lastInsertId();
        $newSell->execute(['employee' => 1,'client' => $clientId,'station'=>1,'total'=>0,'paid'=>0]);
        $sellId = $pdo->lastInsertId();

        $newQuantity = $product['cantidad']-$products_in_cart[$product['id_producto']];
        foreach ($products as $product) {
            if($newQuantity<0)  $removeFromStock->execute(['dsa']);
            else $removeFromStock->execute(['cantidad'=>$newQuantity,'id_producto'=>$product['id_producto']]);
            $individualSell->execute(['sellId'=>$sellId,'product'=>$product['id_producto'],'quantity'=>$products_in_cart[$product['id_producto']],'price'=>(float)$product['precio']*$products_in_cart[$product['id_producto']]]);
        }
        
        $_SESSION['cart'] = array();
        $pdo->commit(); 
        header('Location: index.php?page=placeorder&id='.$clientId.'&id2='.$sellId);
    }catch(Exception $e){
        if($pdo->inTransaction()) $pdo->rollback(); //  Si fallo la insercion en la transaccion .. rollback
        $errores = 1;  // Mostramos el error en pantalla y matamos la ejecución
        var_dump($e);
    }
}
?>


<?=template_header('Cart')?>

<div class="cart content-wrapper">
    <h1>Shopping Cart</h1>
    <form action="index.php?page=cart" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No tienes productos en tu carrito de compras</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id:producto=<?=$product['id_producto']?>">
                            <img src="<?=$product['imagen']?>" width="50" height="50" alt="<?=$product['nombre']?>">
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=product&id_producto=<?=$product['id_producto']?>"><?=$product['nombre']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$product['id_producto']?>" class="remove">Remove</a>
                    </td>
                    <td class="price">&dollar;<?=$product['precio']?></td>
                    <td class="quantity">
                        <input type="number" name="cantidad-<?=$product['id_producto']?>" value="<?=$products_in_cart[$product['id_producto']]?>" min="1" max="<?=$product['cantidad']?>" placeholder="Cantidad" required>
                    </td>
                    <td class="price">&dollar;<?=$product['precio'] * $products_in_cart[$product['id_producto']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price">&dollar;<?=$subtotal?></span>
            <br>
            <span class="text">Ingresa tu nombre</span> <input type="text" name="nombre" id="nombreCliente" required>
        </div>
        <div class="buttons">
            <input type="submit" value="Update" name="update">
            <input type="submit" value="Place Order" name="placeorder">
        </div>
        <?php if($errores >0): ?>
        <div class="alert alert-danger" role="alert">
            Hubo un error en la compra
        </div>
        <?php endif ?>
    </form>
</div>

<?=template_footer()?>