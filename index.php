<?php
ini_set("error_reporting", 1);
error_reporting(E_ALL);
/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you aaneed to load Slim Framework with your own
 * PSR-4 autoloader.
 */
require 'vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


function getConnection() {
    $dbhost = "moddather.net";
    $dbuser = "moddatherTask";
    $dbpass = "moddatherTask";
    $dbname = "insurances";

    try {
        $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    return $dbh;
}

$db = getConnection();


/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new Slim\App();


/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */
$app->get('/', function ($request, $response, $args) {
    $response->write("welcome to insurance api!");
    return $response;
});


 $app->post("/insert_insurance", function(Request $request,  Response $response ,$args){
    global $db;
    $title = $request->getParam("title");
    $price = $request->getParam("price");
    $cat = $request->getParam("cat");

    
    
       $query = sprintf("INSERT INTO `insurance_table` (`title`,`price`,`cat`) VALUES ('%s','%f','%s')", 
           $title,$price,$cat);
       
       
    $Res = $db->query($query);
    if ($Res) {
        $result = array(
            "status" => "OK",
            "message" => "inserted successfuly"
            );
        PrintOutput($result);
    } else {
        $result = array(
            "status" => "NOK",
            "message" => "Can't can't insert the names"
        );
        PrintOutput($result);
    }
    
    
});


 $app->get("/delete_insurance/{id}", function(Request $request,  Response $response ,$args){
    global $db;
    
    $id = $args["id"];
;
    
       $query = sprintf("DELETE FROM `insurance_table` WHERE `id`='%d'", $id);
       
       
    $Res = $db->query($query);
    if ($Res) {
        $result = array(
            "status" => "OK",
            "message" => "deleted"
            );
        PrintOutput($result);
    } else {
        $result = array(
            "status" => "NOK",
            "message" => "Can't delete the insurance"
        );
        PrintOutput($result);
    }
    
    
});



$app->get("/get_insurances", function(Request $request,  Response $response,$args){
    global $db;
    $query = sprintf("SELECT * FROM `insurance_table`");
    $Res = $db->query($query)->fetchAll();
    $Values = array();
   
    
    if(count($Res)>=1){
       
       foreach ($Res as $value){
           $Values [] = array(
               "id"=>$value["id"],
               "title"=>$value["title"],
               "price"=>$value["price"],
               "cat"=>$value["cat"]
           );
       }
       $result = array(
           "status"=>"OK",
           "values"=>$Values
       );
        PrintOutput($result);
    }else {
        $result = array (
            "status"=>"not ok",
            "message"=>"the id you entered in invalid"
        );
        PrintOutput($result);
        
    }
    
    
});
function PrintOutput($res) {
    header("Content-Type: application/json");
    echo json_encode($res);
    exit;
}

$app->run();