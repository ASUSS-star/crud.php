<?php
// deleteventas.php
include "../config.php";

if (!isset($_GET['id'])) { header("Location: readventas.php"); exit; }
$id = (int)$_GET['id'];

try {
    $conn->beginTransaction();

    // Obtener detalles de la venta
    $stmt = $conn->prepare("SELECT id_producto, cantidad FROM detalle_venta WHERE id_venta = :id");
    $stmt->execute([':id' => $id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restaurar stock
    $stmtUpdate = $conn->prepare("UPDATE productos SET stock = stock + :cant WHERE id = :id");
    foreach ($detalles as $d) {
        $stmtUpdate->execute([':cant' => $d['cantidad'], ':id' => $d['id_producto']]);
    }

    // Eliminar detalles
    $stmt = $conn->prepare("DELETE FROM detalle_venta WHERE id_venta = :id");
    $stmt->execute([':id' => $id]);

    // Eliminar venta
    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $conn->commit();
    echo "<script>alert('Venta eliminada y stock restaurado'); window.location='readventas.php';</script>";
} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
