<?php
// readcompras.php
include "../config.php";
include "../header.php";

// Obtener compras
$stmt = $conn->prepare("
  SELECT c.id, c.fecha_compra, c.total
  FROM compras c
  ORDER BY c.fecha_compra DESC
");
$stmt->execute();
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h5><b><i class="bi bi-cart"></i> Compras</b></h5>
</header>

<div class="row mb-3">
  <div class="col-md-6">
    <a class="btn btn-success" href="createcompras.php"><i class="bi bi-plus-lg"></i> Nueva Compra</a>
  </div>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Total</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($compras as $c) : ?>
    <tr>
      <td><?= htmlspecialchars($c['id']) ?></td>
      <td><?= htmlspecialchars($c['fecha_compra']) ?></td>
      <td>$<?= number_format($c['total'], 2) ?></td>
      <td>
        <a class="btn btn-info btn-sm" href="viewcompra.php?id=<?= $c['id'] ?>">Ver</a>
        <a class="btn btn-warning btn-sm" href="updatecompras.php?id=<?= $c['id'] ?>">Editar</a>
        <a class="btn btn-danger btn-sm" href="deletecompras.php?id=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar compra? Esto RESTAURARÁ el stock.');">Eliminar</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include "../footer.php"; ?>
