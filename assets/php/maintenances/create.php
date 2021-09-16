<?php
    require '/home/paesgi2021g1/www/assets/php/base.php';

    $db = connectToDatabase();

    if(!getLoggedUserId()){
        http_response_code(400);
        die();
    }

    $requester_uid = getLoggedUserId();

    if(!userHavePermission($requester_uid, "admin_panel.manage.maintenance.create")) { http_response_code(412); die(); }
    if(!isset($_POST['reason']) || !isset($_POST['starting_date']) || !isset($_POST['ending_date'])) { http_response_code(412); die(); }
    
    try {
        $sql = "INSERT INTO maintenances (reason, starting_date, ending_date, created_by) VALUES (:reason, :starting_date, :ending_date, :created_by);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':reason', $_POST['reason']);
        $stmt->bindParam(':starting_date', $_POST['starting_date']);
        $stmt->bindParam(':ending_date', $_POST['ending_date']);
        $stmt->bindParam(':created_by', $requester_uid);
        $stmt->execute();

        registerNewLog('<a href="https://account.roomsthegame.com/view/' . $requester_uid . '" target="_BLANK">' . getUserInfos($requester_uid)['nick'] . '</a> a creer une maintenance pour la raison "' . $_POST['reason'] . '"');
    } catch (exception $e) {
        die($e->getMessage());
        http_response_code(412);
    }
?>