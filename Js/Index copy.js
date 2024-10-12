// URL del endpoint del controlador
const url = "proyectos/cierrecontcap/controllers/controller.php";

// Función para renderizar el DataTable
const renderDataTable = (idTbl) => {
    $(idTbl).DataTable({
        responsive: true,
        paging: false,
        dom: 't',
        columnDefs: [
            {
                targets: [1, 2, 3, 4, 5, 6, 7, 8],
                className: 'text-end'
            },
            {
                targets: 8,
                render: $.fn.dataTable.render.number('.', ',', 2, '$'),
            }
        ],
        drawCallback: function () {
            const api = this.api();

            // Función para calcular totales de columnas
            const calculateTotals = (startCol, endCol, isCurrency = false) => {
                const totals = Array(endCol - startCol + 1).fill(0);

                // Iteramos sobre cada fila para sumar los valores de las columnas especificadas
                api.rows().every(function () {
                    const data = this.data();
                    for (let i = startCol; i <= endCol; i++) {
                        totals[i - startCol] += parseFloat(data[i]) || 0;
                    }
                });

                // Actualizar los pies de las columnas correspondientes
                totals.forEach((total, index) => {
                    const footerElement = $(api.column(startCol + index).footer());
                    footerElement.html($.fn.dataTable.render.number('.', ',', isCurrency ? 2 : 0, isCurrency ? '$' : '').display(total));
                });
            };

            // Calcular y mostrar los totales de las columnas
            calculateTotals(2, 7, false); 
            calculateTotals(8, 8, true);
        }
    });
};

// Función para obtener datos mediante fetch
async function fetchData(funcion, idResp, tableId) {
    const formData = new FormData();
    formData.append('funcion', funcion);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.status}`);
        }

        const data = await response.text();
        $(idResp).html(data);
        renderDataTable(tableId);
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

// Llamadas a la función genérica para obtener datos
fetchData('getdataTbl', '#tblresp', '#tbl');
fetchData('getdataTbl2', '#tblresp2', '#tbl2');
