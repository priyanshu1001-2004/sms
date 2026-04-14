$(function () {
    "use strict";

    // ================================
    // ✅ GLOBAL SAAS DATATABLE FUNCTION
    // ================================
    function initDataTable(selector) {

        let columnsConfig = [];
        let defaultOrder = [];

        // read settings from <th>
        $(selector + ' thead th').each(function (index) {
            let isSortable = $(this).data('orderby') === true;
            let defaultDir = $(this).data('order-default');

            columnsConfig.push({
                orderable: isSortable
            });

            if (defaultDir) {
                defaultOrder.push([index, defaultDir]);
            }
        });

        // prevent re-initialization
        if ($.fn.DataTable.isDataTable(selector)) {
            return;
        }

        $(selector).DataTable({
            dom: 't',              // only table (clean UI)
            searching: false,
            lengthChange: false,
            paging: true,
            info: true,

            columns: columnsConfig,
            order: defaultOrder,

            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });
    }

    // ================================
    // ✅ AUTO APPLY TO ALL TABLES
    // ================================
    $('.saas-table').each(function () {
        let tableId = '#' + $(this).attr('id');
        initDataTable(tableId);
    });

    // ================================
    // ✅ OPTIONAL (SPECIAL TABLES ONLY)
    // ================================

    // Export table (keep separate if needed)
    if ($('#file-datatable').length) {
        let table = $('#file-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'colvis']
        });

        table.buttons().container()
            .appendTo('#file-datatable_wrapper .col-md-6:eq(0)');
    }

    // Delete row table (optional feature)
    if ($('#delete-datatable').length) {
        let table = $('#delete-datatable').DataTable();

        $('#delete-datatable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
        });

        $('#button').on('click', function () {
            table.row('.selected').remove().draw(false);
        });
    }

    // ================================
    // ✅ SELECT2
    // ================================
    $('.select2').select2({
        minimumResultsForSearch: Infinity
    });

});