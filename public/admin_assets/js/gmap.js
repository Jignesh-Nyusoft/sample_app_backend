// "use strict";

function setValue(item){
    if(item.types.includes("locality")){
        document.getElementById('city').value = item.long_name;
    }
    // if(item.types[0] == 'administrative_area_level_2'){
    //     document.getElementById('city').value = item.long_name;
    // }
    if(item.types[0] == 'administrative_area_level_1'){
        document.getElementById('state').value = item.long_name;
    }
    if(item.types[0] == 'country'){
        document.getElementById('country').value = item.long_name;
    }
    if(item.types[0] == 'postal_code'){
        document.getElementById('postal_code').value = item.long_name;
    }
}

function initialize() {
    var input = document.getElementById('location');

    var options = {
        // types: ['(cities)'],
        types: ["establishment"],
        componentRestrictions: { country: "de" },
    };

    var autocomplete = new google.maps.places.Autocomplete(input, options);
    // var autocomplete = new google.maps.places.Autocomplete(input);

    google.maps.event.addListener(autocomplete, 'place_changed', function () {

        var place = autocomplete.getPlace();
        // console.log(place);
        var resultArray = place.address_components;

        $('#location_input').val($('#location').val());

        document.getElementById('city').value = '';
        document.getElementById('state').value = '';
        document.getElementById('country').value = '';
        document.getElementById('postal_code').value = '';
        resultArray.forEach(setValue);

        $('#latitude').val(place.geometry.location.lat());
        $('#longitude').val(place.geometry.location.lng());

    });
}
