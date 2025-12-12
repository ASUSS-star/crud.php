<?php
if ($_POST) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $imagen = $_FILES['imagen']['name'];
    $tmp = $_FILES['imagen']['tmp_name'];

    // RUTA CORREGIDA
    if ($imagen != "") {
        move_uploaded_file($tmp, "../imagen/" . $imagen);
    }

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, direccion, imagen)
    VALUES (:nombre, :email, :telefono, :direccion, :imagen)");

    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":telefono", $telefono);
    $stmt->bindParam(":direccion", $direccion);
    $stmt->bindParam(":imagen", $imagen);
    $stmt->execute();

    echo "<script>alert('Cliente agregado');window.location='readclientes.php';</script>";
}
?>

<div class="modal" id="create">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header">
    <h5><i class="bi bi-person-plus-fill"></i> Agregar Cliente</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="post" enctype="multipart/form-data">
<div class="modal-body">
    <div class="mb-3"><label>Nombre</label><input type="text" class="form-control" name="nombre" required></div>
    <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="email" required></div>
    <div class="mb-3"><label>Teléfono</label><input type="text" class="form-control" name="telefono"></div>
    <div class="mb-3"><label>Dirección</label><input type="text" class="form-control" name="direccion"></div>
    <div class="mb-3"><label>Imagen</label><input type="file" class="form-control" name="imagen" accept="image/*"></div>
</div>

<div class="modal-footer">
<button class="btn btn-primary" type="submit">Guardar</button>
<button class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
</div>
</form>

</div></div></div>
