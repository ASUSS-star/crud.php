<?php
// createcompras.php
include "../config.php";
include "../header.php";

// Traer proveedores y productos
$proveedores = $conn->query("SELECT id_proveedor, nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);
$productos = $conn->query("SELECT id, nombre, precio, stock FROM productos")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_proveedor = $_POST['id_proveedor'];
        $fecha = $_POST['fecha'] ?: date('Y-m-d H:i:s');
        $product_ids = $_POST['product_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $precios = $_POST['precio'] ?? [];

        if (empty($id_proveedor) || count($product_ids) == 0) {
            throw new Exception("Proveedor y productos son obligatorios.");
        }

        $total = 0;
        $detalles = [];
        for ($i=0; $i<count($product_ids); $i++){
            $pid = (int)$product_ids[$i];
            $cant = (int)$cantidades[$i];
            $precio = (float)$precios[$i];
            if ($cant <= 0) throw new Exception("Cantidad inválida.");
            $total += $precio * $cant;
            $detalles[] = ['id_producto'=>$pid,'cantidad'=>$cant,'precio'=>$precio];
        }

        $conn->beginTransaction();

        // Insertar cabecera
        $stmt = $conn->prepare("INSERT INTO compras (fecha, id_proveedor, total) VALUES (:fecha, :id_proveedor, :total)");
        $stmt->execute([':fecha'=>$fecha, ':id_proveedor'=>$id_proveedor, ':total'=>$total]);
        $id_compra = $conn->lastInsertId();

        // Insertar detalle y actualizar stock
        $stmtDetalle = $conn->prepare("INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES (:compra_id, :producto_id, :cantidad, :precio)");
        $stmtUpdateStock = $conn->prepare("UPDATE productos SET stock = stock + :cant WHERE id = :id");

        foreach ($detalles as $d){
            $stmtDetalle->execute([
                ':compra_id'=>$id_compra,
                ':producto_id'=>$d['id_producto'],
                ':cantidad'=>$d['cantidad'],
                ':precio'=>$d['precio']
            ]);
            $stmtUpdateStock->execute([':cant'=>$d['cantidad'], ':id'=>$d['id_producto']]);
        }

        $conn->commit();
        echo "<script>alert('Compra registrada'); window.location='readcompras.php';</script>"; exit;
    } catch (Exception $e){
        if ($conn->inTransaction()) $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<h5><b><i class="bi bi-cart-plus"></i> Nueva Compra</b></h5>
</header>

<?php if(!empty($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>

<form method="POST" id="formCompra">
  <div class="mb-3">
    <label>Proveedor</label>
    <select name="id_proveedor" class="form-control" required>
      <option value="">-- Selecciona proveedor --</option>
      <?php foreach($proveedores as $p) : ?>
        <option value="<?= $p['id_proveedor'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
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
  <button type="submit" class="btn btn-primary">Registrar Compra</button>
</form>

<script>
const productos = <?php
$arr=[];
foreach($productos as $p) $arr[]=['id'=>$p['id'],'nombre'=>$p['nombre'],'precio'=>(float)$p['precio'],'stock'=>(int)$p['stock']];
echo json_encode($arr);
?>;

function crearRow(){
  const tbody = document.querySelector('#tablaProductos tbody');
  const tr = document.createElement('tr');

  const sel = document.createElement('select');
  sel.className='form-control product-select';
  sel.innerHTML='<option value="">--Selecciona--</option>';
  productos.forEach(p=>sel.innerHTML+=`<option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}">${p.nombre}</option>`);

  const precio = document.createElement('input'); precio.type='text'; precio.className='form-control precio'; precio.readOnly=true;
  const stock = document.createElement('input'); stock.type='text'; stock.className='form-control stock'; stock.readOnly=true;
  const cantidad = document.createElement('input'); cantidad.type='number'; cantidad.className='form-control cantidad'; cantidad.min=1; cantidad.value=1;
  const subtotal = document.createElement('input'); subtotal.type='text'; subtotal.className='form-control subtotal'; subtotal.readOnly=true;
  const removeBtn = document.createElement('button'); removeBtn.type='button'; removeBtn.className='btn btn-danger btn-sm'; removeBtn.textContent='X';

  const hidPid=document.createElement('input'); hidPid.type='hidden'; hidPid.name='product_id[]';
  const hidCantidad=document.createElement('input'); hidCantidad.type='hidden'; hidCantidad.name='cantidad[]';
  const hidPrecio=document.createElement('input'); hidPrecio.type='hidden'; hidPrecio.name='precio[]';

  sel.addEventListener('change',function(){
    const p=productos.find(x=>x.id==sel.value);
    if(p){ precio.value=p.precio.toFixed(2); stock.value=p.stock; cantidad.max=p.stock; updateSubtotal(); } else { precio.value=stock.value=subtotal.value=''; cantidad.value=1; }
    hidPid.value=sel.value; hidPrecio.value=precio.value;
  });

  cantidad.addEventListener('input',updateSubtotal);
  removeBtn.addEventListener('click',()=>{ tr.remove(); updateTotal(); });

  function updateSubtotal(){
    const pr=parseFloat(precio.value)||0;
    const q=parseInt(cantidad.value)||0;
    const st=parseFloat(pr*q)||0;
    subtotal.value=st.toFixed(2);
    hidCantidad.value=q;
    hidPrecio.value=pr.toFixed(2);
    updateTotal();
  }

  function makeCell(el){ const td=document.createElement('td'); td.appendChild(el); return td; }
  tr.appendChild(makeCell(sel));
  tr.appendChild(makeCell(precio));
  tr.appendChild(makeCell(stock));
  tr.appendChild(makeCell(cantidad));
  tr.appendChild(makeCell(subtotal));
  const tdBtn=document.createElement('td'); tdBtn.appendChild(removeBtn); tr.appendChild(tdBtn);
  tr.appendChild(hidPid); tr.appendChild(hidCantidad); tr.appendChild(hidPrecio);
  tbody.appendChild(tr);
  return tr;
}

function updateTotal(){
  let sum=0;
  document.querySelectorAll('#tablaProductos .subtotal').forEach(s=>sum+=parseFloat(s.value)||0);
  document.getElementById('total').value=sum.toFixed(2);
}

document.getElementById('agregar').addEventListener('click',()=>crearRow());
</script>

<?php include "../footer.php"; ?>
