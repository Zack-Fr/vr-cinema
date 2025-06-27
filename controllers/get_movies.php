<?php
require("../models/Movies.php");
require("../config/connection.php");

$response = [];
$response["status"] = 200;

if(!isset($_GET["id"])){
    $movies = Movies::all($mysqli);

    $response["movies"] = [];

    foreach($movies as $m){
        $response["articles"][] =$m->toArray();
    }
    echo json_encode($response);
    return;
}
$id = $_GET["id"];
$article = Movies::find($mysqli, $id);
$response["movies"] = $article->toArray();

echo json_encode($response);
return;