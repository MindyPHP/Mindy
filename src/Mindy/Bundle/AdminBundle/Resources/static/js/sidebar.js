import { cookie } from 'easy-storage';

const cookieName = '__sidebar_pin';

const classes = {
    buttons_pinned: 'b-sidebar-buttons__button_pinned'
};

class Sidebar {
    pin() {
        cookie.set(cookieName, true);

        $(".b-header").addClass("b-header_padding");
        $(".b-page").addClass("b-page_padding");

        $(".b-sidebar").addClass("b-sidebar_pin");

        $('.b-sidebar-buttons__button_pin').addClass(classes.buttons_pinned)
    }

    unpin() {
        cookie.set(cookieName, false);

        $(".b-header").removeClass("b-header_padding");
        $(".b-page").removeClass("b-page_padding");

        $(".b-sidebar").removeClass("b-sidebar_pin");

        $('.b-sidebar-buttons__button_pin').removeClass(classes.buttons_pinned)
    }

    togglePin() {
        if ($('.b-sidebar-buttons__button_pin').hasClass(classes.buttons_pinned)) {
            sidebar.unpin();
        } else {
            sidebar.pin();
        }
    }

    show() {
        $(".b-sidebar").addClass("b-sidebar_open");
        $(".b-sidebar-buttons").addClass("b-sidebar-buttons_open");
    }

    hide() {
        $(".b-sidebar").removeClass("b-sidebar_open");
        $(".b-sidebar-buttons").removeClass("b-sidebar-buttons_open");
    }

    toggle() {
        if ($(".b-sidebar").hasClass("b-sidebar_pin")) {
            return;
        }

        if ($(".b-sidebar").hasClass("b-sidebar_open")) {
            this.hide();
        } else {
            this.show();
        }
    }
}

const sidebar = new Sidebar;

$(document)
    .on('click', e => {
        let $target = $(e.target),
            $sidebar = $('.b-sidebar');

        if ($sidebar.hasClass('b-sidebar_pin')) {
            return
        }

        if ($sidebar.hasClass('b-sidebar_open')) {
            if (
                $target.closest('.b-sidebar').length > 0 ||
                $target.closest('.b-sidebar-buttons').length > 0
            ) {

            } else {
                sidebar.hide();
            }
        }
    })
    .on('click', ".b-sidebar-buttons__button_toggle", function (e) {
        e.preventDefault();
        sidebar.toggle();
    })
    .on("click", ".b-sidebar-buttons__button_pin", function (e) {
        e.preventDefault();
        sidebar.togglePin();
    });
