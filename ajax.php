<?php

$city = $_GET['city'];

$showAll = file_get_contents("https://sturents.com/geo/show-all");
$regions = json_decode($showAll, true);

if(isset($_GET['cities']) && $_GET['cities']) {
    echo $showAll;
    exit;
}

// Check if the choosen city exist in city list
if(in_array($city, $regions)) {
    $kmlString = file_get_contents("https://sturents.com/geo/".$city.".kml?hash=383608f5a3");
    $kml = simplexml_load_string($kmlString);
    $coordinatesString = (string)$kml->Placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;
    $patterns = array("/\s+/", "/\s([?.!])/");
    $replacer = array(" ","$1");

    $cleaned = trim(preg_replace( $patterns, $replacer, $coordinatesString ));
    $coordinates = explode(' ', $cleaned);
    $longLatArray = array();
    foreach($coordinates as $coordinate) {
        $longLatArray[] = explode(',', $coordinate);
    }
    $coordinatesJSON = json_encode($longLatArray);

    echo $coordinatesJSON;
}
else {
    echo "Invalid city name";
}

exit;
