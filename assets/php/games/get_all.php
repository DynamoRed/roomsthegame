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
        $now = time();

        for($i = 0; $i < count($parties); $i++){
            $parties[$i]['manager_nick'] = getUserInfos($parties[$i]['manager_uid'])['nick'];
            $parties[$i]['manager_pic'] = getUserAvatar($parties[$i]['manager_uid']);
            $parties[$i]['count_members'] = getPartyMembersCount($parties[$i]['pcode']);
            if(strtotime($parties[$i]['ending_date'] )< $now) $parties[$i]['ended'] = true;
        }
        
        echo json_encode($parties);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>