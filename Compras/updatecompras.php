<?php
// updatecompras.php
include "config.php";
include "header.php";

if(!isset($_GET['id'])){ header("Location: readcompras.php"); exit; }
$id=(int)$_GET['id'];

// Obtener compra
$stmt=$conn->prepare("SELECT * FROM compras WHERE id=:id");
$stmt->execute([':id'=>$id]);
$compra=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$compra){ echo "<div class='alert alert-danger'>Compra no encontrada</div>"; include "footer.php"; exit; }

$proveedores=$conn->query("SELECT id,nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $fecha=$_POST['fecha'];
    $id_proveedor=$_POST['id_proveedor'];
    $stmt=$conn->prepare("UPDATE compras SET fecha=:fecha, id_proveedor=:id_proveedor WHERE id=:id");
    if($stmt->execute([':fecha'=>$fecha, ':id_proveedor'=>$id_proveedor, ':id'=>$id])){
        echo "<script>alert('Compra actualizada'); window.location='readcompras.php';</script>"; exit;
    } else $error="Error al actualizar";
}
?>

<h5><b><i class="bi bi-pencil"></i> Editar Compra #<?= $compra['id'] ?></b></h5>
</header>

<?php if(!empty($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>

<form method="post">
  <div class="mb-3">
    <label>Proveedor</label>
    <select name="id_proveedor" class="form-control" required>
      <?php foreach($proveedores as $p): ?>
        <option value="<?= $p['id'] ?>" <?= ($p['id']==$compra['id_proveedor'])?'selected':'' ?>><?= htmlspecialchars($p['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Fecha</label>
    <input type="datetime-local" name="fecha" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($compra['fecha'])) ?>">
  </div>
  <button class="btn btn-primary">Actualizar</button>
  <a href="readcompras.php" class="btn btn-secondary">Volver</a>
</form>

<?php include "footer.php"; ?>
