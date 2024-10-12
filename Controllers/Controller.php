<?php
require_once __DIR__ . '/../Models/Model.php';
class Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Model();
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
    public function getDetails()
    {
        $result = $this->model->getDetails($_POST);
        $this->renderTable($result, 'tbl3', false);
    }
    public function getDetailstbl()
    {
        $result = $this->model->getDetailstbl($_POST);
        $this->renderTable($result, 'tbl3', false);
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
        include __DIR__ . '/../Views/table.php';
    }
}

if (isset($_POST['funcion'])) {
    call_user_func([new Controller, $_POST['funcion']]);
}
