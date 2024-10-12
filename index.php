<?php

require_once 'Controllers/Controller.php';
$controller = new Controller();

?>
<!doctype html>
<html lang="es">

<head>
    <title>Cierre Contable Cápita</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Your custom styles (optional) -->
    <style>
        .table-sm td,
        .table-sm th {
            padding: .1rem;
            font-size: 12px;
        }

        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #f1f1f1;
            padding: 20px 0;
            text-align: center;
            z-index: 99;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer small {
            color: #333;
            font-size: 0.875rem;
        }

        .breadcrumb {
            margin-bottom: 0;
            background-color: #dfdfdf;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: '/';
        }

        .link-details,
        .link-update {
            margin: 0 5px;
            /* Espaciado entre los botones */
            padding: 5px 10px;
            /* Espaciado interno */
            border-radius: 4px;
            /* Bordes redondeados */
            transition: background-color 0.3s;
            /* Transición suave */
        }

        .link-details:hover,
        .link-update:hover {
            background-color: #e0e0e0;
            /* Color de fondo al pasar el mouse */
        }
    </style>
</head>

<body>
    <nav aria-label="breadcrumb" class="bg-white p-3 rounded shadow-sm">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/principal.php?p=gerenciaVisual/Vprincipal1" class="text-primary">
                    <i class="fas fa-home"></i> Contabilidad
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Cierre Contable Cápita</li>
        </ol>
    </nav>
    <div class="container-fluid">
        <div class="d-flex flex-wrap gap-4 align-items-center justify-content-between mt-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-secondary me-2"></i>
                <select class="form-select w-auto" id="yearSelect">
                    <option value="">Seleccionar año</option>
                    <?php $controller->getYears(); ?>
                </select>
            </div>
        </div>
        <div class="row mt-5">
            <!-- Fecha de Creación -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded bg-light h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-size: 1.25rem;" id="fechaCreacionTab">Fecha de Creación</h3>
                        <div class="table-responsive-sm" id="tblresp" aria-labelledby="fechaCreacionTab">
                            <!-- Aquí va el contenido de la tabla de fechas de creación -->
                            <div class="loading-spinner" aria-live="polite" style="display:none;">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <!-- Contenido dinámico de la tabla -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fecha de Modificación -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded bg-light h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-size: 1.25rem;" id="fechaModificacionTab">Fecha de Modificación</h3>
                        <div class="table-responsive-sm" id="tblresp2" aria-labelledby="fechaModificacionTab">
                            <!-- Aquí va el contenido de la tabla de fechas de modificación -->
                            <div class="loading-spinner" aria-live="polite" style="display:none;">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <!-- Contenido dinámico de la tabla -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Details -->
        <div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí va el contenido dinámico del modal -->
                        <div class="table-responsive-sm" id="tblresp3">
                            <!-- Contenido dinámico del modal -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer mt-5 py-3 bg-light text-center text-muted shadow-sm rounded">
            <small>Cierre Contable Cápita. © Copyright <?php echo date('Y'); ?>, J-Frem</small>
        </footer>
    </div>


    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.es.min.js"></script>
    <script src="proyectos/cierrecontcap/js/index.js"></script>
</body>

</html>