//import $ from 'jquery';
import { MenuEditor, initializeIconPicker } from '@maxsoft/bootstrap_menu_editor';

export function initModule() {

    let btnAdd = $("#btnAdd");
    let btnUpdate = $("#btnUpdate");

    const menuEditor = new MenuEditor("menucms", { maxLevel: 3 });

    menuEditor.onClickDelete((t) => {
        $.confirm({
            title: 'Usuwanie!',
            content: 'Czy na pewno chesz usunąć ' + t.item.getDataset().text + ' z nawigacji? ',
            buttons: {
                confirm: {
                    text: 'Tak',
                    action: function () {

                        t.item.remove();
                        let output = menuEditor.getString();
                        fetch("/api/navigationcms/store/",
                            {
                                method: "post",
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: output,
                            });

                    }
                },
                cancel: {
                    text: 'Anuluj',
                    action: function () {

                    }
                }
            }
        });
    });

    menuEditor.onClickEdit((event) => {
        $("#btnUpdate").removeAttr("disabled");

        // get the dataset from the clicked item
        let itemData = event.item.getDataset();
        console.log(itemData);
        menuEditor.edit(event.item); // set the item in edit mode
        $("#txtText").val(itemData.text);
        $("#txtHref").val(itemData.href);
        $("#txtIcon").val(itemData.icon);
        $("#txtTooltip").val(itemData.tooltip);
    });

    menuEditor.onDragEnd((event) => {
        let output = menuEditor.getString();
        console.log(output);

        fetch("/api/navigationcms/store/",
            {
                method: "post",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: output,
            });
        // add logic here
    });

    btnAdd.on('click', () => {

        let newItem = {
            text: $("#txtText").val(),
            href: $("#txtHref").val(),
            icon: $("#txtIcon").val(),
            tooltip: $("#txtTooltip").val(),
        };

        //console.log(newItem);

        menuEditor.add(newItem);
        let output = menuEditor.getString();

        fetch("/api/navigationcms/store/",
            {
                method: "post",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: output,
            });
    });

    btnUpdate.on('click', () => {
        let data = {
            text: $("#txtText").val(),
            href: $("#txtHref").val(),
            icon: $("#txtIcon").val(),
            tooltip: $("#txtTooltip").val(),
        };
        menuEditor.update(data);
        let output = menuEditor.getString();
        fetch("/api/navigationcms/store/",
            {
                method: "post",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: output,
            });
    });

    fetch('/api/navigationcms/getjson/', {
        method: 'GET'
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            menuEditor.setArray(data);
            menuEditor.mount();
        });

    initializeIconPicker('#txtIcon');
}
