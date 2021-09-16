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
    if (!isset($_GET['user_id']) || empty($_GET['user_id'])) { http_response_code(412); die(); }
    if(!partyExist($pcode)) {
        http_response_code(400);
        return;
    }
    
    try {
        $party_member = getPartyMemberInfos($pcode, $_GET['user_id']);
        echo json_encode($party_member);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>