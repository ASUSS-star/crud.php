<?php
// viewcompra.php
include "../config.php";     // ✔ Ruta correcta
include "../header.php";     // ✔ Ruta correcta

if (!isset($_GET['id'])) {
    header("Location: readcompras.php");
    exit;
}

$id = (int)$_GET['id'];

// Obtener datos de la compra
$stmt = $conn->prepare("
    SELECT c.id, c.fecha, p.nombre AS proveedor
    FROM compras c
    INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor
    WHERE c.id = :id
");
$stmt->execute([":id" => $id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    echo "<div class='alert alert-danger'>Compra no encontrada</div>";
    include "../footer.php";
    exit;
}

// Obtener detalles de la compra
$stmtDet = $conn->prepare("
    SELECT d.producto_id, d.cantidad, d.precio, pr.nombre
    FROM detalle_compra d
    INNER JOIN productos pr ON d.producto_id = pr.id
    WHERE d.compra_id = :id
");
$stmtDet->execute([":id" => $id]);
$detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
?>

<h5><b><i class="bi bi-eye"></i> Detalle de Compra #<?= $compra['id'] ?></b></h5>
</header>

<div class="card p-3">
    <p><b>Proveedor:</b> <?= htmlspecialchars($compra['proveedor']) ?></p>
    <p><b>Fecha:</b> <?= $compra['fecha'] ?></p>
</div>

<h5 class="mt-4"><b>Productos Comprados</b></h5>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['nombre']) ?></td>
            <td><?= $d['cantidad'] ?></td>
            <td>$<?= number_format($d['precio'], 2) ?></td>
            <td>$<?= number_format($d['cantidad'] * $d['precio'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="readcompras.php" class="btn btn-secondary">Volver</a>

<?php include "../footer.php"; ?>
