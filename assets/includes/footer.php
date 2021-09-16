<footer class="default-footer">
    <div class="top">
        <div class="ftr-top-left">
            <a class="ftr-link" href="mailto:contact@roomsthegame.com">
                <img src="https://assets.roomsthegame.com/images/icons/mail.svg">
                contact@roomsthegame.com
            </a>
            <a class="ftr-link" href="https://www.roomsthegame.com/legal/cgu">
                <img src="https://assets.roomsthegame.com/images/icons/cg.svg">
                CGU
            </a>
            <a class="ftr-link" href="https://www.roomsthegame.com/legal/cgv">
                <img src="https://assets.roomsthegame.com/images/icons/cg.svg">
                CGV
            </a>
            <a class="ftr-link" href="https://www.roomsthegame.com/branding">
                <img src="https://assets.roomsthegame.com/images/icons/share.svg">
                Branding
            </a>
            <a class="ftr-link" href="https://support.roomsthegame.com/">
                <img src="https://assets.roomsthegame.com/images/icons/support.svg">
                Support
            </a>
        </div>
        <div class="ftr-top-middle">
            <img src="https://assets.roomsthegame.com/images/logo-short-white.png" class="logo" alt="Round White Logo">
        </div>
        <div class="ftr-top-right">
            <a class="ftr-link btn-left-img" href="https://www.roomsthegame.com/devpath">
                DevPath
                <img src="https://assets.roomsthegame.com/images/icons/devpath.svg">
            </a>
            <a class="ftr-link btn-left-img" href="https://www.roomsthegame.com/r/facebook">
                Facebook
                <img src="https://assets.roomsthegame.com/images/icons/facebook_white_rounded.svg">
            </a>
            <a class="ftr-link btn-left-img" href="https://www.roomsthegame.com/r/twitter">
                Twitter
                <img src="https://assets.roomsthegame.com/images/icons/twitter_white_rounded.svg">
            </a>
            <a class="ftr-link btn-left-img" href="https://www.roomsthegame.com/r/discord">
                Discord
                <img src="https://assets.roomsthegame.com/images/icons/discord_white_rounded.svg">
            </a>
            <a class="ftr-link btn-left-img" target="_BLANK" href="https://geopalz.fr">
                GeoPelo
                <img src="https://assets.roomsthegame.com/images/icons/geopalz_white.svg">
            </a>
        </div>
    </div>
    <div class="bottom">
        <span>© Rooms The Game 2021 - <?= date('Y') ?> | Tous droits réservés.</span>
    </div>
</footer>
<script src="https://assets.roomsthegame.com/js/burger-menu.js"></script>
<?php if(userIsLogged()){
    $user_preferences = getUserPreferences(getLoggedUserId()); ?>
    <script>
        if(<?php echo $user_preferences['dark_mode'] ?> == "0"){
            document.body.classList.add("lightmode");
        }
    </script>
<?php } ?>