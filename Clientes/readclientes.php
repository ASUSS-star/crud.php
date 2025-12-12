<?php
include "../header.php";
$stmt = $conn->prepare("SELECT * FROM clientes");
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h5><b><i class="fa fa-users"></i> Clientes</b></h5>
</header>

<div class="row">
    <div class="col-md-2 text-right">
        <h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
            <i class="bi bi-person-plus-fill"></i> Nuevo
        </button>
        </h1>
    </div>
</div>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th width="20">ID</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Teléfono</th>
    <th>Dirección</th>
    <th>Imagen</th>
    <th width="100">Acciones</th>
</tr>
</thead>
<tbody>
<?php foreach ($clientes as $cliente) { ?>
<tr>
    <td><?= $cliente['id'] ?></td>
    <td><?= $cliente['nombre'] ?></td>
    <td><?= $cliente['email'] ?></td>
    <td><?= $cliente['telefono'] ?></td>
    <td><?= $cliente['direccion'] ?></td>
    <td><img src="../imagen/<?= $cliente['imagen'] ?>" width="80"></td>
    <td>
        <a href="updatecliente.php?id=<?= $cliente['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill"></i></a>
        <a onclick="return confirm('¿Seguro de eliminar?')" href="deletecliente.php?id=<?= $cliente['id'] ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
    </td>
</tr>
<?php } ?>
</tbody>
</table>

<?php include "createcliente.php"; ?>
<?php include "../footer.php"; ?>
