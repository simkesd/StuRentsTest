$( document ).ready(function() {

    var mapProp = {
        zoom:8,
        center:new google.maps.LatLng(parseFloat(51.5072), parseFloat(0.1275))
    };

    var map=new google.maps.Map(document.getElementById("map-canvas"),mapProp);

    /*
     * Define object for working with google maps.
     * To start drawing regions just call draw(city) function.
     */
    var stuRents = {
        coordinates : {},
        regions : {},
        mapRegion : [],
        regionPaths: [],

        /*
         * Accepts param that defaults to the city of london.
         * Ajax request is calling php script that gets specific city kml data that is parsed for using in google maps api.
         */
        draw : function(city) {
            this._getAllRegions(); // get list of all regions

            // Ajax to get specific city kml data
            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: { city: city },
                success: function(result, tempObj) {
                    stuRents.coordinates = JSON.parse(result); // array of JSON coordinates for polygon
                    var mapRegion = [];
                    for(var i = 0; i < stuRents.coordinates.length; i++) {
                        mapRegion.push(new google.maps.LatLng(parseFloat(stuRents.coordinates[i][1]), parseFloat(stuRents.coordinates[i][0])));
                    }
                    stuRents.mapRegion = mapRegion; // Prepared array of google maps api coordinates for drawing polygon
                    var regionPath = new google.maps.Polygon({
                        path:stuRents.mapRegion,
                        strokeColor: "#"+$('#stroke-color').val(),
                        strokeOpacity:0.8,
                        strokeWeight:2,
                        fillColor:"#"+$('#fill-color').val(),
                        fillOpacity:0.4
                    });
                    console.log($('#stroke-color').val());
                    regionPath.setMap(map); //draw polygon on the map
                    map.setCenter(new google.maps.LatLng(parseFloat(stuRents.coordinates[0][1]), parseFloat(stuRents.coordinates[0][0]))); // Set center of the map to the first coordinate of polygon
                    stuRents.regionPaths[city] = regionPath; // Add regionPath to the array so it can be later accessed and deleted.
                }
            });
        },

        /*
         * Use ajax request to get list of all regions
         */
        _getAllRegions : function() {
            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: { cities: true },
                success: function(result) {
                    this.regions = JSON.parse(result);
                }
            });
        }
    };

    /*
     * Click listener for cities
     * Get the name of the city from clicked element and if it is active remove its polygon
     * if it is not active draw city polygon
     */
    $('.city').on('click', function() {
        var city = $(this).data('city');
        if ($(this).hasClass('active')) {
            stuRents.regionPaths[city].setMap(null);
            $(this).removeClass('active');
        } else {
            stuRents.draw(city);
            $(this).addClass('active');
        }
    });
});