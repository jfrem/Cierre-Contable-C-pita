// URL del endpoint del controlador
const url = "proyectos/cierrecontcap/controllers/controller.php";

// Función para obtener datos mediante fetch
async function getDataTbl(tableId, responseId) {
    const formData = new FormData();
    formData.append('funcion', tableId === 'tbl' ? 'getdataTbl' : 'getdataTbl2');

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.status}`);
        }

        const data = await response.text();
        $(`#${responseId}`).html(data);
        initializeDataTable(tableId);
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

// Inicializar DataTable
function initializeDataTable(tableId) {
    const createBadge = (className, icon, title) => `
    <span class="badge ${className}" style="cursor: pointer;" title="${title}">
        <i class="fas ${icon}"></i>
    </span>`;

    // Inicializar DataTable con opciones adicionales
    $(`#${tableId}`).DataTable({
        responsive: true,
        paging: false,
        dom: 't',
        columnDefs: [
            {
                // Aplica la clase 'text-end' y separador de miles a todas las columnas excepto la primera y la última
                targets: Array.from({ length: $(`#${tableId} thead th`).length - 4 }, (_, i) => i + 1),
                className: 'text-end',
                render: (data) => $.isNumeric(data) ? $.fn.dataTable.render.number('.', ',').display(data) : data,
            },
            {
                // Formatea la columna 8 (índice 8) para mostrar números como moneda
                targets: 8,
                className: 'text-end',                
                render: $.fn.dataTable.render.number('.', ',', 2, '$'),
            },
            {
                // Configura la columna 10 (índice 10) con botones de acción
                targets: 10,
                className: 'text-center',
                render: () => `
                    <div style="display: flex; justify-content: center; align-items: center;">
                        ${createBadge('bg-primary link-details', 'fa-stream', 'Detalles')}
                        ${createBadge('bg-success link-update', 'fa-sync', 'Actualizar')}
                    </div>`,
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            // Formatea cada columna en el footer
            const api = this.api();

            const colData = [2, 3, 4, 5, 6, 7];
            colData.forEach((index) => {
                const footerValue = api.column(index).footer().textContent;
                const formattedValue = $.fn.dataTable.render.number('.', ',').display(footerValue);
                $(api.column(index).footer()).html(`<strong>${formattedValue}</strong>`);
            });
            // Formateo de la columna 8 en el footer para mostrar como moneda
            const currency = api.column(8).footer().textContent;
            const formattedCurrency = $.fn.dataTable.render.number('.', ',', 2, '$').display(currency);
            $(api.column(8).footer()).html(`<strong>${formattedCurrency}</strong>`);
        },
    });
}

// Cargar datos al cargar la página
getDataTbl('tbl', 'tblresp');
getDataTbl('tbl2', 'tblresp2');
