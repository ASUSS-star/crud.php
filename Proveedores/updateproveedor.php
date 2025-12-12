<?php
include "../config.php";
include "../header.php";

$proveedor = null;
$error = null;

// Verificar que llegue el id_proveedor
if (isset($_GET['id_proveedor'])) {
    $id_proveedor = (int)$_GET['id_proveedor'];

    // Traer los datos del proveedor
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id_proveedor = :id_proveedor");
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmt->execute();
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proveedor) {
        echo "<div class='alert alert-danger'>Proveedor no encontrado.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>No se especificó el proveedor.</div>";
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = $_POST['nombre'];
        $empresa = $_POST['empresa'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];

        if (empty($nombre) || empty($empresa)) {
            throw new Exception("Nombre y empresa son obligatorios.");
        }

        $stmt = $conn->prepare("UPDATE proveedores SET nombre=:nombre, empresa=:empresa, telefono=:telefono, email=:email WHERE id_proveedor=:id_proveedor");
        $stmt->execute([
            ':nombre' => $nombre,
            ':empresa' => $empresa,
            ':telefono' => $telefono,
            ':email' => $email,
            ':id_proveedor' => $id_proveedor
        ]);

        echo "<script>alert('Proveedor actualizado'); window.location='readproveedores.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<h5><b>Editar Proveedor</b></h5>

<?php if(!empty($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($proveedor['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Empresa</label>
        <input type="text" name="empresa" class="form-control" value="<?= htmlspecialchars($proveedor['empresa']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($proveedor['telefono']) ?>">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($proveedor['email']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>

<?php include "../footer.php"; ?>
