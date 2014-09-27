<!DOCTYPE html>
<html>
<head>
    <?php
    $showAll = file_get_contents("https://sturents.com/geo/show-all");
    $regions = json_decode($showAll);

    ?>
    <title></title>
    <style type="text/css">
        #map-canvas { width: 500px; height: 500px; margin: 0; padding: 0;}
        #list {
            -webkit-column-count: 5; /* Chrome, Safari, Opera */
            -moz-column-count: 5; /* Firefox */
            column-count: 5;
        }
        .active {
            background: darkgrey;
        }
    </style>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzQoJe-SIeIEK5FqpyNTJiFR4BCI9Isq0"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript">

        $( document ).ready(function() {
            var mapProp = {
                zoom:8
            };

            var map=new google.maps.Map(document.getElementById("map-canvas"),mapProp);

            /*
             * Define object for working with google maps.
             * To start drawing regions just call draw(city) function. Its param defaults to the city of london.
             * Ajax request is calling php script that gets specific city kml data that is parsed for using in google maps api
             *
             */
            var stuRents = {
                coordinates : {},
                regions : {},
                mapRegion : [],
                regionPaths: [],

                draw : function(city) {
                    if(typeof(city)==='undefined') city = 'london'; // set default value for city
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
                                strokeColor:"#0000FF",
                                strokeOpacity:0.8,
                                strokeWeight:2,
                                fillColor:"#0000FF",
                                fillOpacity:0.4
                            });
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

            stuRents.draw();

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
    </script>
</head>
<body>
<div id="map-canvas"></div>
<div id="list">
    <?php
    foreach($regions as $region) {
        echo '<div class="city" data-city="'.$region.'">';
        echo $region;
        echo '</div>';
    }
    ?>
</div>
</body>
</html>