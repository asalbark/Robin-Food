<?php

function getAddressFromCoordinates($latitude, $longitude) {
    $api_url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&addressdetails=1";

    // Fetch the JSON response
    $json_response = file_get_contents($api_url);

    // Decode the JSON response
    $response = json_decode($json_response, true);

    // Check if the response contains address information
    if (isset($response['display_name'])) {
        return $response['display_name'];
    } else {
        return "Address not found";
    }
}

?>
