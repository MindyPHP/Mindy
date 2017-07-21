import $ from "jquery";
import "jquery-ui/ui/widget.js";
import "jquery-ui/ui/widgets/sortable.js";
import 'checkboxes.js/src/jquery.checkboxes.js';
import './sidebar';
import './file/index';

$(document)
    .on('click', '.b-filter-button', e => {
        e.preventDefault();
        $(e.target).toggleClass('b-filter-button_active');
        $('.b-filter-form').toggleClass('b-filter-form_active');
    })
    .on('click', '.b-flash', e => {
        e.preventDefault();
        $(e.target).closest('.b-flash').remove();
    });
