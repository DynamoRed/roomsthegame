<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['suid']) || empty($_GET['suid'])){ http_response_code(400); die(); }
    $requester_uid = getLoggedUserId();

    try {
        echo getRelation($requester_uid, $_GET['suid']);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>