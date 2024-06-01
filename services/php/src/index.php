<?php

// Set headers to allow CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Dummy data for the API response
$data = array(
    "message" => "Hello, this is a simple PHP API!"
);

// Output the JSON response
echo json_encode($data);
