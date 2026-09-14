<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$activePage = 'reportes';

include '../../includes/header.php';
?>

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-md-12">

            <h2>
                <i class="fas fa-file-alt text-primary"></i>
                Módulo de Reportes
            </h2>

            <p class="text-muted">
                Genere reportes del sistema por módulo y rango de fechas.
            </p>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="fas fa-filter"></i>

                Filtros del Reporte

            </h5>

        </div>

        <div class="card-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-4">

                        <label class="form-label">

                            Módulo

                        </label>

                        <select
                            name="modulo"
                            class="form-select"
                            required>

                            <option value="">Seleccione...</option>

                            <option value="galpones">Galpones</option>

                            <option value="agua">Agua</option>

                            <option value="alimento">Alimento</option>

                            <option value="amoniaco">Amoniaco</option>

                            <option value="mortalidad">Mortalidad</option>

                            <option value="inventario">Inventario</option>

                            <option value="usuarios">Usuarios</option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            Fecha Inicio

                        </label>

                        <input
                            type="date"
                            name="inicio"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            Fecha Final

                        </label>

                        <input
                            type="date"
                            name="fin"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-2 d-grid">

                        <label>&nbsp;</label>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-search"></i>

                            Consultar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

<?php

if(
    isset($_GET['modulo']) &&
    isset($_GET['inicio']) &&
    isset($_GET['fin'])
){

?>

<div class="card shadow mt-4">

<div class="card-header">

<h5>

Resultado del Reporte

</h5>

</div>

<div class="card-body">

<p>

<strong>Módulo:</strong>

<?= ucfirst($_GET['modulo']) ?>

</p>

<p>

<strong>Desde:</strong>

<?= $_GET['inicio'] ?>

&nbsp;&nbsp;

<strong>Hasta:</strong>

<?= $_GET['fin'] ?>

</p>

<div class="mt-4">

<a

href="pdf.php?modulo=<?= $_GET['modulo'] ?>&inicio=<?= $_GET['inicio'] ?>&fin=<?= $_GET['fin'] ?>"

class="btn btn-danger">

<i class="fas fa-file-pdf"></i>

Generar PDF

</a>

<a

href="excel.php?modulo=<?= $_GET['modulo'] ?>&inicio=<?= $_GET['inicio'] ?>&fin=<?= $_GET['fin'] ?>"

class="btn btn-success">

<i class="fas fa-file-excel"></i>

Generar Excel

</a>

</div>

</div>

</div>

<?php } ?>

</div>

<?php include '../../includes/footer.php'; ?>