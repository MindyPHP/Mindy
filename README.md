# CKEditor Bundle

[![Build Status](https://travis-ci.org/MindyPHP/CKEditorBundle.svg?branch=master)](https://travis-ci.org/MindyPHP/CKEditorBundle)

```yaml
ivory_ck_editor:
    configs:
        default:
            filebrowserBrowseUrl: "/core/file/list?wysiwyg=ckeditor"
            filebrowserWindowWidth: 800
            filebrowserWindowHeight: 500
            toolbar: 'default_toolbar'
            language: ru

    toolbars:
        configs:
            default_toolbar: [ '@basic', '@paste', '-', '@media', '-', "@link", "-", "@extra" ]
        items:
            basic: ['Bold', 'Italic', 'Strike', 'RemoveFormat', 'Blockquote', 'Styles', 'Format', 'Table', 'HorizontalRule']
            list: ['NumberedList', 'BulletedList']
            media: ['Image']
            paste: ['PasteText', 'PasteFromWord']
            link: ['Link', 'Unlink', 'Anchor']
            extra: ['Templates', 'Source', '-', 'Maximize']
```
