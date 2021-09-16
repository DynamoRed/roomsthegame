<?php
require '/home/paesgi2021g1/www/assets/php/base.php';

$db = connectToDatabase();

if (!getLoggedUserId()) {
    http_response_code(400);
    die();
}

if (!isset($_POST['content'])) {
    http_response_code(400);
    die();
}

$requester_uid = getLoggedUserId();
$party_code = getUserParty($requester_uid);

try {
    $sql = "INSERT INTO parties_chat_messages (pcode, content, uid) 
            VALUES (:pcode, :content, :uid);";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pcode', $party_code);
    $stmt->bindParam(':content', $_POST['content']);
    $stmt->bindParam(':uid', $requester_uid);
    $stmt->execute();
} catch (exception $e) {
    die($e->getMessage());
    http_response_code(412);
}
