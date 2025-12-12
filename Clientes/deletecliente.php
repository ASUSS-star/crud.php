<?php
include "../config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    header("Location: readclientes.php");
}
?>
