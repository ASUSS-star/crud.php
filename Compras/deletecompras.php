<?php
// deletecompras.php
include "config.php";

if(!isset($_GET['id'])){ header("Location: readcompras.php"); exit; }
$id=(int)$_GET['id'];

try{
    $conn->beginTransaction();

    // Detalles de la compra
    $stmt=$conn->prepare("SELECT id_producto, cantidad FROM detalle_compra WHERE id_compra=:id");
    $stmt->execute([':id'=>$id]);
    $detalles=$stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restar stock
    $stmtUpdate=$conn->prepare("UPDATE productos SET stock = stock - :cant WHERE id = :id");
    foreach($detalles as $d){
        $stmtUpdate->execute([':cant'=>$d['cantidad'], ':id'=>$d['id_producto']]);
    }

    // Eliminar detalles
    $stmt=$conn->prepare("DELETE FROM detalle_compra WHERE id_compra=:id");
    $stmt->execute([':id'=>$id]);

    // Eliminar compra
    $stmt=$conn->prepare("DELETE FROM compras WHERE id=:id");
    $stmt->execute([':id'=>$id]);

    $conn->commit();
    echo "<script>alert('Compra eliminada y stock restaurado'); window.location='readcompras.php';</script>";
}catch(Exception $e){
    if($conn->inTransaction()) $conn->rollBack();
    echo "<div class='alert alert-danger'>Error: ".htmlspecialchars($e->getMessage())."</div>";
}
?>
