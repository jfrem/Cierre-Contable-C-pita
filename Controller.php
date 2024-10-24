<?php
require_once __DIR__ . '/Model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/tools/sqltoexcel/index.php';
class Controller
{
    private $model;
    private $xlsx;

    public function __construct()
    {
        $this->model = new Model();
        $this->xlsx = new Xlsx();
    }
    public function getYears()
    {
        $result = $this->model->getYears();
        foreach ($result as $anio) {
            echo '<option value="' . $anio['PERMESANO'] . '">' . $anio['PERMESANO'] . '</option>';
        }
    }
    public function getdataTbl()
    {
        $data = $this->model->getdataTbl($_POST);
        $this->renderTable($data, 'tbl', true);
    }
    public function getdataTbl2()
    {
        $data = $this->model->getdataTbl2($_POST);
        $this->renderTable($data, 'tbl2', true);
    }
    public function updateData()
    {
        $result = $this->model->updateData($_POST);

        if ($result === false) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos.']);
        } else {
            echo json_encode(['success' => true, 'data' => $result]);
        }
    }
    /**
     * Maneja la solicitud para obtener detalles y generar un archivo Excel.
     *
     * Este método invoca la generación de un archivo Excel que contiene
     * los detalles obtenidos mediante el método 'getDetails' del modelo.
     */
    public function getDetails()
    {
        $this->exportDataToExcel('getDetails');
    }

    /**
     * Maneja la solicitud para obtener detalles de una tabla y generar un archivo Excel.
     *
     * Este método invoca la generación de un archivo Excel que contiene
     * los detalles obtenidos mediante el método 'getDetailstbl' del modelo.
     */
    public function getDetailstbl()
    {
        $this->exportDataToExcel('getDetailstbl');
    }

    /**
     * Genera un archivo Excel basado en el método especificado del modelo.
     *
     * Este método configura el entorno de ejecución y llama al método del modelo
     * correspondiente para obtener los datos necesarios. Si se encuentran resultados,
     * se genera un archivo Excel y se proporciona una URL para su descarga.
     *
     * @param string $method Nombre del método del modelo que se va a invocar.
     * @return void
     */
    public function exportDataToExcel($method)
    {
        // Configuración del tiempo máximo de ejecución y límite de memoria
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        // Invocación del método del modelo para obtener los datos
        $result = $this->model->$method($_POST);
        if ($result) {
            $filePath = 'proyectos/cierrecontcap/informe.xlsx';
            $this->xlsx->CrearXlsx('sql', $result); // Generación del archivo Excel
            echo json_encode(['success' => true, 'fileUrl' => $filePath, 'message' => 'Descarga del archivo Excel exitosa.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron resultados.']);
        }
    }


    /**
     * Renderiza una tabla HTML a partir de los datos proporcionados.
     *
     * Este método incluye una vista para generar la tabla, 
     * utilizando los datos que se le pasan como argumento. 
     * Si se solicita, se puede incluir un pie de tabla.
     *
     * @param array $data Los datos a mostrar en la tabla. 
     *                    Debe ser un array asociativo donde cada elemento 
     *                    representa una fila de la tabla.
     * @param string $tableId El ID que se asignará a la tabla HTML. 
     * @param bool $footer Indica si se debe mostrar un pie de tabla.
     *                     Si es true, se mostrará una fila con "Total:" 
     *                     en la primera celda.
     *
     * @return void No retorna ningún valor.
     */
    private function renderTable($data, $tableId, $footer = false)
    {
        // Incluye la vista de la tabla ubicada en la carpeta 'Views'
        include __DIR__ . '/Views/table.php';
    }
}

if (isset($_POST['funcion'])) {
    call_user_func([new Controller, $_POST['funcion']]);
}
