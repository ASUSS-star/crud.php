<?php
include "../config.php";

if (isset($_GET['id'])) {
    // Obtener el ID del producto a eliminar
    $id = isset($_GET['id']) ? $_GET['id'] : "";

    // Preparar la consulta para eliminar el producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->bindParam(":id", $id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al listado de productos después de eliminar
        header('Location: readproductos.php');
    } else {
        // Manejo de errores si no se puede eliminar
        echo "Error al eliminar el producto.";
    }
}
?>
