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

$(() => {
    $('.fileupload').fileupload({
        dataType: 'json',
        limitConcurrentUploads: 1,
        sequentialUploads: true,
        dropZone: $('#dropzone'),
        add: function (e, data) {
            $('#progress').removeClass('hide').addClass('visible');
            data.submit();
        },
        progress: function (e, data) {
            let progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress').find('.progress-state').width(progress + '%').text(progress + '%');
        },
        done: function (data) {
            api.get('').then(data => {
                $('.files-container').replaceWith($(data).find('.files-container'));
            });

            let $progress = $('#progress');
            $progress.removeClass('visible').addClass('hide');

            notify({ title: 'Файлы загружены' });
        }
    });

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
        .on('click', '.files-create-directory', e => {
            e.preventDefault();

            let $this = $(e.target).closest('a');
            let value = prompt('Введите имя директории:');
            if (value) {
                api.post($this.attr('href'), {}, { directory: value }).then(data => {
                    if (data.status) {
                        notify({ title: 'Директория создана' });

                        api.get('').then(data => {
                            $('.files-container').replaceWith($(data).find('.files-container'));
                        });
                    } else {
                        notify({ title: 'Ошибка', message: 'При создании директории возникла ошибка' });
                    }
                });
            }
        })
        .on('click', '.b-filemanager__table-remove', e => {
            e.preventDefault();

            let $this = $(e.target);
            if (confirm($this.attr('data-confirm-message'))) {
                api.post($this.attr('href')).then(data => {
                    $this.closest('tr').remove();

                    notify({ title: 'Файл удален' });
                });
            }
        })
        .on('click', '.b-filemanager__table-copy', e => {
            e.preventDefault();

            let $target = $(e.target),
                input = $target.closest('.b-table__td').find('.b-filemanager__table-input').get(0);

            input.select();
            document.execCommand('copy');

            notify({ title: 'Ссылка скопирована' });
        })
        .on('click', '.b-filemanager__table-input', e => {
            document.querySelector('.b-filemanager__table-input').select();
        });
});