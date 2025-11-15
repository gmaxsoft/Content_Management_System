
import {
    initTable,
    initDelete,
    initForm,
    showMessage,
    handleAjaxResponse,
    handleAjaxError
} from '../../../public/js/app.js';

export function initModule() {

    initForm($('#form_block_edit'), '/api/users/update/', '', '', false);
    initForm($('#form_passwd'), '/api/users/store_password/', '', '', false);

    $(".backtolist").on('click', function () {
        window.location = "/users";
    });
}