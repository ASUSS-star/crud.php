<?php
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $empresa = $_POST['empresa'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("
        INSERT INTO proveedores(nombre, empresa, telefono, email)
        VALUES(:nombre, :empresa, :telefono, :email)
    ");
    $stmt->bindValue(":nombre", $nombre);
    $stmt->bindValue(":empresa", $empresa);
    $stmt->bindValue(":telefono", $telefono);
    $stmt->bindValue(":email", $email);

    if ($stmt->execute()) {
        echo "<script>alert('Proveedor registrado correctamente.'); window.location.href='readproveedores.php';</script>";
    } else {
        echo "<script>alert('Error al registrar.');</script>";
    }
}

include "../header.php";
?>

<h5><b><i class="fa fa-plus"></i> Registrar Proveedor</b></h5>
</header>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Empresa</label>
        <input type="text" class="form-control" name="empresa" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" class="form-control" name="telefono" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
    </div>
    <button type="submit" class="btn btn-primary">Registrar</button>
</form>

<?php include "../footer.php"; ?>
