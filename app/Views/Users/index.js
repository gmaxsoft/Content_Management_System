import {
    initTable,
    initDelete,
    initForm,
    showMessage,
    handleAjaxResponse,
    handleAjaxError
} from '../../../public/js/app.js';

export function initModule() {

    // Tabela główna
    const mainTableColumns = [
        { field: 'state', sortable: false, width: '40px', align: 'center', valign: 'middle' },
        { field: 'action', title: 'Akcja', sortable: false, class: 'action nowrap', width: '40px' },
        { field: 'user_id', sortable: true, width: '40px' },
        { field: 'user_first_name', sortable: true, class: 'nowrap' },
        { field: 'user_last_name', sortable: true, class: 'nowrap' },
        { field: 'user_email', sortable: true, class: 'nowrap' },
        { field: 'user_phone', sortable: true, class: 'nowrap' },
        { field: 'user_stand_name', sortable: true, class: 'nowrap' },
        { field: 'user_symbol', sortable: true, class: 'nowrap' },
        { field: 'user_level', sortable: true, class: 'nowrap' },
        { field: 'user_active', sortable: true, class: 'nowrap' }
    ];
    initTable($('#datatable'), '/api/users/grid/', mainTableColumns);

    // Usuwanie
    initDelete($('#datatable'), $('#remove'), '/api/users/remove/', 'user_id', 'Czy na pewno chcesz usunąć użytkownika?');

    initForm($('#form_block'), '/api/users/add/', $('#datatable'), '/api/users/grid/');

    $(".backtolist").on('click', function () {
        window.location = "/users";
    });
}