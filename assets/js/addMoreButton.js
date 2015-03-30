+function ($) { "use strict";

    $('.add-more-button').click(function() {

        var inputfield = $('.add-more-template-field').clone(true);
        inputfield.removeClass('display-none');
        $('#' + $(this).attr('data-target')).append(inputfield.html());

        return false;
    });

}(window.jQuery);