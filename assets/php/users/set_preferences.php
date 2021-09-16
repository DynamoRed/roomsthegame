<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }
 
    if(isset($_POST['private_profile'])){
        $requester_uid = getLoggedUserId();
        try {
            $switchIsActive = $_POST['private_profile'];
            $sql = "UPDATE users_preferences SET private_profile = :preference_state WHERE uid = :uid;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':preference_state', $switchIsActive);
            $stmt->bindParam(':uid', $requester_uid);
            $stmt->execute();

            $profile_status = $switchIsActive ? 'privé' : 'public';
            registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a rendu son profil ' .$profile_status);
        } catch (exception $e) {
            die($e->getMessage());
            http_response_code(412);
        }
    } else
    
    if(isset($_POST['dark_mode'])){
        $requester_uid = getLoggedUserId();
        try{
            $switchIsActive = $_POST['dark_mode'];
            $sql = "UPDATE users_preferences SET dark_mode = :preference_state WHERE uid = :uid;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':preference_state', $switchIsActive);
            $stmt->bindParam(':uid', $requester_uid);
            $stmt->execute();

            registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a changé ses preferences');
        } catch (exception $e) {
            die($e->getMessage());
            http_response_code(412);
        }
    } else { http_response_code(400); die(); }
