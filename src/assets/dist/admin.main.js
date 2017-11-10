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
            $('.side-menu--collapsable').attr('data-toggle', 'collapse');
            $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip('destroy');
        } else {
            $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip();
            $('.side-menu--collapsable').removeAttr('data-toggle');
            $('.side-menu--item .sub-menu').removeClass('in');
        }
    }

    $('.nav-menu .hamburger').click(hamburgerClick);

    if (!isMenuWide()) {
        $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip();
        $('.side-menu--collapsable').removeAttr('data-toggle');
    } else {
        $('.nav-menu').toggleClass('wide');
        $('.bridge-wrap').toggleClass('nav-wide');
    }

    $('.side-menu').perfectScrollbar();
    $('.side-menu').css({height: window.innerHeight - 100 + 'px'});
});
