var cijenaIdPrefix = 'cijena_';
var cijenaCopyClassPrefix = 'cijena_copy_';
var totalClassPrefiy = 'total_';
var instructorIdPrefix = 'instruktor_';
var tvrtkaClassPrefix = 'tvrtka_';
var instructorPcntClassPrefix = 'instruktor_pcnt_';
var tvtkaPcntClassPrefix = 'tvrtka_pcnt_';
var instruktorMoreSelector = '#ins_more_pcnt';
var tvrtkaMoreSelector = '#tvtka_more_pcnt';

function cijenaChanged(value, i) {
    jQuery('.' + cijenaCopyClassPrefix + i).html(value);
    jQuery('.' + totalClassPrefiy + i).html(value * i);
    var ins = jQuery('#' + instructorIdPrefix + i).val();
    jQuery('.' + tvrtkaClassPrefix + i).html(value * i - ins);
    if (value == 0) {
        jQuery('.' + instructorPcntClassPrefix + i).html(0);
        jQuery('.' + tvtkaPcntClassPrefix + i).html(0);
    }
    else {
        jQuery('.' + instructorPcntClassPrefix + i).html(((100 * ins) / (value * i)).toFixed(2));
        jQuery('.' + tvtkaPcntClassPrefix + i).html(((100 * (value * i - ins)) / (value * i)).toFixed(2));
    }
}

function instruktorChanged(value, i) {
    var val = jQuery('#' + cijenaIdPrefix + i).val();
    jQuery('.' + tvrtkaClassPrefix + i).html(val * i - value);
    if (val == 0) {
        jQuery('.' + instructorPcntClassPrefix + i).html(0);
        jQuery('.' + tvtkaPcntClassPrefix + i).html(0);
    }
    else {
        jQuery('.' + instructorPcntClassPrefix + i).html(((100 * value) / (val * i)).toFixed(2));
        jQuery('.' + tvtkaPcntClassPrefix + i).html(((100 * (val * i - value)) / (val * i)).toFixed(2));
    }
}

jQuery(document).ready(
        function () {
            for (var i = 1; i < 5; i++)
            {
                jQuery('#' + cijenaIdPrefix + i).change(function () {
                    var _this = jQuery(this);
                    var id = _this.attr('id');
                    cijenaChanged(_this.val(), parseInt(id.substr(cijenaIdPrefix.length, id.lenght)));
                });
                jQuery('#' + instructorIdPrefix + i).change(function () {
                    var _this = jQuery(this);
                    var id = _this.attr('id');
                    instruktorChanged(_this.val(), parseInt(id.substr(instructorIdPrefix.length, id.lenght)));
                });
                jQuery(instruktorMoreSelector).change(function() {
                    jQuery(tvrtkaMoreSelector).html(100 - jQuery(this).val());
                });
            }
        }
);