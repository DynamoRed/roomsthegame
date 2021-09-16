<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';
    require '/home/paesgi2021g1/www/assets/php/games/cards.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    $pcode = getUserParty($requester_uid);
    $card = $_GET['c'];

    if (!isset($pcode) || empty($pcode)) { http_response_code(412); die(); }
    if(!partyExist($pcode)) {
        http_response_code(400);
        return;
    }
    if (!isset($card) || empty($card) || !in_array($card, array_keys($GLOBALS['plate_cards']))) { http_response_code(412); die(); }
    
    try {
        $known_cards = json_decode(getPartyInfos($pcode)['known_cards'], true);
        array_push($known_cards, $card);
        $known_cards = json_encode($known_cards);

        $sql = "UPDATE parties SET known_cards = :known_cards WHERE pcode = :pcode;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->bindParam(':known_cards', $known_cards);
        $stmt->execute();          

    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>