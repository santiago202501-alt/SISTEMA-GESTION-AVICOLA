<?php
/**
 * SIGA - Dashboard Principal
 */
require_once __DIR__ . '/config/database.php';  
require_once __DIR__ . '/config/session.php';
requireLogin();

$db = getDB();
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// ── Estadísticas generales ────────────────────────────────────
$totalGalpones = $db->query("SELECT COUNT(*) FROM galpones")->fetchColumn();
$totalPollos   = $db->query("SELECT SUM(cantidad_actual) FROM galpones")->fetchColumn() ?? 0;
$totalAguaHoy  = $db->query("SELECT COALESCE(SUM(litros),0) FROM registros_agua WHERE fecha=CURDATE()")->fetchColumn();
$totalAlimHoy  = $db->query("SELECT COALESCE(SUM(kilogramos),0) FROM registros_alimento WHERE fecha=CURDATE()")->fetchColumn();
$totalMort     = $db->query("SELECT COALESCE(SUM(cantidad),0) FROM mortalidad WHERE MONTH(fecha)=MONTH(CURDATE())")->fetchColumn();
$alertasActivas= $db->query("SELECT COUNT(*) FROM alertas WHERE leida=0")->fetchColumn();

// ── Últimas alertas ───────────────────────────────────────────
$alertas = $db->query(
    "SELECT a.*, g.nombre AS galpon_nombre
     FROM alertas a JOIN galpones g ON g.id=a.galpon_id
     WHERE a.leida=0 ORDER BY a.created_at DESC LIMIT 5"
)->fetchAll();

// ── Galpones resumen ──────────────────────────────────────────
$galpones = $db->query(
    "SELECT g.*,
       COALESCE((SELECT SUM(litros) FROM registros_agua
                 WHERE galpon_id=g.id AND fecha=CURDATE()),0) AS agua_hoy,
       COALESCE((SELECT SUM(kilogramos) FROM registros_alimento
                 WHERE galpon_id=g.id AND fecha=CURDATE()),0) AS alimento_hoy,
       COALESCE((SELECT MAX(nivel_ppm) FROM registros_amoniaco
                 WHERE galpon_id=g.id AND fecha=CURDATE()),0) AS amoniaco_hoy
     FROM galpones g ORDER BY g.codigo"
)->fetchAll();

// ── Datos Chart: consumo agua últimos 7 días ──────────────────
$aguaChart = $db->query(
    "SELECT DATE_FORMAT(fecha,'%d/%m') AS dia, COALESCE(SUM(litros),0) AS total
     FROM registros_agua
     WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     GROUP BY fecha ORDER BY fecha"
)->fetchAll();

// ── Datos Chart: consumo alimento últimos 7 días ──────────────
$alimChart = $db->query(
    "SELECT DATE_FORMAT(fecha,'%d/%m') AS dia, COALESCE(SUM(kilogramos),0) AS total
     FROM registros_alimento
     WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     GROUP BY fecha ORDER BY fecha"
)->fetchAll();

// ── Mortalidad por galpón (mes actual) ───────────────────────
$mortChart = $db->query(
    "SELECT g.nombre, COALESCE(SUM(m.cantidad),0) AS total
     FROM galpones g
     LEFT JOIN mortalidad m ON m.galpon_id=g.id AND MONTH(m.fecha)=MONTH(CURDATE())
     GROUP BY g.id ORDER BY g.codigo"
)->fetchAll();

// ── Amoníaco por galpón hoy ──────────────────────────────────
$amonChart = $db->query(
    "SELECT g.nombre, COALESCE(MAX(a.nivel_ppm),0) AS ppm
     FROM galpones g
     LEFT JOIN registros_amoniaco a ON a.galpon_id=g.id AND a.fecha=CURDATE()
     GROUP BY g.id ORDER BY g.codigo"
)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── STATS ROW ── -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fa-solid fa-warehouse"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= $totalGalpones ?></div>
        <div class="stat-label">Galpones</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon green"><i class="fa-solid fa-drumstick-bite"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalPollos) ?></div>
        <div class="stat-label">Pollos totales</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon teal"><i class="fa-solid fa-droplet"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalAguaHoy) ?></div>
        <div class="stat-label">Litros hoy</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon amber"><i class="fa-solid fa-wheat-awn"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalAlimHoy) ?> <small style="font-size:.8rem">kg</small></div>
        <div class="stat-label">Alimento hoy</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon red"><i class="fa-solid fa-skull-crossbones"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalMort) ?></div>
        <div class="stat-label">Mortalidad mes</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon purple"><i class="fa-solid fa-bell"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= $alertasActivas ?></div>
        <div class="stat-label">Alertas activas</div>
      </div>
    </div>
  </div>
</div>

<!-- ── ROW: CHARTS ── -->
<div class="row g-4 mb-4">
  <!-- Agua -->
  <div class="col-md-6">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-droplet"></i> Consumo de agua (7 días)</span>
      </div>
      <div class="siga-card-body">
        <div class="chart-wrapper"><canvas id="chartAgua"></canvas></div>
      </div>
    </div>
  </div>
  <!-- Alimento -->
  <div class="col-md-6">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-wheat-awn"></i> Consumo de alimento (7 días)</span>
      </div>
      <div class="siga-card-body">
        <div class="chart-wrapper"><canvas id="chartAlim"></canvas></div>
      </div>
    </div>
  </div>
  <!-- Mortalidad por galpón -->
  <div class="col-md-6">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-chart-bar"></i> Mortalidad por galpón (mes actual)</span>
      </div>
      <div class="siga-card-body">
        <div class="chart-wrapper"><canvas id="chartMort"></canvas></div>
      </div>
    </div>
  </div>
  <!-- Amoníaco -->
  <div class="col-md-6">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-wind"></i> Nivel de amoníaco hoy (ppm)</span>
      </div>
      <div class="siga-card-body">
        <div class="chart-wrapper"><canvas id="chartAmon"></canvas></div>
      </div>
    </div>
  </div>
</div>

<!-- ── ROW: Alertas + Galpones ── -->
<div class="row g-4">
  <!-- Alertas recientes -->
  <div class="col-md-5">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-triangle-exclamation"></i> Alertas recientes</span>
        <a href="<?= BASE_URL ?>/modules/alertas/index.php" class="btn btn-sm btn-outline-secondary">Ver todas</a>
      </div>
      <div class="siga-card-body">
        <?php if (empty($alertas)): ?>
          <div class="text-center py-4 text-muted">
            <i class="fa-solid fa-check-circle fa-2x mb-2 text-success"></i>
            <p>Sin alertas activas</p>
          </div>
        <?php else: ?>
          <?php foreach ($alertas as $a): ?>
            <div class="alerta-item <?= e($a['nivel']) ?>">
              <i class="fa-solid <?= $a['nivel']==='critica' ? 'fa-circle-xmark' : ($a['nivel']==='advertencia'?'fa-triangle-exclamation':'fa-circle-info') ?>"></i>
              <div>
                <div style="font-weight:600;font-size:.8rem"><?= e($a['galpon_nombre']) ?></div>
                <div><?= e($a['mensaje']) ?></div>
                <div style="font-size:.72rem;color:#94a3b8;margin-top:2px"><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Estado galpones -->
  <div class="col-md-7">
    <div class="siga-card">
      <div class="siga-card-header">
        <span class="siga-card-title"><i class="fa-solid fa-warehouse"></i> Estado de galpones hoy</span>
        <a href="<?= BASE_URL ?>/modules/galpones/index.php" class="btn btn-sm btn-outline-secondary">Gestionar</a>
      </div>
      <div class="siga-card-body p-0">
        <div class="table-responsive">
          <table class="table table-siga mb-0">
            <thead>
              <tr>
                <th>Galpón</th><th>Pollos</th><th>Agua (L)</th>
                <th>Alimento (kg)</th><th>NH₃ (ppm)</th><th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($galpones as $g): ?>
              <tr>
                <td><strong><?= e($g['codigo']) ?></strong><br><small class="text-muted"><?= e($g['nombre']) ?></small></td>
                <td>
                  <?= number_format($g['cantidad_actual']) ?>
                  <?php if ($g['cantidad_actual'] > $g['capacidad_maxima']): ?>
                    <i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="Sobrepoblación"></i>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="<?= $g['agua_hoy'] < ALERTA_AGUA_LITROS && $g['agua_hoy']>0 ? 'text-danger fw-600' : '' ?>">
                    <?= number_format($g['agua_hoy'],0) ?>
                  </span>
                </td>
                <td>
                  <span class="<?= $g['alimento_hoy'] < ALERTA_ALIMENTO_KG && $g['alimento_hoy']>0 ? 'text-danger fw-600' : '' ?>">
                    <?= number_format($g['alimento_hoy'],1) ?>
                  </span>
                </td>
                <td>
                  <span class="<?= $g['amoniaco_hoy'] > ALERTA_AMONIACO_PPM ? 'text-danger fw-600' : '' ?>">
                    <?= number_format($g['amoniaco_hoy'],1) ?>
                    <?php if ($g['amoniaco_hoy'] > ALERTA_AMONIACO_PPM): ?>
                      <i class="fa-solid fa-circle-exclamation text-danger ms-1"></i>
                    <?php endif; ?>
                  </span>
                </td>
                <td>
                  <span class="badge-estado badge-<?= $g['estado'] ?>">
                    <?= ucfirst($g['estado']) ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// ── JSON para gráficas ────────────────────────────────────────
$js_agua_labels = json_encode(array_column($aguaChart,'dia'));
$js_agua_data   = json_encode(array_column($aguaChart,'total'));
$js_alim_labels = json_encode(array_column($alimChart,'dia'));
$js_alim_data   = json_encode(array_column($alimChart,'total'));
$js_mort_labels = json_encode(array_column($mortChart,'nombre'));
$js_mort_data   = json_encode(array_column($mortChart,'total'));
$js_amon_labels = json_encode(array_column($amonChart,'nombre'));
$js_amon_data   = json_encode(array_column($amonChart,'ppm'));

$extraJs = <<<JS
<script>
// Chart Agua
new Chart(document.getElementById('chartAgua'), {
  type:'bar',
  data:{
    labels: {$js_agua_labels},
    datasets:[{label:'Litros',data:{$js_agua_data},backgroundColor:'rgba(13,148,136,.7)',borderRadius:6}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
// Chart Alimento
new Chart(document.getElementById('chartAlim'), {
  type:'line',
  data:{
    labels:{$js_alim_labels},
    datasets:[{label:'Kg',data:{$js_alim_data},borderColor:'rgba(230,126,34,1)',backgroundColor:'rgba(230,126,34,.15)',fill:true,tension:.4,pointRadius:5}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
// Chart Mortalidad
new Chart(document.getElementById('chartMort'), {
  type:'bar',
  data:{
    labels:{$js_mort_labels},
    datasets:[{label:'Muertes',data:{$js_mort_data},backgroundColor:['rgba(192,57,43,.75)','rgba(231,76,60,.75)','rgba(203,67,53,.75)','rgba(169,50,38,.75)']}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
});
// Chart Amoníaco
new Chart(document.getElementById('chartAmon'), {
  type:'bar',
  data:{
    labels:{$js_amon_labels},
    datasets:[{label:'ppm',data:{$js_amon_data},
      backgroundColor: {$js_amon_data}.map(v => v > 25 ? 'rgba(192,57,43,.82)' : 'rgba(42,82,152,.72)'),
      borderRadius:6}]
  },
  options:{
    responsive:true,maintainAspectRatio:false,
    plugins:{legend:{display:false}},
    scales:{y:{beginAtZero:true,max:60}},
    plugins:{
      annotation:{annotations:{lineLimite:{type:'line',yMin:25,yMax:25,borderColor:'red',borderWidth:2,borderDash:[6,4]}}}
    }
  }
});
</script>
JS;

require_once __DIR__ . '/includes/footer.php';
?>
