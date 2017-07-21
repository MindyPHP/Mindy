import api from '../api';
import notify from '../notify';

/* The jQuery UI widget factory, can be omitted if jQuery UI is already included */
require('imports-loader?define=>false&exports=>false!blueimp-file-upload/js/vendor/jquery.ui.widget.js');
/* The Iframe Transport is required for browsers without support for XHR file uploads */
require('imports-loader?define=>false&exports=>false!blueimp-file-upload/js/jquery.iframe-transport.js');
/* The basic File Upload plugin */
require('imports-loader?define=>false&exports=>false!blueimp-file-upload/js/jquery.fileupload.js');
/* The File Upload processing plugin */
require('imports-loader?define=>false&exports=>false!blueimp-file-upload/js/jquery.fileupload-process.js');
/* The File Upload validation plugin */
require('imports-loader?define=>false&exports=>false!blueimp-file-upload/js/jquery.fileupload-validate.js');

const attachFileManager = () => {
    $('.b-filemanager__fileinput').fileupload({
        limitConcurrentUploads: 1,
        sequentialUploads: true,
        dropZone: $('.b-filemanager__dropzone'),
        add: (e, data) => {
            $('.b-filemanager__progress').addClass('b-filemanager__progress_visible');
            data.submit();
        },
        progressall: (e, data) => {
            let progress = parseInt(data.loaded / data.total * 100, 10);
            $('.b-filemanager__progress').find('.b-filemanager__state').width(progress + '%').text(progress + '%');
        },
        done: (data) => {
            $('.b-filemanager__progress').removeClass('b-filemanager__progress_visible');
            notify({ title: 'Файлы загружены' });

            api.get('').then(data => {
                $('.b-filemanager__target').replaceWith(data);
            });
        }
    });
};

$(document)
    .on('dragover dragenter', e => {
        let $dropZone = $('.b-filemanager__dropzone'),
            timeout = window.dropZoneTimeout;
        if (!timeout) {
            $dropZone.addClass('b-filemanager__dropzone_in');
        } else {
            clearTimeout(timeout);
        }

        let found = false,
            node = e.target;

        do {
            if (node === $dropZone[0]) {
                found = true;
                break;
            }
            node = node.parentNode;
        } while (node != null);

        if (found) {
            $dropZone.addClass('b-filemanager__dropzone_hover');
        } else {
            $dropZone.removeClass('b-filemanager__dropzone_hover');
        }

        window.dropZoneTimeout = setTimeout(() => {
            window.dropZoneTimeout = null;
            $dropZone.removeClass('b-filemanager__dropzone_in b-filemanager__dropzone_hover');
        }, 100);
    })
    .on('click', '.b-filemanager__mkdir', e => {
        e.preventDefault();

        let $this = $(e.target).closest('a');
        let value = prompt('Введите имя директории:');
        if (value) {
            api.post($this.attr('href'), {}, { directory: value }).then(data => {
                if (data.status) {
                    notify({ title: 'Директория создана' });

                    api.get('').then(data => {
                        $('.b-filemanager__target').replaceWith(data);
                    });
                } else {
                    notify({ title: 'Ошибка', message: 'При создании директории возникла ошибка' });
                }
            });
        }
    })
    .on('click', '.b-filemanager__remove', e => {
        e.preventDefault();

        let $this = $(e.target);
        if (confirm($this.attr('data-confirm-message'))) {
            api.post($this.attr('href')).then(data => {
                $this.closest('tr').remove();

                notify({ title: 'Файл удален' });
            });
        }
    })
    .on('click', '.b-filemanager__copy', e => {
        e.preventDefault();

        $(e.target)
            .closest('.b-filemanager__container')
            .find('.b-filemanager__input')
            .select();
        document.execCommand('copy');

        notify({ title: 'Ссылка скопирована' });
    })
    .on('click', '.b-filemanager__input', e => {
        $(e.target).select();
    });

$(() => {
    attachFileManager();
});