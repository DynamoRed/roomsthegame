<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    try {
        $requester_uid = getLoggedUserId();
        $ranks = getAllRanks();
        for($i = 0; $i < count($ranks); $i++){
            $rank_permissions = getRankPermissions($ranks[$i]['rank_id']);
            $ranks[$i]['permissions'] = $rank_permissions;
        }

        echo json_encode($ranks);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
