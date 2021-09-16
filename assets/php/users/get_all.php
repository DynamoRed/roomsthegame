<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    try {
        $users = getAllUsers(isset($_GET['skipped']) ? $_GET['skipped'] : 0, isset($_GET['count']) ? $_GET['count'] : 0);
        $requester_uid = getLoggedUserId();
        for($i = 0; $i < count($users); $i++){
            $users[$i]['avatar'] = getUserAvatar($users[$i]['uid']);
            $users[$i]['private_profile'] = getUserPreferences($users[$i]['uid']) ? getUserPreferences($users[$i]['uid'])['private_profile'] : false;  /* C'est cette ligne qui fait planter l'affichage */
            if(userIsBan($users[$i]['uid'])){
                $users[$i]['ban'] = getBanReason($users[$i]['uid']);
            }
        }

        echo json_encode($users);
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>
