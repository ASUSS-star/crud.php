<?php
include "../config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre = $result['nombre'];
    $descripcion = $result['descripcion'];
    $precio = $result['precio'];
    $stock = $result['stock'];
    $imagenActual = $result['imagen'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Manejo de la imagen
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = $_FILES['imagen']['name'];
        $rutaTemporal = $_FILES['imagen']['tmp_name'];
        $directorioDestino = "imagen/" . $nombreImagen;

        // Mover la imagen al directorio
        move_uploaded_file($rutaTemporal, $directorioDestino);
    } else {
        $directorioDestino = $imagenActual; // Usar la imagen anterior si no se subió una nueva
    }

    // Actualizar producto
    $stmt = $conn->prepare("
        UPDATE productos
        SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen 
        WHERE id = :id
    ");
    $stmt->bindValue(":nombre", $nombre);
    $stmt->bindValue(":descripcion", $descripcion);
    $stmt->bindValue(":precio", $precio);
    $stmt->bindValue(":stock", $stock);
    $stmt->bindValue(":imagen", $directorioDestino);
    $stmt->bindValue(":id", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Producto actualizado correctamente.');
                window.location.href = 'readproductos.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al actualizar el producto.');
                window.location.href = 'readproductos.php';
              </script>";
    }
}

include "header.php";
?>
<h5><b><i class="fa fa-pencil"></i> Actualizar Producto</b></h5>
</header>
<div class="row">
    <div class="col-md-2 text-right">
        <h1><a href="readproductos.php" class="btn btn-info">Regresar</a></h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo $descripcion; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" class="form-control" id="precio" name="precio" value="<?php echo $precio; ?>" required>
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen Actual</label>
            <img src="imagen/<?php echo $imagenActual; ?>" alt="Imagen del Producto" style="max-width: 150px; display: block; margin-bottom: 10px;">
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<?php include "../footer.php"; ?>
