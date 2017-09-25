/**
 * Created by naffiq on 6/11/2017.
 */
$(function() {

    function isMenuWide() {
        if (!localStorage) return false;
        return JSON.parse(localStorage.getItem('bridge/wide-menu'));
    }

    function toggleMenuWide() {
        if (localStorage) {
            localStorage.setItem('bridge/wide-menu', !isMenuWide())
        }
    }

    function hamburgerClick() {
        $('.nav-menu').toggleClass('wide');
        $('.bridge-wrap').toggleClass('nav-wide');
        toggleMenuWide();

        if ($('.nav-menu').hasClass('wide')) {
            $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip('destroy');
        } else {
            $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip();
        }
    }

    $('.nav-menu .hamburger').click(hamburgerClick);

    if (localStorage) {
        if (!isMenuWide()) {
            $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip();
        } else {
            $('.nav-menu').toggleClass('wide');
            $('.bridge-wrap').toggleClass('nav-wide');
        }
    }
});
