import {
    initTable,
    initDelete,
    initForm,
    showMessage,
    handleAjaxResponse,
    handleAjaxError
} from '../../../public/js/app.js';

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

    // Tabela główna
    const mainTableColumns = [
        { field: 'state', sortable: false, width: '40px', align: 'center', valign: 'middle' },
        { field: 'action', title: 'Akcja', sortable: false, class: 'action nowrap', width: '40px' },
        { field: 'slider_id', sortable: true, width: '40px' },
        { field: 'slider_title', sortable: true, class: 'nowrap' },
        { field: 'slider_description', sortable: true, class: 'nowrap' },
        { field: 'slider_identifier', sortable: true, class: 'nowrap' },
        { field: 'slider_display', sortable: true, class: 'nowrap' },
        { field: 'slider_lang', sortable: true, class: 'nowrap' }
    ];
    initTable($('#datatable'), '/api/slider/grid/', mainTableColumns);

    // Usuwanie
    initDelete($('#datatable'), $('#remove'), '/api/slider/remove/', 'slider_id', 'Czy na pewno chcesz usunąć Slider?');

    initForm($('#form_block'), '/api/slider/add', $('#datatable'), '/api/slider/grid/');
    initForm($('#configform'), '/api/slider/saveconfig', '', '', false);

    $(".backtolist").on('click', function () {
        window.location = "/slider/";
    });
}

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