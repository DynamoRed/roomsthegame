<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    $pcode = getUserParty(getLoggedUserId());

    if (!isset($pcode) || empty($pcode)) { http_response_code(412); die(); }
    if(!partyExist($pcode)) {
        http_response_code(400);
        return;
    }
    
    try {
        $party_members = getPartyMembers($pcode);
        for($i = 0; $i < count($party_members); $i++){
            if(userExist($party_members[$i]['uid'])){
                if($party_members[$i]['is_disconnected']) continue;
                $party_members[$i] = getUserInfos($party_members[$i]['uid']);
                if($party_members[$i]['uid'] == $requester_uid) $party_members[$i]['is_requester'] = true;
                $party_members[$i]['avatar'] = getUserAvatar($party_members[$i]['uid']);
                $party_members[$i]['character_id'] = getPartyMemberInfos($pcode, $party_members[$i]['uid'])['character_id'];
                if($party_members[$i]['uid'] == getPartyInfos($pcode)['manager_uid']) $party_members[$i]['is_manager'] = true;
            } else { http_response_code(412); return; }
        }
        echo json_encode($party_members);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>