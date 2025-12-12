<?php
// createventas.php
include "../config.php";
include "../header.php";

// Traer clientes y productos (producto incluye stock y precio)
$clientes = $conn->query("SELECT id, nombre FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
$productos = $conn->query("SELECT id, nombre, precio, stock FROM productos")->fetchAll(PDO::FETCH_ASSOC);

// PROCESAR POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recibir datos
        $id_cliente = $_POST['id_cliente'];
        $fecha = $_POST['fecha'] ?: date('Y-m-d H:i:s');
        $product_ids = $_POST['product_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $precios = $_POST['precio'] ?? [];

        // Validaciones básicas
        if (empty($id_cliente) || count($product_ids) == 0) {
            throw new Exception("Cliente y productos son obligatorios.");
        }

        // Calcular total y validar cantidades
        $total = 0.0;
        $detalles = []; // array de arrays (id_producto, cantidad, precio)
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = (int)$product_ids[$i];
            $cant = (int)$cantidades[$i];
            $precio = (float)$precios[$i];
            if ($cant <= 0) throw new Exception("Cantidad inválida.");
            // obtener stock actual del producto (bloqueamos row con SELECT FOR UPDATE abajo cuando iniciemos la transacción)
            $total += $precio * $cant;
            $detalles[] = ['id_producto' => $pid, 'cantidad' => $cant, 'precio' => $precio];
        }

        // Iniciar transacción
        $conn->beginTransaction();

        // Insertar venta (header)
        $stmt = $conn->prepare("INSERT INTO ventas (fecha, id_cliente, total) VALUES (:fecha, :id_cliente, :total)");
        $stmt->execute([':fecha' => $fecha, ':id_cliente' => $id_cliente, ':total' => $total]);
        $id_venta = $conn->lastInsertId();

        // Por cada detalle: validar stock y registrar
        $stmtCheck = $conn->prepare("SELECT stock FROM productos WHERE id = :id FOR UPDATE");
        $stmtUpdateStock = $conn->prepare("UPDATE productos SET stock = stock - :cant WHERE id = :id");
        $stmtDetalle = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio) VALUES (:id_venta, :id_producto, :cantidad, :precio)");

        foreach ($detalles as $d) {
            // bloquear y consultar stock
            $stmtCheck->execute([':id' => $d['id_producto']]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$row) throw new Exception("Producto ID {$d['id_producto']} no existe.");
            if ((int)$row['stock'] < $d['cantidad']) {
                throw new Exception("No hay suficiente stock para el producto ID {$d['id_producto']}.");
            }
            // insertar detalle y actualizar stock
            $stmtDetalle->execute([
                ':id_venta' => $id_venta,
                ':id_producto' => $d['id_producto'],
                ':cantidad' => $d['cantidad'],
                ':precio' => $d['precio']
            ]);
            $stmtUpdateStock->execute([':cant' => $d['cantidad'], ':id' => $d['id_producto']]);
        }

        $conn->commit();
        echo "<script>alert('Venta registrada'); window.location='readventas.php';</script>";
        exit;
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<h5><b><i class="bi bi-cart-plus"></i> Nueva Venta</b></h5>
</header>

<?php if (!empty($error)) : ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" id="formVenta">
  <div class="mb-3">
    <label>Cliente</label>
    <select name="id_cliente" class="form-control" required>
      <option value="">-- Selecciona cliente --</option>
      <?php foreach ($clientes as $c) : ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label>Fecha</label>
    <input type="datetime-local" name="fecha" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
  </div>

  <hr>
  <h6>Productos</h6>
  <table class="table" id="tablaProductos">
    <thead><tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="text-end"><b>Total:</b></td>
        <td><input type="text" id="total" readonly class="form-control" value="0.00"></td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  <button type="button" id="agregar" class="btn btn-secondary">Agregar producto</button>
  <button type="submit" class="btn btn-primary">Registrar Venta</button>
</form>

<script>
// Datos de productos para JS
const productos = <?php
    // convert products to JSON with id, name, precio, stock
    $arr = [];
    foreach ($productos as $p) $arr[] = ['id' => $p['id'], 'nombre' => $p['nombre'], 'precio' => (float)$p['precio'], 'stock' => (int)$p['stock']];
    echo json_encode($arr);
?>;

function crearRow() {
  const tbody = document.querySelector('#tablaProductos tbody');
  const tr = document.createElement('tr');

  const sel = document.createElement('select');
  sel.className = 'form-control product-select';
  sel.innerHTML = '<option value="">--Selecciona--</option>';
  productos.forEach(p => sel.innerHTML += `<option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}">${p.nombre}</option>`);

  const precio = document.createElement('input'); precio.type='text'; precio.className='form-control precio'; precio.readOnly=true;
  const stock = document.createElement('input'); stock.type='text'; stock.className='form-control stock'; stock.readOnly=true;
  const cantidad = document.createElement('input'); cantidad.type='number'; cantidad.className='form-control cantidad'; cantidad.min=1; cantidad.value=1;
  const subtotal = document.createElement('input'); subtotal.type='text'; subtotal.className='form-control subtotal'; subtotal.readOnly=true;
  const removeBtn = document.createElement('button'); removeBtn.type='button'; removeBtn.className='btn btn-danger btn-sm'; removeBtn.textContent='X';

  // Hidden inputs to send to server
  const hidPid = document.createElement('input'); hidPid.type='hidden'; hidPid.name='product_id[]';
  const hidCantidad = document.createElement('input'); hidCantidad.type='hidden'; hidCantidad.name='cantidad[]';
  const hidPrecio = document.createElement('input'); hidPrecio.type='hidden'; hidPrecio.name='precio[]';

  sel.addEventListener('change', function() {
    const i = sel.selectedIndex;
    const opt = sel.options[i];
    const p = productos.find(x => x.id == sel.value);
    if (p) {
      precio.value = p.precio.toFixed(2);
      stock.value = p.stock;
      cantidad.max = p.stock;
      updateSubtotal();
    } else {
      precio.value = stock.value = subtotal.value = '';
      cantidad.value = 1;
    }
    hidPid.value = sel.value;
    hidPrecio.value = precio.value;
  });

  cantidad.addEventListener('input', updateSubtotal);

  function updateSubtotal(){
    const pr = parseFloat(precio.value) || 0;
    const q = parseInt(cantidad.value) || 0;
    const st = parseFloat(pr * q) || 0;
    subtotal.value = st.toFixed(2);
    hidCantidad.value = q;
    hidPrecio.value = pr.toFixed(2);
    updateTotal();
  }

  removeBtn.addEventListener('click', () => { tr.remove(); updateTotal(); });

  tr.appendChild(makeCell(sel));
  tr.appendChild(makeCell(precio));
  tr.appendChild(makeCell(stock));
  tr.appendChild(makeCell(cantidad));
  tr.appendChild(makeCell(subtotal));
  const tdBtn = document.createElement('td'); tdBtn.appendChild(removeBtn);
  tr.appendChild(tdBtn);

  // append hidden inputs into the row (not visible)
  tr.appendChild(hidPid); tr.appendChild(hidCantidad); tr.appendChild(hidPrecio);

  tbody.appendChild(tr);
  function makeCell(el){ const td=document.createElement('td'); td.appendChild(el); return td; }
  return tr;
}

function updateTotal(){
  let sum = 0;
  document.querySelectorAll('#tablaProductos .subtotal').forEach(s => {
    const v = parseFloat(s.value) || 0;
    sum += v;
  });
  document.getElementById('total').value = sum.toFixed(2);
}

document.getElementById('agregar').addEventListener('click', () => crearRow());
</script>

<?php include "../footer.php"; ?>
