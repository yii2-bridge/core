/**
 * Created by naffiq on 6/11/2017.
 */
$(function() {
    var menuToggleUrl = $('body').data('menu-toggle-url');

    function isMenuWide() {
        if (!localStorage) return false;
        return JSON.parse(localStorage.getItem('bridge/wide-menu'));
    }

    function toggleMenuWide() {
        var shouldBeWide = !isMenuWide();

        if (localStorage) {
            localStorage.setItem('bridge/wide-menu', shouldBeWide)
        }

        $.get(menuToggleUrl + '&state=' + (shouldBeWide ? 'true' : 'false'));
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

    $('.nav-menu--header-hamburger').click(hamburgerClick);

    if (!isMenuWide()) {
        $('ul.side-menu li.side-menu--item, .form--sign-out button').tooltip();
        $('.side-menu--collapsable').removeAttr('data-toggle');

        $('.nav-menu').removeClass('wide');
        $('.bridge-wrap').removeClass('nav-wide');
    } else {
        $.get(menuToggleUrl + '&state=true');

        $('.nav-menu').addClass('wide');
        $('.bridge-wrap').addClass('nav-wide');
    }

    $('.side-menu').perfectScrollbar();
    $('.side-menu').css({height: window.innerHeight - 100 + 'px'});
});
