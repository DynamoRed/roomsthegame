<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();

    try {
        $parties = getAllParties();

        for($i = 0; $i < count($parties); $i++){
            $parties[$i]['count_members'] = getPartyMembersCount($parties[$i]['pcode']);
        }
        
        echo json_encode($parties);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>