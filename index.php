<!DOCTYPE html>
<html>
<head>
    <?php
    $showAll = file_get_contents("https://sturents.com/geo/show-all");
    $regions = json_decode($showAll);

    ?>
    <title></title>
    <link rel="stylesheet" href="styles.css"/>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzQoJe-SIeIEK5FqpyNTJiFR4BCI9Isq0"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts.js"></script>
    <script type="text/javascript" src="jscolor/jscolor.js"></script>
</head>
<body>
<div id="map-canvas"></div>

<h4>Select the colors for your polygon and than click on the region you wish to show</h4>
<p>To delete region, simply click it again</p>
<input class="color" id="stroke-color" value="0000FF">
<input class="color" id="fill-color" value="0000FF">

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