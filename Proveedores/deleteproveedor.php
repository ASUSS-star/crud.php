<?php
include "../config.php";

if (isset($_GET['id_proveedor'])) {
    $id_proveedor = (int)$_GET['id_proveedor'];

    try {
        $conn->beginTransaction();

        // 1️⃣ Obtener todas las compras de este proveedor
        $stmt = $conn->prepare("SELECT id FROM compras WHERE id_proveedor = :id_proveedor");
        $stmt->execute([':id_proveedor' => $id_proveedor]);
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($compras as $compra) {
            // 2️⃣ Borrar los detalles de cada compra
            $stmtDelDetalle = $conn->prepare("DELETE FROM detalle_compra WHERE compra_id = :compra_id");
            $stmtDelDetalle->execute([':compra_id' => $compra['id']]);

            // 3️⃣ Borrar la compra
            $stmtDelCompra = $conn->prepare("DELETE FROM compras WHERE id = :compra_id");
            $stmtDelCompra->execute([':compra_id' => $compra['id']]);
        }

        // 4️⃣ Finalmente, borrar el proveedor
        $stmtDelProv = $conn->prepare("DELETE FROM proveedores WHERE id_proveedor = :id_proveedor");
        $stmtDelProv->execute([':id_proveedor' => $id_proveedor]);

        $conn->commit();
        echo "<script>alert('Proveedor eliminado correctamente'); window.location='readproveedores.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<div class='alert alert-danger'>Error al eliminar proveedor: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

} else {
    echo "<div class='alert alert-danger'>No se especificó el proveedor.</div>";
}
?>
