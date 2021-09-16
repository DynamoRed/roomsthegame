<?php if(isset($current_notification)){
    echo '<script>addNotification("'. $current_notification['type'] .'", "'. $current_notification['content'] .'", '. $current_notification['duration'] .');</script>';
} 

$actual_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>
<header>
    <div class="hdr" id="header">
        <div class="hdr-left">
            <a href="https://www.roomsthegame.com"><img src="https://assets.roomsthegame.com/images/logo.png" class="small-logo-text"></a>
        </div>
        <div class="hdr-right">
            <div class="user-notifications dropmenu">
                <a class="hdr-link profile dropbutton">
                    <img src="https://assets.roomsthegame.com/images/icons/notifications.svg">
                </a>
                <div class="dropcontent">
                    <div class="user-notification alert">
                        <span class="user-notification-content">Ceci est une notification test</span>
                    </div>
                    <div class="user-notification info">
                        <span class="user-notification-content">Ceci est une notification test Ceci est une notification test</span>
                    </div>
                    <div class="user-notification warning">
                        <span class="user-notification-content">Ceci est une notification test</span>
                    </div>
                    <div class="user-notification success">
                        <span class="user-notification-content">Ceci est une notification test Ceci est une notification test Ceci est une notification test</span>
                    </div>
                    <div class="user-notification">
                        <span class="user-notification-content">Ceci est une notification test Ceci est une notification</span>
                    </div>
                </div>
            </div>
            <div class="dropmenu">
                <a class="hdr-link profile dropbutton">
                    <img src="https://assets.roomsthegame.com/images/icons/expand_more.svg" class="dropmore">
                    <img src="<?= getUserAvatar(getLoggedUserId()) ?>" class="hdr-profile-avatar">
                </a>
                <div class="dropcontent">
                    <a href="https://account.roomsthegame.com" class="hdr-link">Profil</a>
                    <a href="https://auth.roomsthegame.com/sign/out" class="hdr-link">Se Deconnecter</a>
                </div>
            </div>
        </div>
    </div>
</header>