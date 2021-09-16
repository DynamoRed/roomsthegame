<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    try {
        $users = getAllUsers();
        $requester_uid = getLoggedUserId();

        echo count($users);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
