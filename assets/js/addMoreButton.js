document.onreadystatechange = function () {
  if (document.readyState === 'complete') {

    +function ($) {
      "use strict";

      $(document).on('click', '.add-more-button', function () {

        var inputfield = $('.add-more-template-field').clone(true);
        inputfield.removeClass('display-none');
        $(this).closest('form').find('.add-more-fileinputs:first').append(inputfield.html());

        return false;
      });

    }(window.jQuery);

  };

};