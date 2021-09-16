<nav>
    <div class="nav-category">
        <a class="nav-link" href="https://panel.roomsthegame.com">
            <img src="https://assets.roomsthegame.com/images/icons/panel_index.svg" class="nav-link-icon">
            <span class="nav-link-name">Accueil</span>
        </a>
    </div>
    <?php if(userIsOperator(getLoggedUserId())) { ?>
        <div class="nav-category">
            <div class="nav-category-title">Reservés Opérateurs</div>
            <a class="nav-link" href="https://db.roomsthegame.com" target="_BLANK">
                <img src="https://assets.roomsthegame.com/images/icons/panel_database.svg" class="nav-link-icon">
                <span class="nav-link-name">PhpMyAdmin</span>
            </a>
        </div>
    <?php } ?>
    <div class="nav-category">
        <div class="nav-category-title">Gestion</div>
        <?php if(userHavePermission(getLoggedUserId(), "admin_panel.manage.users")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/users">
                <img src="https://assets.roomsthegame.com/images/icons/panel_users.svg" class="nav-link-icon">
                <span class="nav-link-name">Utilisateurs</span>
            </a>
        <?php //} if(userHavePermission(getLoggedUserId(), "admin_panel.manage.gcomponents")) { ?>
            <!--<a class="nav-link" href="https://panel.roomsthegame.com/manage/gcomponents">
                <img src="https://assets.roomsthegame.com/images/icons/panel_games_components.svg" class="nav-link-icon">
                <span class="nav-link-name">Composants de jeu</span>
            </a>-->
        <?php } if(userHavePermission(getLoggedUserId(), "admin_panel.manage.ranks")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/manage/ranks">
                <img src="https://assets.roomsthegame.com/images/icons/panel_ranks.svg" class="nav-link-icon">
                <span class="nav-link-name">Rangs</span>
            </a>
        <?php }?>
    </div>
    <div class="nav-category">
        <div class="nav-category-title">Modération</div>
        <?php if(userHavePermission(getLoggedUserId(), "admin_panel.moderation.games")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/games">
                <img src="https://assets.roomsthegame.com/images/icons/panel_games.svg" class="nav-link-icon">
                <span class="nav-link-name">Parties en cours</span>
            </a>
        <?php } if(userHavePermission(getLoggedUserId(), "admin_panel.moderation.logs")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/logs">
                <img src="https://assets.roomsthegame.com/images/icons/panel_logs.svg" class="nav-link-icon">
                <span class="nav-link-name">Logs Générales</span>
            </a>
        <?php } ?>
    </div>
    <div class="nav-category others">
        <div class="nav-category-title">Autres</div>
        <?php if(userHavePermission(getLoggedUserId(), "admin_panel.news")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/manage/news">
                <img src="https://assets.roomsthegame.com/images/icons/panel_news.svg" class="nav-link-icon">
                <span class="nav-link-name">Actualités</span>
            </a>
        <?php } if(userHavePermission(getLoggedUserId(), "admin_panel.devpath")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/manage/devpath">
                <img src="https://assets.roomsthegame.com/images/icons/panel_devpath.svg" class="nav-link-icon">
                <span class="nav-link-name">Devpath</span>
            </a>
        <?php } if(userHavePermission(getLoggedUserId(), "admin_panel.support")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/manage/support">
                <img src="https://assets.roomsthegame.com/images/icons/panel_support.svg" class="nav-link-icon">
                <span class="nav-link-name">Support</span>
            </a>
        <?php } if(userHavePermission(getLoggedUserId(), "admin_panel.redirections")) { ?>
            <a class="nav-link" href="https://panel.roomsthegame.com/manage/redirections">
                <img src="https://assets.roomsthegame.com/images/icons/panel_redirect.svg" class="nav-link-icon">
                <span class="nav-link-name">Redirections</span>
            </a>
        <?php } ?>
    </div>
</nav>