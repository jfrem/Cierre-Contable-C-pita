// URL del endpoint del controlador
const url = "proyectos/cierrecontcap/controller.php";

// Función para renderizar el loader
const renderLoader = (tblResponse) => {
    $(tblResponse).html(`
        <i class="fa fa-spinner fa-spin fa-2x fa-fw" 
           style="z-index: 1050; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        </i>
    `);
};

// Función para obtener datos mediante fetch
async function getDataTbl(funcion, tableId, respId) {
    // Renderizar el loader
    renderLoader(`#${respId}`);

    const formData = new FormData();
    formData.append('anio', $('#yearSelect').val());
    formData.append('funcion', funcion);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.text();
        $(`#${respId}`).html(data);

        const createBadge = (className, icon, title) => `
            <span class="badge ${className}" style="cursor: pointer;" title="${title}">
                <i class="fas ${icon}"></i>
            </span>`;

        const columnDefs = [
            {
                targets: Array.from({ length: $(`#${tableId} thead th`).length - 4 }, (_, i) => i + 1),
                className: 'text-end',
                render: data => $.isNumeric(data) ? $.fn.dataTable.render.number('.', ',').display(data) : data,
            },
            {
                targets: 8,
                className: 'text-end',
                render: $.fn.dataTable.render.number('.', ',', 2, '$'),
            },
            {
                targets: tableId === 'tbl' ? 10 : 9,
                className: 'text-center',
                render: () => `
                    <div style="display: flex; justify-content: center; align-items: center;">
                        ${createBadge(tableId === 'tbl' ? 'bg-primary link-details' : 'bg-primary link-detailsbtl', 'fa-stream', 'Detalles')}
                        ${tableId === 'tbl' ? createBadge('bg-success link-update', 'fa-sync', 'Actualizar') : ''}
                    </div>`,
            },
        ];

        const table = $(`#${tableId}`).DataTable({
            responsive: true,
            paging: false,
            dom: 't',
            columnDefs,
            initComplete: function () {
                const colors = ['table-danger', 'table-success', 'table-danger', 'table-success', 'table-info', 'table-secondary', 'table-light'];
                const startIdx = 3;
                const endIdx = 7;

                this.api().columns().every(function (index) {
                    if (index >= startIdx && index <= endIdx) {
                        $(this.header()).addClass(colors[(index - startIdx) % colors.length]);
                    }
                });
            },
            footerCallback: function () {
                const api = this.api();
                const colData = [2, 3, 4, 5, 6, 7];

                colData.forEach(index => {
                    const footerValue = api.column(index).footer().textContent;
                    $(api.column(index).footer()).html(`<strong>${$.fn.dataTable.render.number('.', ',').display(footerValue)}</strong>`);
                });

                const currency = api.column(8).footer().textContent;
                $(api.column(8).footer()).html(`<strong>${$.fn.dataTable.render.number('.', ',', 2, '$').display(currency)}</strong>`);
            },
        });
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

// Función para manejar clic en detalles de las tablas
$(document).on('click', '.link-details, .link-detailsbtl', async function (event) {
    event.preventDefault();

    const $row = $(this).closest('tr');
    const $table = $row.closest('table');
    const table = $table.DataTable();
    const rowData = table.row($row).data();
    const [anio, mes] = rowData;

    // Determinar función del controlador y ID de la tabla
    const isDetails = $(this).hasClass('link-details');
    const funcion = isDetails ? 'getDetails' : 'getDetailstbl';
    const tblId = isDetails ? 'tbl3' : 'tbl4';
    $row.addClass('table-success');

    // Cambiar el ícono del botón a un ícono de carga
    const originalIcon = $(this).find('i').attr('class'); // Guarda la clase original
    $(this).find('i').attr('class', 'fas fa-spinner fa-spin'); // Cambia al ícono de carga
    $(this).prop('disabled', true).attr('aria-busy', 'true'); // Deshabilita el botón

    // Preparar datos para la solicitud
    const formData = new FormData();
    formData.append('funcion', funcion);
    formData.append('anio', anio);
    formData.append('mes', mes);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const data = await response.json();
        console.log(data);

        // Redirigir solo si la operación fue exitosa
        if (data.success) {
            // window.location.href = 'proyectos/cierrecontcap/informe.xlsx';
            window.location.href = data.fileUrl;
        } else {
            alert(data.message || 'No se pudo generar el informe.');
        }

    } catch (error) {
        console.error("Error en la solicitud:", error);
        alert(`Ocurrió un error al actualizar los datos. Por favor, inténtelo de nuevo. Detalle: ${error.message}`);
    } finally {
        // Rehabilitar el elemento y eliminar la clase, independientemente del resultado
        $(this).prop('disabled', false).attr('aria-busy', 'false'); // Rehabilita el botón y marca como no ocupado
        $(this).find('i').attr('class', originalIcon); // Restaura el ícono original
        $row.removeClass('table-success');
    }
});

// Función para eliminar el archivo informe.xlsx creado
function delFile() {
    const formData = new FormData();
    formData.append('funcion', 'deleteFile');

    fetch(url, {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error(`Error: ${response.status} - ${response.statusText}`);
        }
    })
    console.log('File deleted successfully');
}

// Función para actualizar datos mediante AJAX
$(document).on('click', '.link-update', async function (event) {
    event.preventDefault();

    const $row = $(this).closest('tr');
    const $table = $row.closest('table');
    const table = $table.DataTable();
    const [anio, mes] = table.row($row).data();

    const formData = new FormData();
    formData.append('funcion', 'updateData');
    formData.append('anio', anio);
    formData.append('mes', mes);

    $row.addClass('table-success');

    renderLoader(`#tblresp`);
    renderLoader(`#tblresp2`);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.status} - ${response.statusText}`);
        }

        const data = await response.json();
        console.log(data);
        getDataTbl('getdataTbl', 'tbl', 'tblresp');
        getDataTbl('getdataTbl2', 'tbl2', 'tblresp2');

        setTimeout(() => $row.removeClass('table-success'), 2000);
    } catch (error) {
        console.error("Error en la solicitud:", error);
        alert("Ocurrió un error al actualizar los datos. Por favor, inténtelo de nuevo.");
    }
});



// Función para manejar el cambio de año
$(document).on('change', '#yearSelect', function (event) {
    const anio = $(this).val();
    console.log(anio);

    getDataTbl('getdataTbl', 'tbl', 'tblresp');
    getDataTbl('getdataTbl2', 'tbl2', 'tblresp2');

});
