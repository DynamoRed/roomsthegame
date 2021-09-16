<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase(); 

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    if(!isset($_POST['rank_id']) && !(isset($_POST['name']) || isset($_POST['color']) || isset($_POST['inherit_lvl']) || isset($_POST['permissions']) || isset($_POST['is_operator']))){
        http_response_code(412);
        die();
    }

    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "ranks.update")) { http_response_code(412); die(); }

    try {
        foreach($_POST as $postkey=>$postdata){
            if($postkey != "rank_id" && $postkey != "permissions") {
                $sql = "UPDATE ranks SET ".$postkey." = :new_value WHERE rank_id = :rank_id;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':new_value', $postdata);
                $stmt->bindParam(':rank_id', $_POST['rank_id']);
                $stmt->execute();
            }
        }

        if(isset($_POST['permissions'])){
            $sql = "DELETE FROM ranks_permissions WHERE rank_id = :rank_id;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':rank_id', $_POST['rank_id']);
            $stmt->execute();
            usleep(100);
            $rank_permissions = json_decode($_POST['permissions']);
            foreach($rank_permissions as $permission){
                $sql = "INSERT INTO ranks_permissions (rank_id, permission_name) VALUES (:rank_id, :permission_name);";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':rank_id', $_POST['rank_id']);
                $stmt->bindParam(':permission_name', $permission);
                $stmt->execute();
            }
        }

        $rank_infos = getRankInfos($_POST['rank_id']);

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a mis a jour le rang "' . $rank_infos['name'] . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>