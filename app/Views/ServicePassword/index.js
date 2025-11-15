import {
    initTable,
    initDelete,
    initForm,
    showMessage,
    handleAjaxResponse,
    handleAjaxError
} from '../../../public/js/app.js';

export function initModule() {

    initForm($('#form_block'), '/api/servicepassword/add/', '', '', false);

}
