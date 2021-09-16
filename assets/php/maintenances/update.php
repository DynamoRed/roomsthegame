<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "admin_panel.manage.maintenance.update")) { http_response_code(412); die(); }
    if(!isset($_GET['mid'])) { http_response_code(412); die(); }
    $id = $_GET['mid'];
    
    try {
        $sql = "UPDATE maintenances SET reason = :reason, starting_date = :starting_date, ending_date = :ending_date WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':reason', $_POST['reason']);
        $stmt->bindParam(':starting_date', $_POST['starting_date']);
        $stmt->bindParam(':ending_date', $_POST['ending_date']);
        $stmt->execute();

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a mis a jour la maintenance pour la raison "' . $_POST['reason'] . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>