// URL del endpoint del controlador
const url = "proyectos/cierrecontcap/controllers/controller.php";

// Función para obtener datos mediante fetch
async function getDataTbl(funcion, tableId, respId) {
    const formData = new FormData();
    formData.append('anio', $('#yearSelect').val());
    formData.append('funcion', funcion);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const data = await response.text();
        $(`#${respId}`).html(data);

        const createBadge = (className, icon, title) => `
            <span class="badge ${className}" style="cursor: pointer;" title="${title}">
                <i class="fas ${icon}"></i>
            </span>`;

        $(`#${tableId}`).DataTable({
            responsive: true,
            paging: false,
            dom: 't',
            columnDefs: [
                {
                    targets: Array.from({ length: $(`#${tableId} thead th`).length - 4 }, (_, i) => i + 1),
                    className: 'text-end',
                    render: (data) => $.isNumeric(data) ? $.fn.dataTable.render.number('.', ',').display(data) : data,
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
                        ${tableId === 'tbl' ? createBadge('bg-primary link-details', 'fa-stream', 'Detalles') : createBadge('bg-primary link-detailsbtl', 'fa-stream', 'Detalles')}
                        ${tableId === 'tbl' ? createBadge('bg-success link-update', 'fa-sync', 'Actualizar') : ''}
                    </div>`,
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                const api = this.api();
                const colData = [2, 3, 4, 5, 6, 7];
                colData.forEach((index) => {
                    const footerValue = api.column(index).footer().textContent;
                    const formattedValue = $.fn.dataTable.render.number('.', ',').display(footerValue);
                    $(api.column(index).footer()).html(`<strong>${formattedValue}</strong>`);
                });
                const currency = api.column(8).footer().textContent;
                const formattedCurrency = $.fn.dataTable.render.number('.', ',', 2, '$').display(currency);
                $(api.column(8).footer()).html(`<strong>${formattedCurrency}</strong>`);
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
    console.log(rowData);

    $('#modalDetails').modal('show');
    // Determinar la función del controlador según la clase del elemento
    const funcion = $(this).hasClass('link-details') ? 'getDetails' : 'getDetailstbl';
    console.log(funcion);

    const formData = new FormData();
    formData.append('funcion', funcion);
    formData.append('anio', rowData[0]);
    formData.append('mes', rowData[1]);

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const data = await response.text();
        $('#tblresp3').html(data);

        if ($.fn.DataTable.isDataTable('#tbl3')) {
            $('#tbl3').DataTable().destroy();
        }

        $('#tbl3').DataTable({
            paging: false,
            scrollX: true,
            searching: false,
            buttons: true,
            info: false,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Descargar Excel',
                    title: 'Datos',
                    filename: 'datos'
                }
            ]
        });
    } catch (error) {
        console.error("Error en la solicitud:", error);
        alert("Ocurrió un error al actualizar los datos. Por favor, inténtelo de nuevo.");
    }
});

// Función para actualizar datos mediante AJAX
$(document).on('click', '.link-update', async function (event) {
    event.preventDefault();

    const $row = $(this).closest('tr');
    const $table = $row.closest('table');
    const table = $table.DataTable();
    const rowData = table.row($row).data();

    const formData = new FormData();
    formData.append('funcion', 'updateData');
    formData.append('anio', rowData[0]);
    formData.append('mes', rowData[1]);

    $row.addClass('table-success');

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

        setTimeout(() => $row.removeClass('table-success'), 2000);
    } catch (error) {
        console.error("Error en la solicitud:", error);
        alert("Ocurrió un error al actualizar los datos. Por favor, inténtelo de nuevo.");
    }
});

// Función para manejar el cambio de año
$(document).on('change', '#yearSelect', function (event) {
    const value = $(this).val();
    console.log(value);

    getDataTbl('getdataTbl', 'tbl', 'tblresp');
    getDataTbl('getdataTbl2', 'tbl2', 'tblresp2');
});
