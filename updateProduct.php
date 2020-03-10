<?php  
if ( !empty($_GET['id_producto'])) { 
    $id_producto = $_REQUEST['id_producto']; 
} 
 
if (!empty($_POST)) { 
    // keep track validation errors 
    $idError=null; 
    $nombreError = null; 
    $descError = null; 
    $cantError = null; 
    $precioError  = null; 
    $categoriaError  = null; 
    $transactionError =false; 
 
     
     
    // keep track post values 
    $id_producto=$_POST['id_producto']; 
    $nombre = $_POST['nombre']; 
    $descripcion = $_POST['descripcion']; 
    $cantidad = $_POST['cantidad']; 
    $precio = $_POST['precio']; 
    $id_categoria =$_POST['id_categoria']; 
     
    /// validate input 
    $valid = true; 
 
    if (empty($id_producto)) { 
        $idError = 'Porfavor ingresa un id de producto'; 
        $valid = false; 
    } 
     
    if (empty($nombre)) { 
        $nombreError = 'Porfavor ingresa un nombre de producto'; 
        $valid = false; 
    } 
 
    if (empty($descripcion)) { 
        $descError = 'Porfavor ingresa una descripción'; 
        $valid = false; 
    }		 
     
    if (empty($cantidad)) { 
        $cantError = 'Porfavor ingresa la cantidad'; 
        $valid = false; 
    }	 
     
    if (empty($precio)) { 
        $precioError = 'Porfavor ingresa el precio'; 
        $valid = false; 
    }	 
 
 
    if (empty($id_categoria)) { 
        $categoriaError = 'Selecciona una categoría'; 
        $valid = false; 
    }	 
     
    // update data 
    if ($valid) { 
        
        try{ 
            $pdo->beginTransaction(); 
            $sql2 = "UPDATE productos set id_categoria=:category, nombre = :name, descripcion=:description, cantidad=:quantity, precio=:price  WHERE id_producto = :id"; 
            $stmt = $pdo->prepare($sql2); 
            $stmt->execute(['category'=> $id_categoria,'name'=>$nombre, 'description'=>$descripcion, 'quantity'=>$cantidad,'price'=>$precio, 'id'=>$id_producto]); 
            // $stmt->debugDumpParams(); 
            echo '<script type="text/javascript">'; 
            echo 'setTimeout(function () { swal("¡ÉXITO!","Se ha actualizado el producto","success");'; 
            echo '}, 500);</script>'; 
            $pdo->commit(); 
        } 
        catch(Exception $e){ 
            $pdo->rollback(); 
            // $stmt->debugDumpParams(); 
            echo '<script type="text/javascript">'; 
            echo 'setTimeout(function () { swal("¡ERROR!","El producto no pudo ser actualizado","error");'; 
            echo '}, 500);</script>'; 
            throw $e;  
 
        } 
        // it takes me to the stock page, once I updated a product 
        //header('location: index.php?page=stock'); 
    } 
}  
 
else { 
     
    $sql = "SELECT * FROM productos where id_producto = ?"; 
    $q = $pdo->prepare($sql); 
    $q->execute(array($id_producto)); 
    $data = $q->fetch(PDO::FETCH_ASSOC); 
    $id_producto = $data['id_producto']; 
    $nombre = $data['nombre']; 
    $descripcion = $data['descripcion']; 
    $cantidad = $data['cantidad']; 
    $precio = $data['precio']; 
    $id_categoria = $data['id_categoria']; 
    // $q->debugDumpParams(); 
    // $q=null; 
    // $pdo=null; 
    // $sql=null; 
} 
 
 
?> 
 
<?=template_header('Update product')?> 
 
    <div class="container"> 
        <div class="span10 offset1"> 
            <div class="row"> 
                <h3>Actualizar producto</h3> 
                 
            </div> 
         
                <form class="form-horizontal" action="index.php?page=updateProduct" method="post"> 
                <div class="control-group <?php echo !empty($idError)?'error':'';?>"> 
 
					    <label class="control-label">id</label> 
					    <div class="controls"> 
					      	<input name="id_producto" type="text" readonly placeholder="id" value="<?php echo !empty($id_producto)?$id_producto:''; ?>"> 
					      	<?php if (!empty($idError)): ?> 
					      		<span class="help-inline"><?php echo $idError;?></span> 
					      	<?php endif; ?> 
					    </div> 
					  </div> 
                   
                  <div class="control-group <?php echo !empty($nombreError)?'error':'';?>"> 
                   
                    <label class="control-label">Nombre</label> 
                    <div class="controls"> 
                          <input name="nombre" type="text" placeholder="nombre" value="<?php echo !empty($nombre)?$nombre:'';?>"> 
                          <?php if (!empty($nombreError)): ?> 
                              <span class="help-inline"><?php echo $nombreError;?></span> 
                          <?php endif;?> 
                    </div> 
                  </div> 
 
                  <div class="control-group <?php echo !empty($descError)?'error':'';?>"> 
                   
                    <label class="control-label">Descripción</label> 
                    <div class="controls"> 
                          <input name="descripcion" type="text" placeholder="Descripción" value="<?php echo !empty($descripcion)?$descripcion:'';?>"> 
                          <?php if (!empty($descError)): ?> 
                              <span class="help-inline"><?php echo $descError;?></span> 
                          <?php endif;?> 
                    </div> 
                  </div> 
 
                  <div class="control-group <?php echo !empty($cantError)?'error':'';?>"> 
                   
                  <label class="control-label">Cantidad</label> 
                  <div class="controls"> 
                        <input name="cantidad" type="text" placeholder="Cantidad" value="<?php echo !empty($cantidad)?$cantidad:'';?>"> 
                        <?php if (!empty($cantError)): ?> 
                            <span class="help-inline"><?php echo $cantError;?></span> 
                        <?php endif;?> 
                  </div> 
                </div> 
 
                <div class="control-group <?php echo !empty($precioError)?'error':'';?>"> 
                   
                  <label class="control-label">Precio</label> 
                  <div class="controls"> 
                        <input name="precio" type="text" placeholder="Precio" value="<?php echo !empty($precio)?$precio:'';?>"> 
                        <?php if (!empty($precioError)): ?> 
                            <span class="help-inline"><?php echo $precioError;?></span> 
                        <?php endif;?> 
                  </div> 
                </div> 
 
                <div class="control-group <?php echo !empty($categoriaError)?'error':'';?>"> 
					    	<label class="control-label">Categoria</label> 
					    	<div class="controls"> 
                            	<select name ="id_categoria"> 
                                    <option value="">Selecciona una categoria</option> 
                                        <?php 
					   						 
					   						$query = 'SELECT * FROM categorias'; 
	 				   						foreach ($pdo->query($query) as $row) { 
	 				   							if ($row['id_categoria']==$id_categoria) 
                        	   						echo "<option selected value='" . $row['id_categoria'] . "'>" . $row['tipo'] . "</option>"; 
                        	   					else 
                        	   						echo "<option value='" . $row['id_categoria'] . "'>" . $row['tipo'] . "</option>"; 
					   						} 
					   						 
					  					?> 
                                                     
                                </select> 
					      	 
					    	</div> 
					</div> 
 
 
                  <div class="form-actions"> 
                        <br> 
                      <button type="submit" class="btn btn-success">Actualizar</button> 
                      <a class="btn" href="index.php?page=stock">Regresar</a> 
                    </div> 
                </form> 
            </div> 
             
</div> <!-- /container --> 
 
<?=template_footer()?>