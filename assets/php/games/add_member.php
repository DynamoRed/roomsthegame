<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $pcode = $_GET['pcode'];
    $requester_uid = getLoggedUserId();

    if (!isset($pcode) || empty($pcode)) { http_response_code(412); die(); }
    if(!partyExist($pcode)) {
        http_response_code(400);
        return;
    }
    
    try {
        $sql = "SELECT pcode FROM parties_members WHERE uid = :uid AND pcode = :pcode;";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':uid', $requester_uid);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();
        $user_party = $stmt->rowCount();

        if($user_party === 1){
            $sql = "UPDATE parties_members SET is_disconnected = 0 WHERE pcode = :pcode AND uid = :uid;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uid', $requester_uid);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute(); 
        } else {
            $sql = "INSERT INTO parties_members (pcode, uid) VALUES (:pcode, :uid);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':uid', $requester_uid);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute(); 
        }
         

        addPartySystemChat(getUserInfos($requester_uid)['nick']." a rejoint la partie.", $pcode, false);

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a rejoint la partie "' . $pcode . '"');
        echo 'Join ' . $pcode . ' party.';
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>