<?php
// readventas.php
include "../config.php";
include "../header.php";

// Obtener ventas con nombre de cliente
$stmt = $conn->prepare("
  SELECT v.id, v.fecha_venta, v.total, v.estado, c.nombre AS cliente
  FROM ventas v
  LEFT JOIN clientes c ON v.id_cliente = c.id
  ORDER BY v.fecha_venta DESC
");
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h5><b><i class="bi bi-receipt"></i> Ventas</b></h5>
</header>

<div class="row mb-3">
  <div class="col-md-6">
    <a class="btn btn-success" href="createventas.php"><i class="bi bi-plus-lg"></i> Nueva Venta</a>
  </div>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Total</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ventas as $v) : ?>
    <tr>
      <td><?= htmlspecialchars($v['id']) ?></td>
      <td><?= htmlspecialchars($v['fecha_venta']) ?></td>
      <td><?= htmlspecialchars($v['cliente']) ?></td>
      <td>$<?= number_format($v['total'], 2) ?></td>
      <td><?= htmlspecialchars($v['estado']) ?></td>
      <td>
        <a class="btn btn-info btn-sm" href="viewventa.php?id=<?= $v['id'] ?>">Ver</a>
        <a class="btn btn-warning btn-sm" href="updateventas.php?id=<?= $v['id'] ?>">Editar</a>
        <a class="btn btn-danger btn-sm" href="deleteventas.php?id=<?= $v['id'] ?>" onclick="return confirm('¿Eliminar venta? Esto RESTAURARÁ el stock.');">Eliminar</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include "../footer.php"; ?>
