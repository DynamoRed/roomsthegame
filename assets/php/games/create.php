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

    $creation_state = ['state' => 'success', 'details' => ''];

    if (isset($pcode) && !empty($pcode)) { 
        $creation_state['state'] = 'error';
        $creation_state['details'] = '1007';
        echo json_encode($creation_state);
        exit;
    }

    try {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $generated_pcode = '';
    
        for ($i = 0; $i < 7; $i++) {
            $y = rand(0, strlen($characters) - 1);
            $generated_pcode .= $characters[$y];
        }

        $plate = $GLOBALS['plate_cards'];

        $plate_keys = array_keys($plate);
        shuffle($plate_keys);
        $shuffled_plate = array();
        foreach ($plate_keys as $key) $shuffled_plate[$key] = $plate[$key];

        $shuffled_plate_keys = array_keys($shuffled_plate);
        list($shuffled_plate['room_1'],$shuffled_plate[$shuffled_plate_keys[12]]) = array($shuffled_plate[$shuffled_plate_keys[12]],$shuffled_plate['room_1']);

        $known_cards = array();
        
        $shuffled_plate_keys = array_keys($shuffled_plate);
        foreach($shuffled_plate_keys as $key) 
            if($shuffled_plate[$key]['type'] == "central_room") array_push($known_cards, $key);

        $known_cards = json_encode($known_cards);
        $shuffled_plate = json_encode($shuffled_plate);

        $ending_date = time() + 60*60*2;
        $ending_date = date("y-m-d H:i:s", $ending_date);
        
        $sql = 'INSERT INTO parties (pcode, manager_uid, plate, ending_date, known_cards) VALUES (:pcode, :manager_uid, :plate, :ending_date, :known_cards);';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pcode', $generated_pcode);
        $stmt->bindParam(':manager_uid', $requester_uid);
        $stmt->bindParam(':plate', $shuffled_plate);
        $stmt->bindParam(':ending_date', $ending_date);
        $stmt->bindParam(':known_cards', $known_cards);
        $stmt->execute();

        $creation_state['pcode'] = $generated_pcode;
        echo json_encode($creation_state);

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a créé la partie <a href="https://play.roomsthegame.com/game/'.$generated_pcode.'" target="_BLANK">'.$generated_pcode.'</a>');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>