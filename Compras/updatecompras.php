<?php
// updatecompras.php
include "../config.php";   // ✔ Ruta correcta
include "../header.php";   // ✔ Ruta correcta

if (!isset($_GET['id'])) {
    header("Location: readcompras.php");
    exit;
}

$id = (int)$_GET['id'];

// Obtener la compra
$stmt = $conn->prepare("SELECT * FROM compras WHERE id = :id");
$stmt->execute([":id" => $id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    echo "<div class='alert alert-danger'>Compra no encontrada</div>";
    include "../footer.php";
    exit;
}

// Obtener proveedores
$proveedores = $conn->query("SELECT id_proveedor, nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $id_proveedor = $_POST['id_proveedor'];

    $stmt = $conn->prepare("UPDATE compras 
                            SET fecha = :fecha, id_proveedor = :id_proveedor 
                            WHERE id = :id");

    if ($stmt->execute([
        ":fecha" => $fecha,
        ":id_proveedor" => $id_proveedor,
        ":id" => $id
    ])) {
        echo "<script>alert('Compra actualizada correctamente.'); window.location='readcompras.php';</script>";
        exit;
    } else {
        $error = "Error al actualizar la compra.";
    }
}
?>

<h5><b><i class="bi bi-pencil"></i> Editar Compra #<?= htmlspecialchars($compra['id']) ?></b></h5>
</header>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="id_proveedor" class="form-label">Proveedor</label>
        <select name="id_proveedor" id="id_proveedor" class="form-control" required>
            <?php foreach ($proveedores as $p): ?>
                <option value="<?= $p['id_proveedor'] ?>"
                    <?= ($p['id_proveedor'] == $compra['id_proveedor']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="datetime-local" name="fecha" id="fecha" class="form-control"
               value="<?= date('Y-m-d\TH:i', strtotime($compra['fecha'])) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="readcompras.php" class="btn btn-secondary">Volver</a>
</form>

<?php include "../footer.php"; ?>
