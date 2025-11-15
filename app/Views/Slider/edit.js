
import {
    initTable,
    initDelete,
    initForm,
    showMessage,
    handleAjaxResponse,
    handleAjaxError
} from '../../../public/js/app.js';

import Dropzone from "dropzone";

import {
    ClassicEditor,
    AccessibilityHelp,
    Alignment,
    Autoformat,
    AutoImage,
    Autosave,
    BlockQuote,
    Bold,
    CloudServices,
    Essentials,
    GeneralHtmlSupport,
    Heading,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    PictureEditing,
    RemoveFormat,
    SimpleUploadAdapter,
    SelectAll,
    SourceEditing,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Subscript,
    Superscript,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
} from 'ckeditor5';

export function initModule() {

    var main_id = $('#datatable_files').attr('data-slider-id');

    // Tabela główna
    const mainTableColumns = [
        { field: 'state', sortable: false, width: '40px', align: 'center', valign: 'middle' },
        { field: 'file_move', sortable: true, width: '40px' },
        { field: 'file_id', sortable: true, width: '40px' },
        { field: 'file_src', sortable: true, class: 'nowrap', width: '80px' },
        { field: 'file_name', sortable: true, class: 'nowrap' },
        { field: 'file_type', sortable: true, class: 'nowrap' },
        { field: 'file_created_at', sortable: true, class: 'nowrap' }
    ];
    initTable($('#datatable_files'), '/api/slider/grid_files/' + main_id + '/', mainTableColumns);

    $('#datatable_files').on('reorder-row.bs.table', function (e, data) {
        JSON.stringify($(this).bootstrapTable('getData').map(function (row, i) {
            var newposition
            newposition = i;

            $.post("/api/slider/order_files/", { id: row.file_id, position: newposition });
        }))
    });

    // Usuwanie
    initDelete($('#datatable_files'), $('#remove_files'), '/api/slider/remove_files', 'file_id', 'Czy na pewno chcesz usunąć Pliki?');

    initForm($('#form_block_edit'), '/api/slider/update', '', '', false);

    $(".backtolist").on('click', function () {
        window.location = "/slider/";
    });

}

var dropzoneConfig = {
    paramName: "file",
    maxFilesize: 20, // MB
};

// Ręczna inicjalizacja Dropzone dla elementu o ID "upload-form"
var myDropzone = new Dropzone("#upload-form", dropzoneConfig);

// Przenieś całą logikę z metody init do event listenerów
myDropzone.on("uploadprogress", function (file, progress) {
    var progressBar = file.previewElement.querySelector(".dz-progress");
    // Upewnij się, że element progress istnieje w template
    if (progressBar) {
        progressBar.style.width = progress + "%";
        progressBar.innerHTML = Math.round(progress) + "%"; // Zaokrąglamy
    }
});

myDropzone.on("success", function (file, response) {
    var progressBar = file.previewElement.querySelector(".dz-progress");
    if (progressBar) {
        // Dropzone.js automatycznie dodaje .dz-success, ale to jest do paska
        progressBar.classList.remove("bg-danger"); // Na wypadek wcześniejszego błędu
        progressBar.classList.add("bg-success");
        progressBar.innerHTML = "&nbsp;Załadowane...";
    }

    // Reszta Twojej logiki on success
    $('#datatable_files').bootstrapTable('refresh');

    var msg = response;
    $(".message").find('.alert').html(msg).show(500);
    setTimeout(function () {
        $(".message").find('.alert').fadeOut("slow");
    }, 5000);
});

myDropzone.on("error", function (file, errorMessage) {
    var progressBar = file.previewElement.querySelector(".dz-progress");
    if (progressBar) {
        progressBar.classList.remove("bg-success"); // Na wypadek wcześniejszego sukcesu
        progressBar.classList.add("bg-danger");
        // Dropzone usuwa plik z podglądu, jeśli jest błąd, chyba że ustawisz `autoProcessQueue: false`
        progressBar.innerHTML = errorMessage.message || errorMessage;
    }

    // Reszta Twojej logiki on error
    $('#datatable_files').bootstrapTable('refresh');
});

const editorConfig = {
    toolbar: {
        items: [
            'undo',
            'redo',
            '|',
            'sourceEditing',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'subscript',
            'superscript',
            'removeFormat',
            '|',
            'specialCharacters',
            'link',
            'insertImage',
            'insertImageViaUrl',
            'mediaEmbed',
            'insertTable',
            'blockQuote',
            'htmlEmbed',
            '|',
            'alignment',
            '|',
            'bulletedList',
            'numberedList',
            'todoList',
            'outdent',
            'indent'
        ],
        shouldNotGroupWhenFull: false
    },
    plugins: [
        AccessibilityHelp,
        Alignment,
        Autoformat,
        AutoImage,
        Autosave,
        BlockQuote,
        Bold,
        CloudServices,
        Essentials,
        GeneralHtmlSupport,
        Heading,
        HtmlComment,
        HtmlEmbed,
        ImageBlock,
        ImageCaption,
        ImageInline,
        ImageInsert,
        ImageInsertViaUrl,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        Indent,
        IndentBlock,
        Italic,
        Link,
        LinkImage,
        List,
        ListProperties,
        MediaEmbed,
        Paragraph,
        PasteFromOffice,
        PictureEditing,
        RemoveFormat,
        SimpleUploadAdapter,
        SelectAll,
        SourceEditing,
        SpecialCharacters,
        SpecialCharactersArrows,
        SpecialCharactersCurrency,
        SpecialCharactersEssentials,
        SpecialCharactersLatin,
        SpecialCharactersMathematical,
        SpecialCharactersText,
        Strikethrough,
        Subscript,
        Superscript,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TableToolbar,
        TextTransformation,
        TodoList,
        Underline
    ],
    heading: {
        options: [{
            model: 'paragraph',
            title: 'Paragraph',
            class: 'ck-heading_paragraph'
        },
        {
            model: 'heading1',
            view: 'h1',
            title: 'Heading 1',
            class: 'ck-heading_heading1'
        },
        {
            model: 'heading2',
            view: 'h2',
            title: 'Heading 2',
            class: 'ck-heading_heading2'
        },
        {
            model: 'heading3',
            view: 'h3',
            title: 'Heading 3',
            class: 'ck-heading_heading3'
        },
        {
            model: 'heading4',
            view: 'h4',
            title: 'Heading 4',
            class: 'ck-heading_heading4'
        },
        {
            model: 'heading5',
            view: 'h5',
            title: 'Heading 5',
            class: 'ck-heading_heading5'
        },
        {
            model: 'heading6',
            view: 'h6',
            title: 'Heading 6',
            class: 'ck-heading_heading6'
        }
        ]
    },
    htmlSupport: {
        allow: [{
            name: /^.*$/,
            styles: true,
            attributes: true,
            classes: true
        }]
    },
    image: {
        toolbar: [
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            '|',
            'resizeImage'
        ]
    },
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: 'https://',
        decorators: {
            toggleDownloadable: {
                mode: 'manual',
                label: 'Downloadable',
                attributes: {
                    download: 'file'
                }
            }
        }
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },
    menuBar: {
        isVisible: true
    },
    placeholder: 'Type or paste your content here!',
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
    },
    simpleUpload: {
        uploadUrl: '/api/filesupload/'
    },
    licenseKey: 'GPL',
    height: '400px'
};

let editor;

function createEditor(elementId) {
    return ClassicEditor
        .create(document.querySelector(elementId), editorConfig)
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(err => console.error(err.stack));
}
createEditor('#slider_description');


const el = document.getElementById('form_block');
if (el) {
    document.querySelector("#form_block").addEventListener("submit", function (e) {
        editor.setData('');
    });
}