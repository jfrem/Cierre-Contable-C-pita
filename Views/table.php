<?php

if (!empty($data)) {
    // Inicializar un arreglo para almacenar los totales de las columnas numéricas
    $totals = [];

    echo "<table class=\"table table-striped table-sm table-bordered table-striped border-primary align-middle mb-0 bg-white\" id=\"$tableId\">";
    echo "<thead class=\"bg-light\">";
    echo "<tr>";

    // Generar encabezados de la tabla
    foreach (array_keys($data[0]) as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Generar filas de la tabla
    foreach ($data as $row) {
        echo "<tr>";
        $columnIndex = 0;
        foreach ($row as $cell) {
            echo "<td>" . $cell . "</td>";

            // Sumar los valores numéricos para las columnas correspondientes
            if (is_numeric($cell)) {
                if (!isset($totals[$columnIndex])) {
                    $totals[$columnIndex] = 0;
                }
                $totals[$columnIndex] += $cell;
            }
            $columnIndex++;
        }
        echo "</tr>";
    }

    echo "</tbody>";

    // Generar el pie de tabla si se requiere
    if ($footer) {
        echo "<tfoot>";
        echo "<tr>";
        echo "<td class=\"total-cell\"><strong>Total:</strong></td>";

        // Generar las celdas para los totales
        $columnCount = count($data[0]);
        for ($i = 1; $i < $columnCount; $i++) {
            if (isset($totals[$i])) {
                echo "<td class=\"text-end\"><strong>" . $totals[$i] . "</strong></td>";
            } else {
                echo "<td class=\"text-end\"></td>";
            }
        }
        echo "</tr>";
        echo "</tfoot>";
    }

    echo "</table>";
} else {
    echo "<div class=\"alert alert-warning\">No hay datos disponibles.</div>";
}
