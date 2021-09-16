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
        $sql = "UPDATE parties_members SET is_disconnected = 1 WHERE pcode = :pcode AND uid = :uid;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':uid', $requester_uid);
        $stmt->bindParam(':pcode', $pcode);
        $stmt->execute();

        addPartySystemChat(getUserInfos($requester_uid)['nick']." a quitté la partie.", $pcode, false);

        if(getPartyMembersCount($pcode) == 0) {
            $sql = 'DELETE FROM parties WHERE pcode = :pcode;';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':pcode', $pcode);
            $stmt->execute();
        }

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a quitté la partie "' . $pcode . '"');
        echo "Exit Game";
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>