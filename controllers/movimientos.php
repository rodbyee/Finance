<?php
require_once 'conexion.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo    = $_POST['tipo'] ?? '';
    $cant    = $_POST['cant'] ?? '';
    $descrip = trim($_POST['descrip'] ?? '');
    $fecha   = $_POST['fecha'] ?? '';

    if (($tipo === 'ingreso' || $tipo === 'egreso')
        && is_numeric($cant) && $cant > 0
        && $descrip !== '' && $fecha !== '') {

        $stmt = mysqli_prepare($conn,
            "INSERT INTO move (id_user, tipo, cant, descrip, datet) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isdss", $id_user, $tipo, $cant, $descrip, $fecha);

        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "Movimiento registrado.";
        } else {
            $mensaje = "No se pudo guardar: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "Revisa los datos: monto, tipo, descripción y fecha son obligatorios.";
    }
}

$stmt = mysqli_prepare($conn,
    "SELECT id_mov, tipo, cant, descrip, datet FROM move WHERE id_user = ? ORDER BY datet DESC, id_mov DESC");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$movimientos    = [];
$total_ingresos = 0;
$total_egresos  = 0;

while ($fila = mysqli_fetch_assoc($resultado)) {
    $movimientos[] = $fila;
    if ($fila['tipo'] === 'ingreso') {
        $total_ingresos += $fila['cant'];
    } else {
        $total_egresos += $fila['cant'];
    }
}

$balance     = $total_ingresos - $total_egresos;
$signo       = $balance < 0 ? '-' : '';
$balance_abs = abs($balance);

$semanas_labels  = [];
$semanas_ingreso = [];
$semanas_egreso  = [];
$balance_acum    = [];
$acum            = 0;

for ($i = 3; $i >= 0; $i--) {
    $inicio = date('Y-m-d', strtotime("monday -$i weeks"));
    $fin    = date('Y-m-d', strtotime("sunday -$i weeks"));
    $label  = date('d/m', strtotime($inicio)) . ' - ' . date('d/m', strtotime($fin));
    $semanas_labels[] = $label;

    $stmt2 = mysqli_prepare($conn,
        "SELECT tipo, COALESCE(SUM(cant), 0) AS total
         FROM move
         WHERE id_user = ? AND datet BETWEEN ? AND ?
         GROUP BY tipo");
    mysqli_stmt_bind_param($stmt2, "iss", $id_user, $inicio, $fin);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);

    $ing = 0; $eg = 0;
    while ($r = mysqli_fetch_assoc($res2)) {
        if ($r['tipo'] === 'ingreso') $ing = (float)$r['total'];
        else                          $eg  = (float)$r['total'];
    }
    mysqli_stmt_close($stmt2);

    $semanas_ingreso[] = $ing;
    $semanas_egreso[]  = $eg;
    $acum += ($ing - $eg);
    $balance_acum[] = round($acum, 2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Movimientos - Finance.com</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/finance.jpg">
    <link rel="icon" type="image/png" href="../assets/finance.jpg">
<link rel="stylesheet" href="dashboard.css?v=<?= time() ?>">
<style>
.escaner-zona { margin-bottom: 24px; }

.escaner-drop {
    border: 2px dashed #10b981;
    border-radius: 12px;
    padding: 28px 20px;
    text-align: center;
    background: #f0fdf8;
    color: #555;
    transition: background 0.2s;
}
.escaner-drop:hover { background: #e6f7f1; }
.escaner-icono { font-size: 36px; display: block; margin-bottom: 8px; }
.escaner-drop p { margin: 4px 0; font-weight: 500; color: #333; }
.escaner-drop small { color: #999; font-size: 12px; }

.btn-escaner {
    margin-top: 14px;
    padding: 9px 22px;
    background: #10b981;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.btn-escaner:hover { opacity: 0.85; }

.escaner-preview {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    flex-wrap: wrap;
}
.escaner-preview img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
}
.escaner-estado {
    flex: 1;
    min-width: 180px;
    font-size: 14px;
    color: #555;
    padding-top: 8px;
}
.escaner-estado.ok { color: #10b981; font-weight: 600; }
.escaner-estado.error { color: #ef4444; font-weight: 600; }

.spinner {
    display: inline-block;
    width: 14px; height: 14px;
    border: 2px solid #ccc;
    border-top-color: #10b981;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin-right: 6px;
    vertical-align: middle;
}
@keyframes spin { to { transform: rotate(360deg); } }

.btn-nueva-foto {
    display: inline-block;
    margin-top: 10px;
    font-size: 13px;
    color: #10b981;
    cursor: pointer;
    text-decoration: underline;
    background: none;
    border: none;
    padding: 0;
}

.campo-resaltado input {
    border-color: #10b981 !important;
    background: #f0fdf8 !important;
}
</style>
</head>
<body>
<header>
    <div class="header-content">
        <img src="https://img.pikbest.com/wp/202424/dollar-money-coins-gold-icon-vector-illustration_10613260.jpg!sw800" alt="Finance.com Logo" class="logo">
        <h1>Finance.com</h1>
    </div>
    <nav class="nav-links">
        <a href="movimientos.php" class="activo">Movimientos</a>
        <a href="pagos.php">Pagos pendientes</a>
        <a href="logout.php">Cerrar sesion</a>
    </nav>
</header>

<div class="dashboard">

    <div class="bienvenida">
        <span>Bienvenido de nuevo,</span>
        <strong><?= htmlspecialchars($_SESSION['name_user']) ?></strong>
    </div>

    <div class="ticket">
        <div class="etiqueta">Balance actual</div>
        <div class="monto <?= $balance >= 0 ? 'positivo' : 'negativo' ?>">
            <?= $signo ?>$<?= number_format($balance_abs, 2) ?>
        </div>
        <div class="detalle">
            <div><span class="lab">Ingresos</span>$<?= number_format($total_ingresos, 2) ?></div>
            <div><span class="lab">Egresos</span>$<?= number_format($total_egresos, 2) ?></div>
        </div>
    </div>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="tarjeta">
        <h2>Registrar movimiento</h2>

        <div class="escaner-zona">
            <input type="file" id="archivoTicket" accept="image/*" style="display:none">

            <div class="escaner-drop" id="escanerDrop">
                <span class="escaner-icono">🧾</span>
                <p>Sube una foto de tu ticket o comprobante</p>
                <small>JPG, PNG - Claude extraera los datos automaticamente</small>
                <button type="button" class="btn-escaner" onclick="document.getElementById('archivoTicket').click()">
                    Seleccionar imagen
                </button>
            </div>

            <div class="escaner-preview" id="escanerPreview" style="display:none">
                <img id="previewImg" src="" alt="Comprobante">
                <div class="escaner-estado" id="escanerEstado">
                    <span class="spinner"></span> Analizando comprobante...
                </div>
            </div>
        </div>

        <form action="movimientos.php" method="post" id="formMovimiento">
            <div class="fila">
                <div class="tipo-toggle">
                    <label class="ingreso" id="labelIngreso">
                        <input type="radio" name="tipo" value="ingreso" checked id="radioIngreso">
                        <span>Ingreso</span>
                    </label>
                    <label class="egreso" id="labelEgreso">
                        <input type="radio" name="tipo" value="egreso" id="radioEgreso">
                        <span>Egreso</span>
                    </label>
                </div>
            </div>
            <div class="fila">
                <div class="campo" id="campoCant">
                    <label for="cant">Monto</label>
                    <input type="number" id="cant" name="cant" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
                <div class="campo" id="campoFecha">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="fila">
                <div class="campo" id="campoDescrip">
                    <label for="descrip">Descripcion</label>
                    <input type="text" id="descrip" name="descrip" placeholder="Ej. Renta, sueldo, supermercado..." required>
                </div>
            </div>
            <button type="submit">Guardar movimiento</button>
        </form>
    </div>

    <div class="tarjeta">
        <h2>Ingresos vs Egresos - ultimas 4 semanas</h2>
        <canvas id="graficaBarras" height="120"></canvas>
    </div>

    <div class="tarjeta">
        <h2>Balance acumulado - ultimas 4 semanas</h2>
        <canvas id="graficaLinea" height="120"></canvas>
    </div>

    <div class="tarjeta">
        <h2>Historial</h2>
        <?php if (empty($movimientos)): ?>
            <p class="vacio">Aun no tienes movimientos registrados.</p>
        <?php else: ?>
            <table class="libro">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($m['datet'])) ?></td>
                            <td>
                                <span class="badge <?= $m['tipo'] ?>">
                                    <?= $m['tipo'] === 'ingreso' ? 'Ingreso' : 'Egreso' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($m['descrip']) ?></td>
                            <td class="monto <?= $m['tipo'] ?>">
                                <?= $m['tipo'] === 'ingreso' ? '+' : '-' ?>$<?= number_format($m['cant'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const labels   = <?= json_encode($semanas_labels) ?>;
const ingresos = <?= json_encode($semanas_ingreso) ?>;
const egresos  = <?= json_encode($semanas_egreso) ?>;
const acum     = <?= json_encode($balance_acum) ?>;

Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color = '#888';

new Chart(document.getElementById('graficaBarras'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Ingresos', data: ingresos, backgroundColor: 'rgba(16,185,129,0.75)', borderRadius: 6 },
            { label: 'Egresos',  data: egresos,  backgroundColor: 'rgba(239,68,68,0.75)',  borderRadius: 6 }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-MX', {minimumFractionDigits:2}) } }
        },
        scales: {
            y: { beginAtZero:true, ticks: { callback: v => '$'+v.toLocaleString('es-MX') }, grid: { color:'#f0f0f0' } },
            x: { grid: { display:false } }
        }
    }
});

new Chart(document.getElementById('graficaLinea'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Balance acumulado',
            data: acum,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: acum.map(v => v >= 0 ? '#10b981' : '#ef4444'),
            pointRadius: 5,
            fill: true,
            tension: 0.35
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-MX', {minimumFractionDigits:2}) } }
        },
        scales: {
            y: { ticks: { callback: v => '$'+v.toLocaleString('es-MX') }, grid: { color:'#f0f0f0' } },
            x: { grid: { display:false } }
        }
    }
});

// ── Escaner de comprobante con Claude Vision ─────────────────
const inputArchivo   = document.getElementById('archivoTicket');
const escanerDrop    = document.getElementById('escanerDrop');
const escanerPreview = document.getElementById('escanerPreview');
const previewImg     = document.getElementById('previewImg');
const escanerEstado  = document.getElementById('escanerEstado');

inputArchivo.addEventListener('change', async e => {
    const file = e.target.files[0];
    if (!file) return;

    // Mostrar preview
    const reader = new FileReader();
    reader.onload = ev => { previewImg.src = ev.target.result; };
    reader.readAsDataURL(file);

    escanerDrop.style.display    = 'none';
    escanerPreview.style.display = 'flex';
    escanerEstado.className      = 'escaner-estado';
    escanerEstado.innerHTML      = '<span class="spinner"></span> Analizando comprobante...';

    try {
        const base64 = await toBase64(file);
        const data   = await llamarClaude(base64, file.type);
        rellenarFormulario(data);
    } catch (err) {
        escanerEstado.className   = 'escaner-estado error';
        escanerEstado.innerHTML   = 'No se pudo leer el comprobante. Ingresa los datos manualmente.<br><button class="btn-nueva-foto" onclick="resetEscaner()">Intentar con otra imagen</button>';
    }
});

function toBase64(file) {
    return new Promise((res, rej) => {
        const r = new FileReader();
        r.onload  = () => res(r.result.split(',')[1]);
        r.onerror = rej;
        r.readAsDataURL(file);
    });
}

async function llamarClaude(base64, mediaType) {
    const prompt = `Eres un extractor de datos financieros. Analiza esta imagen de un ticket de compra o comprobante de transferencia bancaria (SPEI, CoDi, etc.) y extrae la informacion.

Responde UNICAMENTE con un objeto JSON valido, sin texto adicional, sin markdown, sin backticks. El JSON debe tener exactamente estas claves:
{
  "tipo": "egreso" o "ingreso",
  "monto": numero decimal (solo el numero, sin simbolos),
  "fecha": "YYYY-MM-DD",
  "descripcion": "texto breve que describe el concepto o comercio"
}

Reglas:
- tipo: si es un ticket de compra o pago -> "egreso". Si es una transferencia recibida o deposito -> "ingreso".
- monto: el total a pagar o el monto transferido. Solo el numero.
- fecha: la fecha del comprobante en formato YYYY-MM-DD. Si no hay fecha visible usa la de hoy: <?= date('Y-m-d') ?>.
- descripcion: nombre del comercio, concepto de transferencia o descripcion breve. Maximo 60 caracteres.`;

    const response = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            model: 'claude-sonnet-4-6',
            max_tokens: 300,
            messages: [{
                role: 'user',
                content: [
                    { type: 'image', source: { type: 'base64', media_type: mediaType, data: base64 } },
                    { type: 'text', text: prompt }
                ]
            }]
        })
    });

    const json = await response.json();
    const texto = json.content?.[0]?.text ?? '';
    return JSON.parse(texto.trim());
}

function rellenarFormulario(data) {
    if (!data.monto || !data.fecha) throw new Error('Datos incompletos');

    // Tipo
    if (data.tipo === 'ingreso') {
        document.getElementById('radioIngreso').checked = true;
    } else {
        document.getElementById('radioEgreso').checked = true;
    }

    // Monto
    const cantInput = document.getElementById('cant');
    cantInput.value = parseFloat(data.monto).toFixed(2);
    document.getElementById('campoCant').classList.add('campo-resaltado');

    // Fecha
    const fechaInput = document.getElementById('fecha');
    fechaInput.value = data.fecha;
    document.getElementById('campoFecha').classList.add('campo-resaltado');

    // Descripcion
    const descripInput = document.getElementById('descrip');
    descripInput.value = data.descripcion ?? '';
    document.getElementById('campoDescrip').classList.add('campo-resaltado');

    escanerEstado.className = 'escaner-estado ok';
    escanerEstado.innerHTML = 'Datos extraidos correctamente. Revisa y guarda.<br><button class="btn-nueva-foto" onclick="resetEscaner()">Usar otra imagen</button>';
}

function resetEscaner() {
    inputArchivo.value           = '';
    escanerDrop.style.display    = 'block';
    escanerPreview.style.display = 'none';
    ['campoCant','campoFecha','campoDescrip'].forEach(id =>
        document.getElementById(id).classList.remove('campo-resaltado')
    );
}
</script>
</body>
</html>
