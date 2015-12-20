var formToken = jQuery('input[name=_token]').val();
var cjenovnikDropdownSelector = 'select';

function updateCjenovnik(element) {
    jQuery.ajax({
        url: window.location.href + '/../../../Cjenovnik/table/' + element.val(),
        dataType: 'html',
        type: 'post',
        data: {
            _token: formToken
        },
        success: function (data) {
            element.parent().find('.cjenovnik_table').hide().html(data).fadeIn('fast');
        },
        error: function () {
            element.parent().find('.cjenovnik_table').hide().html('<h3>Došlo je do greške. Provjerite vezu.</h3>').fadeIn('fast');
        }
    });
}

jQuery(document).ready(
        function () {
            jQuery(cjenovnikDropdownSelector).change(function () {
                updateCjenovnik(jQuery(this));
            });
        }
);