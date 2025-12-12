<?php
include "../config.php"; // ← porque estás dentro de /Compras/

if(!isset($_GET['id'])){
    header("Location: readcompras.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $conn->beginTransaction();

    // Obtener detalles con los nombres correctos
    $stmt = $conn->prepare("SELECT producto_id, cantidad FROM detalle_compra WHERE compra_id = :id");
    $stmt->execute([':id' => $id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restar stock
    $stmtUpdate = $conn->prepare("UPDATE productos SET stock = stock - :cant WHERE id = :idProducto");

    foreach ($detalles as $d) {
        $stmtUpdate->execute([
            ':cant' => $d['cantidad'],
            ':idProducto' => $d['producto_id']
        ]);
    }

    // Eliminar detalles
    $stmt = $conn->prepare("DELETE FROM detalle_compra WHERE compra_id = :id");
    $stmt->execute([':id' => $id]);

    // Eliminar compra
    $stmt = $conn->prepare("DELETE FROM compras WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $conn->commit();

    echo "<script>
        alert('Compra eliminada correctamente y stock actualizado.');
        window.location = 'readcompras.php';
    </script>";

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
