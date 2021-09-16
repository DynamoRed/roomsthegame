<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_GET['suid']) || empty($_GET['suid'])){ http_response_code(400); die(); }
    $requester_uid = getLoggedUserId();

    try {
        if(getRelation($requester_uid, $_GET['suid']) < 1){ http_response_code(412); die(); }
        $sql = "DELETE FROM users_relations WHERE (fuid = :fuid AND suid = :suid) OR (fuid = :suid AND suid = :fuid);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':fuid', $requester_uid);
        $stmt->bindParam(':suid', $_GET['suid']);
        $stmt->execute();  

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a supprimé <a href="https://account.roomsthegame.com/view/' . $_GET['suid'] . '" target="_BLANK">' . getUserInfos($_GET['suid'] )['nick'] . '</a> de ces amis.');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>