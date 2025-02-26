$(function() {
    currentUrl = $('#currentUrl').val();
    locations = [];
    services = [];
    facilities = [];
    getAllOffers();
    initialize();

    $('.services:checked').each(function (i) {
        services[i] = $(this).val();
    });

    $('.facilities:checked').each(function (i) {
        facilities[i] = $(this).val();
    });
})

$(document).on('click', '.pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    getAllOffers(page);
});

$(document).on('click','#keyword-search,#apply_service,#apply_facility',function(){

    $('.collapse-custom').removeClass('open-box');

    services = [];
    $('.services:checked').each(function (i) {
        services[i] = $(this).val();
    });

    facilities = [];
    $('.facilities:checked').each(function (i) {
        facilities[i] = $(this).val();
    });

    getAllOffers();
})

$(document).on('click','#clear_service',function(){
    $('.collapse-custom').removeClass('open-box');
    services = [];
    $('.services:checked').each(function (i) {
        $(this).prop('checked', false);
    });
    getAllOffers();
})

$(document).on('click','#clear_facility',function(){
    $('.collapse-custom').removeClass('open-box');
    facilities = [];
    $('.facilities:checked').each(function (i) {
        $(this).prop('checked', false);
    });
    getAllOffers();
})

$('#location').on('keyup',function(){
    if($(this).val() == '' || $(this).val() == null){
        $('#latitude').val('');
        $('#longitude').val('');
        $('#city').val('');
        $('#state').val('');
        $('#country').val('');
        $('#postal_code').val('');
    }
})

// $(".collapse-custom").on('click',function(e){
//     e.stopPropagation();
// });

// $(document).on('click',function(){
//     $('.collapse-custom').removeClass('open-box');
// });

function getAllOffers(page) {

    if(page == undefined){
        var fetchUrl = currentUrl;
    }else{
        var fetchUrl = currentUrl+"?page="+page;
    }

    $.ajax({
        type: 'GET',
        url: fetchUrl,
        data: {
            'keyword': $('#keyword').val(),
            'city': $('#city').val(),
            'state': $('#state').val(),
            'country': $('#country').val(),
            'location': $('#location').val(),
            'services': services,
            'facilities': facilities
        },
        dataType: 'json',
        beforeSend: function () {
            $('.main_ajax_loader').show();
        },
        success: function (response) {

            $('.main_ajax_loader').hide();
            $('.offer_listing').html(response.html);
            $('#total_offers').html(response.total);
            locations = response.locations;
            // updateUrl(page)
            renderMap();

            // $('html, body').animate({
            //     scrollTop: $('header').offset().top
            // }, 1000);
            $('html, body').animate({
                scrollTop: $('body').offset().top
            }, 1000);

        },
        error: function (response) {
            console.log(response);
        }

    });
}

function renderMap() {
    // Map options
    var options = {
        center: { lat: 51.1657, lng: 10.4515 },
        zoom: 6,
    }

    // New map
    var map = new google.maps.Map(document.getElementById('listing_map'), options);

    const bounds = new google.maps.LatLngBounds();

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(parseFloat(locations[i][1]), parseFloat(locations[i][2])),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
            infowindow.setContent(locations[i][0]);
            infowindow.open(map, marker);
            }
        })(marker, i));

        bounds.extend({ lat: parseFloat(locations[i][1]), lng: parseFloat(locations[i][2])});
    }

    if(locations.length > 0){
        // This is needed to set the zoom after fitbounds,
        google.maps.event.addListener(map, 'zoom_changed', function() {
            zoomChangeBoundsListener =
                google.maps.event.addListener(map, 'bounds_changed', function(event) {
                    if (this.getZoom() > 15 && this.initialZoom == true) {
                        // Change max/min zoom here
                        this.setZoom(15);
                        this.initialZoom = false;
                    }
                google.maps.event.removeListener(zoomChangeBoundsListener);
            });
        });
        map.initialZoom = true;

        map.fitBounds(bounds);
    }

}

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
        types: ['(cities)'],
        componentRestrictions: { country: "de" },
    };

    var autocomplete = new google.maps.places.Autocomplete(input, options);
    // var autocomplete = new google.maps.places.Autocomplete(input);

    google.maps.event.addListener(autocomplete, 'place_changed', function () {

        var place = autocomplete.getPlace();
        // console.log(place);
        var resultArray = place.address_components;

        document.getElementById('city').value = '';
        document.getElementById('state').value = '';
        document.getElementById('country').value = '';
        document.getElementById('postal_code').value = '';
        resultArray.forEach(setValue);

        $('#latitude').val(place.geometry.location.lat());
        $('#longitude').val(place.geometry.location.lng());

    });
}

// function updateUrl(page){
//     if(page != undefined){
//         var url = currentUrl + "?page="+page;
//     }else{
//         var url = currentUrl;
//     }
//     window.history.pushState({"html":"","pageTitle":""},"", url);
// }
