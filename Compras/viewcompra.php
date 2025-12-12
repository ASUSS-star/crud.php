<?php
// viewcompra.php
include "config.php";
include "header.php";

if(!isset($_GET['id'])) { header("Location: readcompras.php"); exit; }
$id=(int)$_GET['id'];

// Cabecera
$stmt=$conn->prepare("SELECT c.*, p.nombre AS proveedor FROM compras c LEFT JOIN proveedores p ON c.id_proveedor=p.id WHERE c.id=:id");
$stmt->execute([':id'=>$id]);
$compra=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$compra){ echo "<div class='alert alert-danger'>Compra no encontrada</div>"; include "footer.php"; exit; }

// Detalles
$stmt=$conn->prepare("SELECT dc.*, pr.nombre FROM detalle_compra dc LEFT JOIN productos pr ON dc.id_producto=pr.id WHERE dc.id_compra=:id");
$stmt->execute([':id'=>$id]);
$detalles=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h5><b><i class="bi bi-eye"></i> Compra #<?= $compra['id'] ?></b></h5>
</header>

<div class="mb-3">
  <b>Fecha:</b> <?= $compra['fecha'] ?><br>
  <b>Proveedor:</b> <?= htmlspecialchars($compra['proveedor']) ?><br>
  <b>Total:</b> $<?= number_format($compra['total'],2) ?>
</div>

<table class="table table-bordered">
  <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
  <tbody>
    <?php foreach($detalles as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['nombre']) ?></td>
        <td>$<?= number_format($d['precio'],2) ?></td>
        <td><?= $d['cantidad'] ?></td>
        <td>$<?= number_format($d['precio']*$d['cantidad'],2) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="readcompras.php" class="btn btn-secondary">Volver</a>
<?php include "footer.php"; ?>
