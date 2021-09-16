<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    $party_code = getUserParty($requester_uid);

    try {
        $messages = getAllPartyMessage($party_code);

        for($i = 0; $i < count($messages); $i++){
            if($messages[$i]['type'] != 1){
                if(userExist($messages[$i]['uid'])){
                    if($messages[$i]['uid'] == $requester_uid) $messages[$i]['is_requester'] = true;
                    $messages[$i]['author'] = getUserInfos($messages[$i]['uid'])['nick'];
                } else { http_response_code(412); return; }
            }
        }

        echo json_encode($messages);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?> 