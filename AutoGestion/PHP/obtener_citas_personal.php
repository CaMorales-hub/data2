<?php
require 'conexion.php';

$resultado = $conn->query("SELECT c.id, u.nombre AS usuario, c.fecha, c.hora, c.estado, c.archivo_pdf
                           FROM citas c
                           JOIN usuarios u ON c.usuario_id = u.id
                           ORDER BY c.fecha DESC");

while ($row = $resultado->fetch_assoc()):
?>
  <tr data-id="<?= $row['id'] ?>">
    <td><?= htmlspecialchars($row['usuario']) ?></td>
    <td><?= $row['fecha'] ?></td>
    <td><?= $row['hora'] ?></td>
    <td class="estado"><?= ucfirst($row['estado']) ?></td>
    <td>
      <button class="btn-editar" onclick="mostrarCita('<?= $row['archivo_pdf'] ?>')" title="Ver PDF">
        <i data-lucide="eye"></i>
      </button>
      <button class="btn-confirmar" onclick="confirmarCita(<?= $row['id'] ?>)" title="Confirmar">
        <i data-lucide="check-circle"></i>
      </button>
      <button class="btn-eliminar" onclick="abrirModalEliminar(<?= $row['id'] ?>)" title="Eliminar">
        <i data-lucide="trash"></i>
      </button>
    </td>
  </tr>
<?php endwhile; ?>
