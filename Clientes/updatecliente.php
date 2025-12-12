<?php
include "../config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $c = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_POST) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $imagen_actual = $_POST['imagen_actual'];

    if ($_FILES['imagen']['name'] != "") {
        $imagen = $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "imagen/".$imagen);
    } else {
        $imagen = $imagen_actual;
    }

    $stmt = $conn->prepare("UPDATE clientes SET nombre=:nombre, email=:email, telefono=:telefono, direccion=:direccion, imagen=:imagen WHERE id=:id");
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":telefono", $telefono);
    $stmt->bindParam(":direccion", $direccion);
    $stmt->bindParam(":imagen", $imagen);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo "<script>alert('Cliente actualizado');window.location='readclientes.php';</script>";
}

include "../header.php";
?>
<h5><b><i class="fa fa-pencil"></i> Actualizar Cliente</b></h5>
</header>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $c['id'] ?>">
<input type="hidden" name="imagen_actual" value="<?= $c['imagen'] ?>">

<div class="mb-3"><label>Nombre</label><input class="form-control" type="text" name="nombre" value="<?= $c['nombre'] ?>" required></div>
<div class="mb-3"><label>Email</label><input class="form-control" type="email" name="email" value="<?= $c['email'] ?>" required></div>
<div class="mb-3"><label>Teléfono</label><input class="form-control" type="text" name="telefono" value="<?= $c['telefono'] ?>"></div>
<div class="mb-3"><label>Dirección</label><input class="form-control" type="text" name="direccion" value="<?= $c['direccion'] ?>"></div>

<div class="mb-3">
    <label>Imagen Actual</label><br>
    <img src="imagen/<?= $c['imagen'] ?>" width="120"><br><br>
    <input class="form-control" type="file" name="imagen" accept="image/*">
</div>

<button class="btn btn-primary">Actualizar</button>
<a href="readclientes.php" class="btn btn-secondary">Volver</a>
</form>

<?php include "../footer.php"; ?>
