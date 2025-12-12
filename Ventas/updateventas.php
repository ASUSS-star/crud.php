<?php
// updateventas.php
include "../config.php";
include "../header.php";

if (!isset($_GET['id'])) { header("Location: readventas.php"); exit; }
$id = (int)$_GET['id'];

// obtener venta
$stmt = $conn->prepare("SELECT * FROM ventas WHERE id = :id");
$stmt->execute([':id' => $id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$venta) { echo "<div class='alert alert-danger'>Venta no encontrada</div>"; include "footer.php"; exit; }

$clientes = $conn->query("SELECT id, nombre FROM clientes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $id_cliente = $_POST['id_cliente'];
    $stmt = $conn->prepare("UPDATE ventas SET fecha = :fecha, id_cliente = :id_cliente WHERE id = :id");
    if ($stmt->execute([':fecha'=>$fecha, ':id_cliente'=>$id_cliente, ':id'=>$id])) {
        echo "<script>alert('Venta actualizada'); window.location='readventas.php';</script>"; exit;
    } else {
        $error = "Error al actualizar";
    }
}
?>

<h5><b><i class="bi bi-pencil"></i> Editar Venta #<?= $venta['id'] ?></b></h5>
</header>

<?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="post">
  <div class="mb-3">
    <label>Cliente</label>
    <select name="id_cliente" class="form-control" required>
      <?php foreach($clientes as $c) : ?>
        <option value="<?= $c['id'] ?>" <?= ($c['id']==$venta['id_cliente'])?'selected':'' ?>><?= htmlspecialchars($c['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Fecha</label>
    <input type="datetime-local" name="fecha" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($venta['fecha'])) ?>">
  </div>

  <button class="btn btn-primary">Actualizar</button>
  <a href="readventas.php" class="btn btn-secondary">Volver</a>
</form>

<?php include "../footer.php"; ?>
