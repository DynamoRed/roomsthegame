<?php
    if(isset($_GET['gid']) && !empty($_GET['gid'])){
        header("Location: https://play.roomsthegame.com/game/" . $_GET['gid']);
        exit;
    } else {
        header("Location: https://www.roomsthegame.com");
        exit;
    }
?>