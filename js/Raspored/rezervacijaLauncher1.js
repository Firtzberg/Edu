var daySelector = 'div.raspored-blocks';
var pixelsPerHour = 48;
var firstHour = 8;

function launchRezervacija(datum, startHour, ucionicaId, instruktorId) {
    var newLocation = '/Rezervacija/create';
    if (window.location.toString().includes('/osoblje')) {
        newLocation = '/osoblje' + newLocation;
    }
    if (instruktorId) {
        newLocation += '/' + instruktorId;
    }
    newLocation += '?datum='+datum+'&startHour='+startHour;
    if (ucionicaId) {
        newLocation += '&ucionica_id=' + ucionicaId;
    }
    window.location = newLocation;
}

jQuery(document).ready(
    function () {
        jQuery(daySelector).click(function (event) {
            let element = jQuery(this);
            let pixelsFromTop = event.pageY - element.offset().top;
            launchRezervacija(
                element.attr('datum'),
                Math.floor(pixelsFromTop/pixelsPerHour) + firstHour,
                element.attr('ucionica-id'),
                element.attr('instruktor-id')
            );
        });
    }
);