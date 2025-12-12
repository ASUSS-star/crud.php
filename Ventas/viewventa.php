<?php
// viewventa.php
include "../config.php";
include "../header.php";

if (!isset($_GET['id'])) { header("Location: readventas.php"); exit; }
$id = (int)$_GET['id'];

// Cabecera
$stmt = $conn->prepare("SELECT v.*, c.nombre AS cliente FROM ventas v LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id = :id");
$stmt->execute([':id' => $id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$venta) { echo "<div class='alert alert-danger'>Venta no encontrada</div>"; include "footer.php"; exit; }

// Detalles
$stmt = $conn->prepare("SELECT dv.*, p.nombre FROM detalle_venta dv LEFT JOIN productos p ON dv.id_producto = p.id WHERE dv.id_venta = :id");
$stmt->execute([':id' => $id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h5><b><i class="bi bi-eye"></i> Venta #<?= $venta['id'] ?></b></h5>
</header>

<div class="mb-3">
  <b>Fecha:</b> <?= $venta['fecha'] ?><br>
  <b>Cliente:</b> <?= htmlspecialchars($venta['cliente']) ?><br>
  <b>Total:</b> $<?= number_format($venta['total'],2) ?>
</div>

<table class="table table-bordered">
  <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
  <tbody>
    <?php foreach ($detalles as $d) : ?>
      <tr>
        <td><?= htmlspecialchars($d['nombre']) ?></td>
        <td>$<?= number_format($d['precio'],2) ?></td>
        <td><?= $d['cantidad'] ?></td>
        <td>$<?= number_format($d['precio'] * $d['cantidad'],2) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="readventas.php" class="btn btn-secondary">Volver</a>
<?php include "../footer.php"; ?>
