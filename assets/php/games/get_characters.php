<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    if(!$requester_uid) return;
    
    try {
        $characters = getAllCharacters();
        echo json_encode($characters);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>