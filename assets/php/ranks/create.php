<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_POST['name']) || !isset($_POST['color']) || !isset($_POST['inherit_lvl']) || !isset($_POST['permissions'])){ http_response_code(400); die();}
    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "ranks.create")) { http_response_code(412); die(); }

    try {
        $sql = "INSERT INTO ranks (name, color, inherit_lvl, is_operator) VALUES (:name, :color, :inherit_lvl, :is_operator);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':color', $_POST['color']);
        $stmt->bindParam(':inherit_lvl', $_POST['inherit_lvl']);
        $stmt->bindParam(':is_operator', $_POST['is_operator']);
        $stmt->execute();  

        $last_id = $db->lastInsertId();
        $sql = "SELECT * FROM ranks WHERE rank_id = :new_rank_id";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':new_rank_id', $last_id);
        $stmt->execute();
        $new_rank = $stmt->fetch();
        
        $rank_permissions = json_decode($_POST['permissions']);
        foreach($rank_permissions as $permission){
            $sql = "INSERT INTO ranks_permissions (rank_id, permission_name) VALUES (:rank_id, :permission_name);";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':rank_id', $new_rank['rank_id']);
            $stmt->bindParam(':permission_name', $permission);
            $stmt->execute();
        }

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a creer le rang "' . $_POST['name'] . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>