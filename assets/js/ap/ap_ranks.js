panel.manage.ranks = {};

let ranksPanel;
let ranksContainer;

window.onload = () => {
    ranksPanel = document.getElementById("panelRanks");
    panel.manage.ranks.displayRanks();
};

function validateColorCode(colorCode) {
    const colorCodeRegex = /^\w{6}$/;
    return colorCodeRegex.test(colorCode.toLowerCase());
}

//---- DISPLAY RANKS ----//

panel.manage.ranks.displayRanks = () => {
    while(ranksPanel.lastChild) ranksPanel.lastChild.remove();

    let panelActualsRanksContainer = document.createElement("div");
    panelActualsRanksContainer.classList.add("panel-actual-ranks-container");

    let overlay = createOverlay(document.body);
    loadingIcon = document.createElement("img");
    loadingIcon.src = "https://assets.roomsthegame.com/images/icons/refresh.svg";
    loadingIcon.classList.add("overlay-loading-icon");
    overlay.append(loadingIcon);

    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/ranks/get_all", (response) => {
        removeOverlay();
        let jsonRanks = JSON.parse(response);
        if (!jsonRanks) return;

        for (let rank in jsonRanks) {
            panelActualsRanksContainer.append(panel.manage.ranks.generateRankContainer(jsonRanks[rank]));
        }

        let panelNewRankButtonContainer = document.createElement("div");
        panelNewRankButtonContainer.classList.add("rank-new-button");

        let panelNewRankButton = document.createElement("a");
        panelNewRankButton.classList.add("btn-primary");
        panelNewRankButton.classList.add("slim");
        panelNewRankButton.onclick = () => {
            panel.manage.ranks.openRankCreationPanel();
        };

        let panelNewRankButtonIcon = document.createElement("img");
        panelNewRankButtonIcon.src = "https://assets.roomsthegame.com/images/icons/add.svg";
        panelNewRankButton.append(panelNewRankButtonIcon);
        panelNewRankButton.innerHTML += "Nouveau";
        panelNewRankButtonContainer.append(panelNewRankButton);
        panelActualsRanksContainer.append(panelNewRankButtonContainer);

        ranksPanel.append(panelActualsRanksContainer);
        ranksContainer = panelActualsRanksContainer;

        for(let rank in jsonRanks){
            let inheritRankPermissions = jsonRanks[rank]['permissions'];
            for(let inheritRankPermission in inheritRankPermissions)
                inheritRankPermissions[inheritRankPermission] = inheritRankPermissions[inheritRankPermission]['permission_name'];
        }

        panel.manage.ranks.allRanks = jsonRanks;
    });
};

panel.manage.ranks.generateRankContainer = rank => {
    let rankContainer = document.createElement("div");
    rankContainer.classList.add("panel-rank-container");

    let rankElement = document.createElement("div");
    rankElement.classList.add("user-rank");
    rankElement.innerHTML = rank["name"];
    rankElement.innerHTML += ` (${rank["inherit_lvl"]})`;
    rankElement.style.borderColor = `#${rank["color"]}`;
    rankContainer.append(rankElement);

    let rankActions = document.createElement("div");
    rankActions.classList.add("rank-actions");

    if (rank["rank_id"] != 0) {
        let rankAction = document.createElement("a");
        rankAction.classList.add("rank-edit-button");
        rankAction.innerHTML = "Editer";
        rankAction.onclick = () => {
            panel.manage.ranks.openRankUpdatePanel(rank["rank_id"]);
        };
        rankActions.append(rankAction);

        rankAction = document.createElement("a");
        rankAction.classList.add("rank-delete-button");
        rankAction.innerHTML = "x";
        rankAction.title = "Supprimer";
        rankAction.onclick = () => {
            panel.manage.ranks.removeRank(rank["rank_id"], rankContainer);
        };
        rankActions.append(rankAction);
    }

    rankContainer.append(rankActions);

    return rankContainer;
};



//---- CREATE RANK ----//

panel.manage.ranks.openRankCreationPanel = () => {
    if(!ranksPanel) return;

    let eRankContainers = document.getElementsByClassName("panel-create-ranks");
    let i = 0;
    while(eRankContainers[i]){
       eRankContainers[i].remove(); 
    } 

    let ePermissionsContainers = document.getElementsByClassName("panel-add-rank-permissions");
    while(ePermissionsContainers[i]){
        ePermissionsContainers[i].remove(); 
    } 

    let newRankContainer = document.createElement("div");
    newRankContainer.classList.add("panel-create-ranks");
    newRankContainer.innerHTML = "<h2>Nouveau Rang</h2>";
    newRankContainer.onclick = () => {
        let ePermissionsContainers = document.getElementsByClassName("panel-add-rank-permissions");
        while(ePermissionsContainers.lastChild) ePermissionsContainers.lastChild.remove();
    }

    let newRankInputName = document.createElement("input");
    newRankInputName.type = "text";
    newRankInputName.placeholder = "Nom";
    newRankInputName.oninput = () => {newRankInputName.classList.remove('invalid')};
    newRankContainer.append(newRankInputName);

    let newRankInputColor = document.createElement("input");
    newRankInputColor.type = "text";
    newRankInputColor.placeholder = "Couleur";
    newRankInputColor.oninput = () => {newRankInputColor.classList.remove('invalid')};
    newRankContainer.append(newRankInputColor);

    let newRankInputInheritLvl = document.createElement("input");
    newRankInputInheritLvl.type = "number";
    newRankInputInheritLvl.placeholder = "Level d'Heritage";
    newRankInputInheritLvl.min = 10;
    newRankInputInheritLvl.max = 99;
    newRankInputInheritLvl.oninput = () => {
        newRankInputInheritLvl.classList.remove('invalid');
        if((newRankInputInheritLvl.value > 99 || newRankInputInheritLvl.value < 1) && newRankInputInheritLvl.value.length != 0){
            newRankInputInheritLvl.classList.add("invalid");
            addNotification("warning", "Le level d'heritage doit être entre 10 et 99 !", 5);
            newRankInputInheritLvl.value = 10;
            return false;
        }
    };
    newRankContainer.append(newRankInputInheritLvl);

    let newRankInputOperator = document.createElement("div");
    newRankInputOperator.classList.add("create-rank-is-operator");
    newRankInputOperator.innerHTML = "<span>Opérateur</span>";

    let labelSwitch = document.createElement("label");
    labelSwitch.classList.add("input-switch");

    let inputSwitch = document.createElement("input");
    inputSwitch.type = "checkbox";

    labelSwitch.append(inputSwitch);
    labelSwitch.onchange = () => {
        labelSwitch.classList.toggle("checked");
    }
    labelSwitch.innerHTML += '<span class="switch-round-slider"></span>';      

    newRankInputOperator.append(labelSwitch);
    newRankContainer.append(newRankInputOperator);

    let nextButton = document.createElement("a");
    nextButton.classList.add("btn-primary");
    nextButton.classList.add("btn-left-img");
    nextButton.classList.add("slim");
    nextButton.innerHTML = "Suivant";
    nextButton.onclick = () => {
        let newRankInputNameData = newRankInputName.value;
        let newRankInputColorData = newRankInputColor.value.trim();
        let newRankInputInheritLvlData = newRankInputInheritLvl.value;

        if(newRankInputNameData.length <= 3) {
            newRankInputName.classList.add("invalid");
            addNotification("warning", "Le nom du rang est trop court !", 5);
            return false;
        }

        if(!validateColorCode(newRankInputColorData)) {
            newRankInputColor.classList.add("invalid");
            addNotification("warning", "Votre code couleur doit être en hexadecimal (6 caracteres, Ex: 'ffffff') !", 5);
            return false;
        }

        if(newRankInputInheritLvlData.length == 0) {
            newRankInputInheritLvl.classList.add("invalid");
            addNotification("warning", "Le level d'heritage doit être renseigné !", 5);
            return false;
        }

        if(newRankInputInheritLvlData > 99 || newRankInputInheritLvlData < 10) {
            newRankInputInheritLvl.classList.add("invalid");
            addNotification("warning", "Le level d'heritage doit être entre 10 et 99 !", 5);
            newRankInputInheritLvlData.value = 10;
            return false;
        }

        data = {};
        data['name'] = newRankInputName.value;
        data['color'] = newRankInputColor.value;
        data['inherit_lvl'] = newRankInputInheritLvl.value;
        data['is_operator'] = labelSwitch.classList.contains("checked");

        panel.manage.ranks.openRankCreationPermissionsPanel(data);
    };

    let nextButtonIcon = document.createElement("img");
    nextButtonIcon.src = "https://assets.roomsthegame.com/images/icons/arrow_next.svg";
    nextButton.append(nextButtonIcon);

    newRankContainer.append(nextButton);

    ranksPanel.append(newRankContainer);
};

panel.manage.ranks.openRankCreationPermissionsPanel = data => {
    if(!ranksPanel) return;
    
    let newRankContainer = document.getElementsByClassName("panel-create-ranks")[0];
    if(!newRankContainer) return;

    if(!data || data.length < 4) return;

    let i = 0;
    let ePermissionsContainers = document.getElementsByClassName("panel-add-rank-permissions");
    while(ePermissionsContainers[i]){
        ePermissionsContainers[i].remove(); 
    } 

    let newRankPermissions = [];

    let newRankPermissionsContainer = document.createElement("div");
    newRankPermissionsContainer.classList.add("panel-add-rank-permissions");
    newRankPermissionsContainer.innerHTML = "<h2>Permissions</h2>";

    let permissionsContainer = document.createElement("div");
    permissionsContainer.classList.add("permissions-container");
    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/permissions/get_all", (response) => {
        let jsonPermissions = JSON.parse(response);
        if (!jsonPermissions) return;

        for (let permission in jsonPermissions) {
            let permissionName = jsonPermissions[permission]['permission_name'];
            let permissionContainer = document.createElement("div");
            permissionContainer.classList.add("permission-container");
            permissionContainer.innerHTML = `<span>${permissionName}</span>`;

            let labelCheckmark = document.createElement("label");
            labelCheckmark.classList.add("input-checkmark");                    
            labelCheckmark.title = data['is_operator'] ? "Préremplie car vous avez coché 'Opérateur'" : "";
            labelCheckmark.onchange = () => {
                if(labelCheckmark.classList.contains("disabled")) return;
                if(newRankPermissions.includes(permissionName)){ 
                    for(let i = 0; i < newRankPermissions.length; i++){
                        if(newRankPermissions[i] === permissionName) newRankPermissions.splice(i, i);
                    }
                } else {
                    newRankPermissions.push(permissionName);
                }
                labelCheckmark.classList.toggle("checked");
            }
            
            let inputCheckmark = document.createElement("input");
            inputCheckmark.type = "checkbox";

            for(let inheritRank in panel.manage.ranks.allRanks){
                inheritRank = panel.manage.ranks.allRanks[inheritRank];
                if(inheritRank['inherit_lvl'] < data['inherit_lvl']){
                    if(inheritRank['permissions'].includes(permissionName)){
                        labelCheckmark.title = "Case cochée par héritage";
                        labelCheckmark.classList.add("checked");
                        labelCheckmark.classList.add("disabled");
                    }
                }
            }

            if(data['is_operator']){
                labelCheckmark.classList.add("checked");
                labelCheckmark.classList.add("disabled");
            }

            labelCheckmark.append(inputCheckmark);
            labelCheckmark.innerHTML += '<span class="checkmark"></span>';

            permissionContainer.append(labelCheckmark);
            permissionsContainer.append(permissionContainer);
        }

        newRankPermissionsContainer.append(permissionsContainer);

        let validateButton = document.createElement("a");
        validateButton.classList.add("btn-primary");
        validateButton.classList.add("slim");
        validateButton.onclick = () => {
            data['permissions'] = newRankPermissions;
            panel.manage.ranks.createRank(data);
        };

        let validateButtonIcon = document.createElement("img");
        validateButtonIcon.src = "https://assets.roomsthegame.com/images/icons/check.svg";
        validateButton.append(validateButtonIcon);
        validateButton.innerHTML += "Valider";

        newRankPermissionsContainer.append(validateButton);

        ranksPanel.append(newRankPermissionsContainer);
    });
};

panel.manage.ranks.createRank = data => {
    if(!data || data.length < 2) return;

    let newRankData = new FormData();

    data['is_operator'] = data['is_operator'] ? 1 : 0;

    newRankData.set('name', data['name']);
    newRankData.set('color', data['color']);
    newRankData.set('inherit_lvl', data['inherit_lvl']);
    newRankData.set('is_operator', data['is_operator']);
    newRankData.set('permissions',JSON.stringify(data['permissions']));

    newAJAXBasicRequest("POST", "https://assets.roomsthegame.com/php/ranks/create", (response) => {
        addNotification("success", "Rang créé avec succès !", 5);
        panel.manage.ranks.displayRanks();
    }, newRankData);
}




//---- UPDATE RANK ----//

panel.manage.ranks.openRankUpdatePanel = rankId => {
    if(!ranksPanel) return;

    let eRankContainers = document.getElementsByClassName("panel-create-ranks");
    let i = 0;
    while(eRankContainers[i]){
       eRankContainers[i].remove(); 
    } 

    let ePermissionsContainers = document.getElementsByClassName("panel-add-rank-permissions");
    while(ePermissionsContainers[i]){
        ePermissionsContainers[i].remove(); 
    } 

    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/ranks/get?rank_id=${rankId}`, (response) => {
        let jsonRank = JSON.parse(response);
        if (!jsonRank) return;

        let updateRankContainer = document.createElement("div");
        updateRankContainer.classList.add("panel-create-ranks");
        updateRankContainer.innerHTML = `<h2>Modification Rang ${jsonRank['name']}</h2>`;
        updateRankContainer.onclick = () => {
            let rankPermissionsContainer = document.getElementsByClassName("panel-add-rank-permissions")[0];
            if(rankPermissionsContainer){
                rankPermissionsContainer.remove();
            }
        }

        let updateRankInputName = document.createElement("input");
        updateRankInputName.type = "text";
        updateRankInputName.placeholder = `Nom: ${jsonRank['name']}`;
        updateRankInputName.oninput = () => {updateRankInputName.classList.remove('invalid')};
        updateRankContainer.append(updateRankInputName);
    
        let updateRankInputColor = document.createElement("input");
        updateRankInputColor.type = "text";
        updateRankInputColor.placeholder = `Couleur: ${jsonRank['color']}`;
        updateRankInputColor.oninput = () => {updateRankInputColor.classList.remove('invalid')};
        updateRankContainer.append(updateRankInputColor);
    
        let updateRankInputInheritLvl = document.createElement("input");
        updateRankInputInheritLvl.type = "number";
        updateRankInputInheritLvl.value = jsonRank['inherit_lvl'];
        updateRankInputInheritLvl.min = 2;
        updateRankInputInheritLvl.max = 99;
        updateRankInputInheritLvl.oninput = () => {
            updateRankInputInheritLvl.classList.remove('invalid');
            if((updateRankInputInheritLvl.value > 99 || updateRankInputInheritLvl.value < 1) && updateRankInputInheritLvl.value.length != 0){
                updateRankInputInheritLvl.classList.add("invalid");
                addNotification("warning", "Le level d'heritage doit être entre 10 et 99 !", 5);
                updateRankInputInheritLvl.value = 10;
                return false;
            }
        };
        updateRankContainer.append(updateRankInputInheritLvl);
    
        let updateRankInputOperator = document.createElement("div");
        updateRankInputOperator.classList.add("create-rank-is-operator");
        updateRankInputOperator.innerHTML = "<span>Opérateur</span>";
    
        let labelSwitch = document.createElement("label");
        labelSwitch.classList.add("input-switch");
    
        let inputSwitch = document.createElement("input");
        inputSwitch.type = "checkbox";
        
        if(jsonRank['is_operator'] == "1") labelSwitch.classList.add("checked");

        labelSwitch.append(inputSwitch);
        labelSwitch.onchange = () => {
            labelSwitch.classList.toggle("checked");
        }
        labelSwitch.innerHTML += '<span class="switch-round-slider"></span>';      
    
        updateRankInputOperator.append(labelSwitch);
        updateRankContainer.append(updateRankInputOperator);
    
        let nextButton = document.createElement("a");
        nextButton.classList.add("btn-primary");
        nextButton.classList.add("btn-left-img");
        nextButton.classList.add("slim");
        nextButton.innerHTML = "Suivant";
        nextButton.onclick = () => {
            let updateRankInputNameData = updateRankInputName.value;
            let updateRankInputColorData = updateRankInputColor.value.trim();
            let updateRankInputInheritLvlData = updateRankInputInheritLvl.value;
    
            if(updateRankInputNameData.length <= 3 && updateRankInputNameData.length != 0) {
                updateRankInputName.classList.add("invalid");
                addNotification("warning", "Le nom du rang est trop court !", 5);
                return false;
            }
        
            if(!validateColorCode(updateRankInputColorData) && updateRankInputColorData.length != 0) {
                updateRankInputColor.classList.add("invalid");
                addNotification("warning", "Votre code couleur doit être en hexadecimal (6 caracteres, Ex: 'ffffff') !", 5);
                return false;
            }
    
            if(updateRankInputInheritLvlData.length == 0) {
                updateRankInputInheritLvl.classList.add("invalid");
                addNotification("warning", "Le level d'heritage doit être renseigné !", 5);
                return false;
            }

            if(updateRankInputInheritLvlData > 99 || updateRankInputInheritLvlData < 10) {
                newRankInputInheritLvl.classList.add("invalid");
                addNotification("warning", "Le level d'heritage doit être entre 10 et 99 !", 5);
                newRankInputInheritLvl.value = 10;
                return false;
            }

            for(let jsonRankPerm in jsonRank['permissions']) 
                if(jsonRankPerm['permission_name']) jsonRankPerm = jsonRankPerm['permission_name'];

            data = {};
            data['rank_id'] = rankId;
            data['name'] = updateRankInputName.value || jsonRank['name'];
            data['color'] = updateRankInputColor.value || jsonRank['color'];
            data['inherit_lvl'] = updateRankInputInheritLvl.value || jsonRank['inherit_lvl'];
            data['is_operator'] = labelSwitch.classList.contains("checked");
            data['permissions'] = labelSwitch.classList.contains("checked") ? [] : jsonRank['permissions'];
            panel.manage.ranks.openRankUpdatePermissionsPanel(data);
        };
    
        let nextButtonIcon = document.createElement("img");
        nextButtonIcon.src = "https://assets.roomsthegame.com/images/icons/arrow_next.svg";
        nextButton.append(nextButtonIcon);
    
        updateRankContainer.append(nextButton);
    
        ranksPanel.append(updateRankContainer);
    });
};

panel.manage.ranks.openRankUpdatePermissionsPanel = data => {
    if(!ranksPanel) return;
    
    let updateRankContainer = document.getElementsByClassName("panel-create-ranks")[0];
    if(!updateRankContainer) return;

    if(!data || data.length < 2) return;

    let i = 0;
    let ePermissionsContainers = document.getElementsByClassName("panel-add-rank-permissions");
    while(ePermissionsContainers[i]){
        ePermissionsContainers[i].remove(); 
    } 

    let updateRankPermissions = data['permissions'];
    for(let i = 0; i < updateRankPermissions.length; i++) updateRankPermissions[i] = updateRankPermissions[i]['permission_name'];

    let updateRankPermissionsContainer = document.createElement("div");
    updateRankPermissionsContainer.classList.add("panel-add-rank-permissions");
    updateRankPermissionsContainer.innerHTML = "<h2>Permissions</h2>";

    let permissionsContainer = document.createElement("div");
    permissionsContainer.classList.add("permissions-container");

    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/permissions/get_all", (response) => {
        let jsonPermissions = JSON.parse(response);
        if (!jsonPermissions) return;
        for (let permission in jsonPermissions) {
            let permissionName = jsonPermissions[permission]['permission_name'];
            let permissionContainer = document.createElement("div");
            permissionContainer.classList.add("permission-container");
            permissionContainer.innerHTML = `<span>${permissionName}</span>`;

            let labelCheckmark = document.createElement("label");
            labelCheckmark.classList.add("input-checkmark");                    
            labelCheckmark.title = data['is_operator'] ? "Préremplie car vous avez coché 'Opérateur'" : "";
            labelCheckmark.onchange = () => {
                if(labelCheckmark.classList.contains("disabled")) return;
                if(updateRankPermissions.includes(permissionName)){ 
                    for(let i = 0; i < updateRankPermissions.length; i++)
                        if(updateRankPermissions[i] === permissionName) updateRankPermissions.splice(i, i);
                } else {
                    updateRankPermissions.push(permissionName);
                }
                labelCheckmark.classList.toggle("checked");
            }
            
            let inputCheckmark = document.createElement("input");
            inputCheckmark.type = "checkbox";

            for(let inheritRank in panel.manage.ranks.allRanks){
                inheritRank = panel.manage.ranks.allRanks[inheritRank];
                if(inheritRank['inherit_lvl'] < data['inherit_lvl']){
                    if(inheritRank['permissions'].includes(permissionName)){
                        labelCheckmark.title = "Case cochée par héritage";
                        labelCheckmark.classList.add("checked");
                        labelCheckmark.classList.add("disabled");
                    }
                }
            }

            if(updateRankPermissions.includes(permissionName)){
                labelCheckmark.classList.add("checked");
            }
            if(data['is_operator']){
                labelCheckmark.classList.add("checked");
                labelCheckmark.classList.add("disabled");
            }

            labelCheckmark.append(inputCheckmark);
            labelCheckmark.innerHTML += '<span class="checkmark"></span>';

            permissionContainer.append(labelCheckmark);
            permissionsContainer.append(permissionContainer);
        }

        updateRankPermissionsContainer.append(permissionsContainer);

        let validateButton = document.createElement("a");
        validateButton.classList.add("btn-primary");
        validateButton.classList.add("slim");
        validateButton.onclick = () => {
            data['permissions'] = updateRankPermissions;
            panel.manage.ranks.updateRank(data);
        };

        let validateButtonIcon = document.createElement("img");
        validateButtonIcon.src = "https://assets.roomsthegame.com/images/icons/check.svg";
        validateButton.append(validateButtonIcon);
        validateButton.innerHTML += "Valider";

        updateRankPermissionsContainer.append(validateButton);

        ranksPanel.append(updateRankPermissionsContainer);
    });
};

panel.manage.ranks.updateRank = data => {
    if(!data || data.length < 2) return;

    let updateRankData = new FormData();

    data['is_operator'] = data['is_operator'] ? 1 : 0;

    updateRankData.set('rank_id', data['rank_id']);
    updateRankData.set('name', data['name']);
    updateRankData.set('color', data['color']);
    updateRankData.set('inherit_lvl', data['inherit_lvl']);
    updateRankData.set('is_operator', data['is_operator']);
    updateRankData.set('permissions',JSON.stringify(data['permissions']));

    newAJAXBasicRequest("POST", "https://assets.roomsthegame.com/php/ranks/update", (response) => {
        addNotification("success", "Rang mis a jour avec succès !", 5);
        panel.manage.ranks.displayRanks();
    }, updateRankData);
}




//---- REMOVE RANK ----//

panel.manage.ranks.removeRank = rankId => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/ranks/remove?rank_id=${rankId}`, (response) => {
        addNotification("success", "Rang retiré avec succès !", 5);
        panel.manage.ranks.displayRanks();    
    });
};