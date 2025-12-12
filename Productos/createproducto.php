<?php
if ($_POST) {
    $nombre = (isset($_POST['nombre']) ? $_POST['nombre'] : "");
    $descripcion = (isset($_POST['descripcion']) ? $_POST['descripcion'] : "");
    $precio = (isset($_POST['precio']) ? $_POST['precio'] : "");
    $stock = (isset($_POST['stock']) ? $_POST['stock'] : "");
    $proveedor_id = (isset($_POST['proveedor_id']) ? $_POST['proveedor_id'] : "");
    $imagen = (isset($_FILES['imagen']['name']) ? $_FILES['imagen']['name'] : "");
    $ruta = "imagen/" . $_FILES['imagen']['name'];
    $resultado = move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    try {
        $stmt = $conn->prepare("INSERT INTO productos(id, nombre, descripcion, precio, stock, imagen, proveedor_id) VALUES(null,:nombre,:descripcion,:precio,:stock,:imagen,:proveedor_id)");
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":proveedor_id", $proveedor_id);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">
  Producto agregado correctamente <a href="readproducto.php" class="alert-link">Volver a productos</a>.
</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
  Error al agregar el producto <a href="readproducto.php" class="alert-link">Intenta nuevamente</a>.
</div>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- Modal de Crear Producto -->
<div class="modal" id="create" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title center" id="modalLoginLabel"><i class="bi bi-box-seam"></i> AGREGAR PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <i class="bi bi-pencil-fill"></i>
                            <label for="inputNombre" class="form-label"> Nombre del Producto</label>
                            <input type="text" class="form-control" id="inputNombre" name="nombre" placeholder="Ingresa el nombre del producto" required />
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-card-text"></i>
                            <label for="inputDescripcion" class="form-label"> Descripción</label>
                            <textarea class="form-control" id="inputDescripcion" name="descripcion" rows="3" placeholder="Describe el producto" required></textarea>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-currency-dollar"></i>
                            <label for="inputPrecio" class="form-label"> Precio</label>
                            <input type="number" step="0.01" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required />
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-boxes"></i>
                            <label for="inputStock" class="form-label"> Stock</label>
                            <input type="number" class="form-control" id="inputStock" name="stock" placeholder="Cantidad en inventario" required />
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-people-fill"></i>
                            <label for="inputProveedor" class="form-label"> Proveedor</label>
                            <select class="form-control" id="inputProveedor" name="proveedor_id" required>
                                <option value="" disabled selected>Selecciona un proveedor</option>
                                <?php
                                $proveedores = $conn->query("SELECT id, nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($proveedores as $proveedor) {
                                    echo '<option value="' . $proveedor['id'] . '">' . $proveedor['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-image"></i>
                            <label for="inputImage" class="form-label"> Imagen</label>
                            <input type="file" class="form-control" name="imagen" id="imagen" accept="image/" />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy2-fill"></i>
                                Guardar
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>