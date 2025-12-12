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
            <th>Imagen</th>
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
        $productos = $conn->query("
            SELECT p.*, pr.nombre AS proveedor_nombre 
            FROM productos p 
            LEFT JOIN proveedores pr 
            ON p.proveedor_id = pr.id_proveedor
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $prod) {

            // RUTA FINAL CORRECTA (funciona siempre)
            $img = "../imagen/" . ($prod['imagen'] ?: "noimage.png");

            echo "<tr>
                    <td>{$prod['id']}</td>

                    <td>
                        <img src='$img' width='60' height='60' 
                             style='object-fit:cover; border-radius:6px;'>
                    </td>

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
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" name="precio" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <select class="form-control" name="proveedor_id" required>
                            <option disabled selected>Selecciona un proveedor</option>

                            <?php
                            $proveedores = $conn->query("SELECT id_proveedor, nombre FROM proveedores")
                                                ->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($proveedores as $prov) {
                                echo "<option value='{$prov['id_proveedor']}'>" . 
                                      htmlspecialchars($prov['nombre']) . 
                                     "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input class="form-control" type="file" name="imagen" accept="image/*" onchange="previewImage(event)">
                        <img id="imagenPreview" src="#" style="display:none; max-width:100px; margin-top:10px;">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>

        </div>
    </div>
</div>

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
// GUARDAR PRODUCTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $proveedor_id = $_POST['proveedor_id'];

    $imagen = $_FILES['imagen']['name'] ?? "";

    // Ruta correcta desde /Productos/ hacia /imagen/
    $ruta = "../imagen/" . $imagen;

    if (!is_dir("../imagen")) mkdir("../imagen", 0755);

    if ($imagen) {
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    }

    $stmt = $conn->prepare("
        INSERT INTO productos (nombre, descripcion, precio, stock, imagen, proveedor_id)
        VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :proveedor_id)
    ");

    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':precio' => $precio,
        ':stock' => $stock,
        ':imagen' => $imagen,
        ':proveedor_id' => $proveedor_id
    ]);

    echo "<script>window.location='readproductos.php';</script>";
}
?>

<?php include "../footer.php"; ?>
