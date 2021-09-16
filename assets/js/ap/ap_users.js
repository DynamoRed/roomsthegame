panel.manage.users = {};

let usersContainer;
let canRefresh = true;

window.onload = () =>  {
  usersContainer = document.getElementsByClassName("panel-users")[0];
  panel.manage.users.displayUsers();

  let searchBar = document.getElementById("searchBar");
  let searchBarInput = document.getElementById("searchBarInput");
  if (!searchBar || !searchBarInput) return;
  searchBar.addEventListener("keydown", evt => {
    var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
    if (keyCode == 13) {
      if (searchBarInput.value.length < 3) {
        addNotification(
          "warning",
          "Votre recherche doit contenir au moins 3 caractères",
          1
        );
        return;
      }

      panel.manage.users.displaySearchedUsers(searchBarInput.value);
    }
  });

  searchBarInput.addEventListener("input", () => {
    searchBarInput.value = searchBarInput.value.trim();
  });

  searchBar.children[0].onclick = () => {
    if (searchBarInput.value.length < 3) {
      addNotification(
        "warning",
        "Votre recherche doit contenir au moins 3 caractères",
        1
      );
      return;
    }

    panel.manage.users.displaySearchedUsers(searchBarInput.value);
  };
};

window.onscroll = () =>  {
  if (!canRefresh) return;
  if (!usersContainer) return;
  let y = window.pageYOffset + window.innerHeight;

  if (y >= usersContainer.offsetHeight) {
    canRefresh = false;

    newAJAXBasicRequest(
      "GET",
      `https://assets.roomsthegame.com/php/users/get_all?count=6&skipped=${usersContainer.children.length}`,
      response => {
        let jsonUsers = JSON.parse(response);
        if (!jsonUsers) return;

        for (let user in jsonUsers) {
          usersContainer.append(
            panel.manage.users.generateUserContainer(jsonUsers[user])
          );
        }

        setTimeout(() => {
          canRefresh = true;
        }, 250);
      }
    );
  }
};

panel.manage.users.generateUserContainer = user => {
  let panelUserContainer = document.createElement("div");
  panelUserContainer.classList.add("panel-user-container");

  let panelUserBadges = document.createElement("div");
  panelUserBadges.classList.add("panel-user-badges");

  let panelUserTopContainer = document.createElement("div");
  panelUserTopContainer.classList.add("panel-user-top");

  let userIsBan = user["ban"];

  if (user["verified_account"] == "1") {
    let badgeVerifiedUser = document.createElement("img");
    badgeVerifiedUser.classList.add("badge-account-icon");
    badgeVerifiedUser.alt = "Compte Vérifié";
    badgeVerifiedUser.src =
      "https://assets.roomsthegame.com/images/icons/verified_user.svg";
    panelUserBadges.append(badgeVerifiedUser);
  }

  if (user["private_prinile"] == "1") {
    let badgePrivatePrinile = document.createElement("img");
    badgePrivatePrinile.classList.add("badge-account-icon");
    badgePrivatePrinile.alt = "Prinil Privé";
    badgePrivatePrinile.src =
      "https://assets.roomsthegame.com/images/icons/private_prinile.svg";
    panelUserBadges.append(badgePrivatePrinile);
  }

  panelUserTopContainer.append(panelUserBadges);

  let panelUserAvatar = document.createElement("img");
  panelUserAvatar.classList.add("panel-user-avatar");
  panelUserAvatar.alt = "Avatar";
  panelUserAvatar.src = user["avatar"];

  panelUserTopContainer.append(panelUserAvatar);

  let panelUserInfos = document.createElement("div");
  panelUserInfos.classList.add("panel-user-infos");

  let panelUserID = document.createElement("h4");
  panelUserID.innerHTML = `ID: <span>${user["uid"]}</span>`;
  panelUserInfos.append(panelUserID);

  let panelUserNick = document.createElement("h4");
  panelUserNick.innerHTML = `Pseudo: <span>${user["nick"]}</span>`;
  panelUserInfos.append(panelUserNick);

  let panelUserMail = document.createElement("h4");
  panelUserMail.innerHTML = `Mail: <span>${user["mail"]}</span>`;
  panelUserInfos.append(panelUserMail);

  panelUserTopContainer.append(panelUserInfos);

  panelUserContainer.append(panelUserTopContainer);

  if (!userIsBan) {
    let panelUserActions = document.createElement("div");
    panelUserActions.classList.add("panel-user-actions");

    let panelUserAction = document.createElement("a");
    panelUserAction.classList.add("btn-primary");
    panelUserAction.classList.add("slim");
    panelUserAction.classList.add("icon-only");
    panelUserAction.title = "Bannir";
    panelUserAction.onclick = () =>  {
      panel.manage.users.openBanPanel(user["uid"]);
    };

    let panelUserActionIcon = document.createElement("img");
    panelUserActionIcon.src =
      "https://assets.roomsthegame.com/images/icons/ban.svg";
    panelUserAction.append(panelUserActionIcon);

    panelUserActions.append(panelUserAction);

    panelUserAction = document.createElement("a");
    panelUserAction.classList.add("btn-primary");
    panelUserAction.classList.add("slim");
    panelUserAction.classList.add("icon-only");
    panelUserAction.title = "Gerer";
    panelUserAction.onclick = () =>  {
      // panel.manage.users.prepareManagePanel(user["uid"]);
      panel.manage.users.openManagePanel(user["uid"]);
    };

    panelUserActionIcon = document.createElement("img");
    panelUserActionIcon.src =
      "https://assets.roomsthegame.com/images/icons/settings.svg";
    panelUserAction.append(panelUserActionIcon);

    panelUserActions.append(panelUserAction);

    panelUserAction = document.createElement("a");
    panelUserAction.classList.add("btn-primary");
    panelUserAction.classList.add("slim");
    panelUserAction.classList.add("icon-only");
    panelUserAction.title = "En voir plus";
    panelUserAction.href =
    `https://account.roomsthegame.com/view/${user["uid"]}`;
    panelUserAction.target = "_BLANK";

    panelUserActionIcon = document.createElement("img");
    panelUserActionIcon.src =
      "https://assets.roomsthegame.com/images/icons/add.svg";
    panelUserAction.append(panelUserActionIcon);

    panelUserActions.append(panelUserAction);

    panelUserContainer.append(panelUserActions);
  } else {
    let panelUserBan = document.createElement("div");
    panelUserBan.classList.add("panel-user-ban");
    panelUserBan.innerHTML = `<h1>Banni</h1><p>(${user["ban"]})</p>`;

    let panelUserUnbanButton = document.createElement("a");
    panelUserUnbanButton.classList.add("btn-primary");
    panelUserUnbanButton.classList.add("slim");
    panelUserUnbanButton.title = "Débannir";
    panelUserUnbanButton.innerHTML =
      "<img src='https://assets.roomsthegame.com/images/icons/ban.svg'>Débannir";
    panelUserUnbanButton.onclick = () =>  {
      panel.manage.users.unbanUser(user["uid"]);
    };

    panelUserBan.append(panelUserUnbanButton);
    panelUserContainer.append(panelUserBan);
  }

  return panelUserContainer;
};

panel.manage.users.displaySearchedUsers = query => {
  if (!usersContainer) return;

  let overlay = createOverlay(document.body);
  loadingIcon = document.createElement("img");
  loadingIcon.src = "https://assets.roomsthegame.com/images/icons/refresh.svg";
  loadingIcon.classList.add("overlay-loading-icon");
  overlay.append(loadingIcon);
  addNotification("info", "Recherche en cours...", 1);

  newAJAXBasicRequest(
    "GET",
    `https://assets.roomsthegame.com/php/users/search?query=${query}`,
    response => {
      removeOverlay();
      let jsonUsers = JSON.parse(response);
      if (!jsonUsers) return;

      while (usersContainer.lastChild) {
        usersContainer.removeChild(usersContainer.lastChild);
      }

      for (let user in jsonUsers) {
        usersContainer.append(
          panel.manage.users.generateUserContainer(jsonUsers[user])
        );
      }
    }
  );
};

panel.manage.users.displayUsers = () =>  {
  if (!usersContainer) return;

  let overlay = createOverlay(document.body);
  loadingIcon = document.createElement("img");
  loadingIcon.src = "https://assets.roomsthegame.com/images/icons/refresh.svg";
  loadingIcon.classList.add("overlay-loading-icon");
  overlay.append(loadingIcon);
  addNotification("info", "Rafraichissement en cours...", 5);

  newAJAXBasicRequest(
    "GET",
    "https://assets.roomsthegame.com/php/users/get_all?count=12",
    response => {
      removeOverlay();

      let jsonUsers = JSON.parse(response);
      if (!jsonUsers) return;

      while (usersContainer.lastChild) {
        usersContainer.removeChild(usersContainer.lastChild);
      }

      for (let user in jsonUsers) {
        usersContainer.append(
          panel.manage.users.generateUserContainer(jsonUsers[user])
        );
      }

      addNotification("success", "Rafraichissement effectué !", 5);
    }
  );
};

panel.manage.users.openBanPanel = userId => {
  let overlay = createOverlay(document.body);

  let overlayCloseButton = document.createElement("a");
  overlayCloseButton.classList.add("overlay-close-button");
  overlayCloseButton.innerText = "x";
  overlayCloseButton.onclick = () =>  {
    overlay.remove();
  };
  overlay.append(overlayCloseButton);

  let overlayContainer = document.createElement("div");
  overlayContainer.classList.add("overlay-container");
  overlay.append(overlayContainer);

  let overlayBanInput = document.createElement("input");
  overlayBanInput.classList.add("overlay-input");
  overlayBanInput.placeholder = "Raison du bannissement";
  overlayBanInput.type = "text";
  overlayContainer.append(overlayBanInput);

  let overlayBanButton = document.createElement("a");
  overlayBanButton.classList.add("btn-primary");
  let overlayBanButtonIcon = document.createElement("img");
  overlayBanButtonIcon.src =
    "https://assets.roomsthegame.com/images/icons/ban.svg";
  overlayBanButton.append(overlayBanButtonIcon);
  overlayBanButton.innerHTML += "Bannir";
  overlayBanButton.onclick = () =>  {
    if (overlayBanInput.value.length <= 3) {
      addNotification("warning", "Vous devez saisir une raison valide !", 5);
    } else {
      newAJAXBasicRequest(
        "GET",
        `https://assets.roomsthegame.com/php/users/ban?user_id=${userId}&reason${overlayBanInput.value}`,
        response => {
          addNotification("success", "Utilisateur banni avec succès !", 5);
          removeOverlay();
          panel.manage.users.displayUsers();
        }
      );
    }
  };
  overlayContainer.append(overlayBanButton);
};

panel.manage.users.openManagePanel = userId => {
  let overlay = createOverlay(document.body);

  let overlayCloseButton = document.createElement("a");
  overlayCloseButton.classList.add("overlay-close-button");
  overlayCloseButton.innerText = "x";
  overlayCloseButton.onclick = () =>  {
    overlay.remove();
  };
  overlay.append(overlayCloseButton);

  let overlayContainer = document.createElement("div");
  overlayContainer.classList.add("overlay-container");
  overlay.append(overlayContainer);
  overlayContainer.innerHTML = "<h2>Rang(s) du joueur</h2>";

  newAJAXBasicRequest(
    "GET",
    `https://assets.roomsthegame.com/php/users/get?user_id=${userId}`,
    response => {
      let jsonUser = JSON.parse(response);
      if (!jsonUser) return;
      req = newAJAXBasicRequest(
        "GET",
        "https://assets.roomsthegame.com/php/ranks/get_all",
        response => {
          let jsonRanks = JSON.parse(response);
          if (!jsonRanks) return;
          jsonUser["user_ranks"].forEach(userRank => {
            jsonRanks.forEach(rank => {
              if (userRank.name == rank.name) {
                let rankContainer = document.createElement("div");
                rankContainer.classList.add("panel-rank-container");
                rankContainer.id = rank.rank_id;

                let rankElement = document.createElement("div");
                rankElement.classList.add("user-rank");
                rankElement.innerHTML = rank.name;
                rankElement.style.borderColor = `#${rank.color}`;
                rankContainer.append(rankElement);

                let rankActions = document.createElement("div");
                rankActions.classList.add("rank-actions");

                if (rank.rank_id != 0) {
                  rankAction = document.createElement("a");
                  rankAction.classList.add("rank-delete-button");
                  rankAction.innerHTML = "x";
                  rankAction.title = "Supprimer";
                  rankAction.onclick = () =>  {
                    newAJAXBasicRequest(
                      "GET",
                      `https://assets.roomsthegame.com/php/users/remove_rank?rank_id=${rank.rank_id}&user_id=${jsonUser["uid"]}`,
                      response => {
                        document.getElementById(rank.rank_id).remove();
                        addNotification(
                          "success",
                          "Rang retiré avec succès !",
                          5
                        );
                      }
                    );
                  };
                  rankActions.append(rankAction);
                }
                rankContainer.append(rankActions);
                overlayContainer.append(rankContainer);
              }
            });
          });
          let panelAddRank = document.createElement("div");
          panelAddRank.classList.add("panel-rank-container");
          panelAddRank.classList.add("add-rank");

          let panelAddRankButton = document.createElement("a");
          panelAddRankButton.classList.add("rank-add-button");
          panelAddRankButton.innerHTML = "+";
          panelAddRankButton.title = "Ajouter";
          panelAddRankButton.onclick = () =>  {
            let addRankContainers = document.getElementsByClassName(
              "panel-add-ranks"
            );
            while(addRankContainers.lastChild) addRankContainers.lastChild.remove();

            let newRankContainer = document.createElement("div");
            newRankContainer.classList.add("panel-add-ranks");
            newRankContainer.innerHTML = "<h2>Ajouter Rang(s)</h2>";

            let ranksContainer = document.createElement("div");
            ranksContainer.classList.add("ranks-container");

            for (let rank in jsonRanks) {
              let find = false;
              for (let userRank in jsonUser["user_ranks"]) {
                if (
                  jsonRanks[rank]["rank_id"] ===
                  jsonUser["user_ranks"][userRank]["rank_id"]
                ) {
                  find = true;
                }
              }
              if (!find) {
                let rankContainer = document.createElement("div");
                rankContainer.classList.add("rank-container");
                rankContainer.innerHTML = `<span>${jsonRanks[rank]["name"]}</span>`;

                let labelCheckmark = document.createElement("label");
                labelCheckmark.classList.add("input-checkmark");
                labelCheckmark.onchange = () =>  {
                  labelCheckmark.classList.toggle("checked");
                };

                let inputCheckmark = document.createElement("input");
                inputCheckmark.type = "checkbox";

                labelCheckmark.append(inputCheckmark);
                labelCheckmark.innerHTML += '<span class="checkmark"></span>';

                rankContainer.append(labelCheckmark);
                ranksContainer.append(rankContainer);
                newRankContainer.append(ranksContainer);
              }
            }
            let overlaySubmitButton = document.createElement("a");
            overlaySubmitButton.classList.add("btn-primary");
            let overlaySubmitButtonIcon = document.createElement("img");
            overlaySubmitButtonIcon.src =
              "https://assets.roomsthegame.com/images/icons/send.svg";
            overlaySubmitButton.append(overlaySubmitButtonIcon);
            overlaySubmitButton.innerHTML += "Soumettre";
            overlaySubmitButton.onclick = () =>  {
              addRankContainers[0].remove();
            };
            newRankContainer.append(overlaySubmitButton);
            overlay.append(newRankContainer);
          };
          overlayContainer.append(panelAddRankButton);
        }
      );
    }
  );
};

panel.manage.users.unbanUser = userId => {
  newAJAXBasicRequest(
    "GET",
    `https://assets.roomsthegame.com/php/users/unban?user_id=${userId}`,
    response => {
      addNotification("success", "Utilisateur débanni avec succès !", 5);
      panel.manage.users.displayUsers();
    }
  );
};
