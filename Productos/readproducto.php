<?php
include "../config.php";
include "../header.php";
?>

<h5><b><i class="bi bi-box-seam"></i> Lista de Productos</b></h5>

<!-- Botón para abrir modal -->
<div class="row mb-3">
    <div class="col-md-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProducto">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </button>
    </div>
</div>

<!-- Tabla de productos -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Proveedor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $productos = $conn->query("SELECT p.*, pr.nombre AS proveedor_nombre FROM productos p 
                                   LEFT JOIN proveedores pr ON p.proveedor_id = pr.id_proveedor")
                          ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $prod) {
            echo "<tr>
                    <td>{$prod['id']}</td>
                    <td>" . htmlspecialchars($prod['nombre']) . "</td>
                    <td>" . htmlspecialchars($prod['descripcion']) . "</td>
                    <td>{$prod['precio']}</td>
                    <td>{$prod['stock']}</td>
                    <td>" . htmlspecialchars($prod['proveedor_nombre']) . "</td>
                    <td>
                        <a href='updateproducto.php?id={$prod['id']}' class='btn btn-warning btn-sm'>Editar</a>
                        <a href='deleteproducto.php?id={$prod['id']}' class='btn btn-danger btn-sm' 
                           onclick=\"return confirm('¿Seguro que deseas eliminar este producto?');\">Borrar</a>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<!-- Modal de Crear Producto -->
<div class="modal fade" id="createProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoLabel"><i class="bi bi-box-seam"></i> AGREGAR PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="inputNombre" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="inputNombre" name="nombre" placeholder="Ingresa el nombre del producto" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="inputDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="inputDescripcion" name="descripcion" rows="3" placeholder="Describe el producto" required></textarea>
                    </div>

                    <!-- Precio -->
                    <div class="mb-3">
                        <label for="inputPrecio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required>
                    </div>

                    <!-- Stock -->
                    <div class="mb-3">
                        <label for="inputStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="inputStock" name="stock" placeholder="Cantidad en inventario" required>
                    </div>

                    <!-- Proveedor -->
                    <div class="mb-3">
                        <label for="inputProveedor" class="form-label">Proveedor</label>
                        <select class="form-control" id="inputProveedor" name="proveedor_id" required>
                            <option value="" disabled selected>Selecciona un proveedor</option>
                            <?php
                            $proveedores = $conn->query("SELECT id_proveedor, nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($proveedores as $proveedor) {
                                echo '<option value="' . $proveedor['id_proveedor'] . '">' . htmlspecialchars($proveedor['nombre']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-3">
                        <label for="inputImagen" class="form-label">Imagen</label>
                        <input class="form-control" type="file" id="inputImagen" name="imagen" accept="image/*" onchange="previewImage(event)">
                        <img id="imagenPreview" src="#" alt="Vista previa" style="display:none; max-width: 100px; margin-top: 10px;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para vista previa de imagen -->
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('imagenPreview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<?php
// Procesar el envío del formulario del modal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $proveedor_id = $_POST['proveedor_id'] ?? "";
    $imagen = $_FILES['imagen']['name'] ?? "";

    $ruta = "imagen/" . $imagen;
    if (!is_dir("imagen")) mkdir("imagen", 0755);

    if ($imagen) move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

    try {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen, proveedor_id) 
                                VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :proveedor_id)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':stock' => $stock,
            ':imagen' => $imagen,
            ':proveedor_id' => $proveedor_id
        ]);

        echo "<script>window.location='readproductos.php';</script>";
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<?php include "../footer.php"; ?>
