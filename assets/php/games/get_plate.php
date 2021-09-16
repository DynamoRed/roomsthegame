<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';
    require '/home/paesgi2021g1/www/assets/php/games/cards.php';

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
        $known_cards = json_decode(getPartyInfos($pcode)['known_cards'], true);
        $plate = getPartyInfos($pcode)['plate'];
        foreach($plate as $card => $room_infos){
            if(!in_array($card, $known_cards)){
                $plate[$card] = [
                    'type' =>'not_discovered_room',
                    'name' => 'Salle non decouverte',
                    'description' => 'Vous ne connaissez pas encore cette case. Vous rendre dedans sera a vos risques et périls.'
                ];
            }
        }
        echo json_encode($plate);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>