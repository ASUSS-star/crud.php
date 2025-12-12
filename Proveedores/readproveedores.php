<?php
include "../config.php";
include "../header.php";
?>

<h5><b><i class="fa fa-users"></i> Lista de Proveedores</b></h5>
</header>
<div class="row">
    <div class="col-md-2 text-right">
        <h1><a href="createproveedor.php" class="btn btn-success">Nuevo Proveedor</a></h1>
    </div>
</div>

<table class="table table-striped">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Empresa</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Acciones</th>
    </tr>

    <?php
    $stmt = $conn->prepare("SELECT * FROM proveedores");
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($proveedores as $proveedor) { ?>
        <tr>
            <td><?php echo $proveedor['id_proveedor']; ?></td>
            <td><?php echo $proveedor['nombre']; ?></td>
            <td><?php echo $proveedor['empresa']; ?></td>
            <td><?php echo $proveedor['telefono']; ?></td>
            <td><?php echo $proveedor['email']; ?></td>
            <td>
                <a class="btn btn-warning" href="updateproveedor.php?id_proveedor=<?php echo $proveedor['id_proveedor']; ?>">✏ Editar</a> 
                <a class="btn btn-danger" href="deleteproveedor.php?id_proveedor=<?php echo $proveedor['id_proveedor']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este proveedor?');">🗑 Borrar</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php include "../footer.php"; ?>
