<?php
// Get the 20 most recently added products

$stmt = $pdo->prepare(
'SELECT V.id_venta, C.nombre,V.fechas,SUM(P.precio) AS total 
FROM Ventas V
JOIN Clientes C ON V.id_cliente=C.id_cliente
JOIN Ventas_partida P ON V.id_venta=P.id_venta
GROUP BY V.id_venta
ORDER BY V.fechas DESC
LIMIT 20
;');

$stmt->execute();
$last_sells = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Last Sells')?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <!--Table-->
            <table  class="table table-hover table-striped">
            <!--Table head-->
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Date</th>
                    <th scope="col">Client</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Items</th>
                </tr>
            </thead>
            <!--Table head-->
            <!--Table body-->
            <tbody>
                <?php if (empty($last_sells)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Aún no hay ventas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($last_sells as $sell): ?>
                    <tr>
                        <?php echo '<th scope="row">'.$sell['id_venta'].'</th>' ?>
                        <?php echo '<td>'.$sell['fechas'].'</td>' ?>
                        <?php echo '<td>'.$sell['nombre'].'</td>' ?>
                        <?php echo '<td>$'.$sell['total'].'</td>' ?>
                        <td><button type="button" class="btn btn-outline"><i class="far fa-eye"></i></button></td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!--Table body-->
            </table>
            <!--Table-->
        </div>
    </div>
</div>

<?=template_footer()?>