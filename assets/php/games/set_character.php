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
    if (!isset($_GET['character_id']) || empty($_GET['character_id'])) { http_response_code(412); die(); }
    if(!partyExist($pcode)) {
        http_response_code(400);
        return;
    }
    
    try {
        $sql = "UPDATE parties_members SET character_id = :character_id WHERE uid = :uid AND pcode = :pcode;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':character_id', $_GET['character_id']);
        $stmt->bindParam(':uid', $requester_uid);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();

    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>