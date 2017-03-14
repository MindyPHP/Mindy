import $ from 'jquery';

// Helper function to get parameters from the query string.
let getUrlParam = paramName => {
    let reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i'),
        match = window.location.search.match(reParam);

    return (match && match.length > 1) ? match[1] : null;
};  

let wysiwyg = {
    ckeditor: e => {
        e.preventDefault();
        let $link = $(e.target),
            url = $link.attr('data-url'),
            text = $link.attr('data-text');

        let funcNum = getUrlParam('CKEditorFuncNum');
        window.opener.CKEDITOR.tools.callFunction(funcNum, url);
        window.close();
    },

    tinymce: e => {
        e.preventDefault();

        let $link = $(e.target),
            url = $link.attr('data-url'),
            text = $link.attr('data-text'),
            parts = name.split('.'),
            ext = parts[parts.length - 1],
            content;

        if (["png", "jpg", "jpeg", "gif"].indexOf(ext.toLowerCase())) {
            content = `<img src='${url}' alt="${name}" />`;
        } else {
            content = `<a href='${url}'>${name}</a>`;
        }

        window.opener.tinyMCE.activeEditor.insertContent(content);
        window.close();
    }
};

$(() => {
    let $container = $('[data-filemanager]');

    switch ($container.attr('data-wysiwyg')) {
        case 'ckeditor':
            $(document).on('click', '.file-manager__table-paste', wysiwyg.ckeditor);
            break;
        case 'tinymce':
            $(document).on('click', '.file-manager__table-paste', wysiwyg.tinymce);
            break;
        default:
            break;
    }
});