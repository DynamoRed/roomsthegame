<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();
    if(!userHavePermission($requester_uid, "admin_panel.manage.maintenance.remove")) { http_response_code(412); die(); }
    if(!isset($_GET['mid'])) { http_response_code(412); die(); }
    $id = $_GET['mid'];

    try {
        $maintenance = getMaintenanceInfos($id);

        $sql = "DELETE FROM maintenances WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();  

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a supprimé la maintenance du ' . $maintenance['starting_date'] . ' au ' . $maintenance['ending_date'] . ' avec la raison "' . $maintenance['reason'] . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>