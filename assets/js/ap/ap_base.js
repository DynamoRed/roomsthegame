var documentFragment = document.createDocumentFragment();

panel = {};
panel.manage = {};

window.onload = () => {
    let logsContainer = document.getElementById("logsContainer");
    if (logsContainer) logsContainer.scrollTop = logsContainer.scrollHeight;

    let maintenanceContainer = document.getElementById("maintenanceContainer");
    if (!maintenanceContainer) return;

    let overlay = createOverlay(document.body);
    loadingIcon = document.createElement("img");
    loadingIcon.src = "https://assets.roomsthegame.com/images/icons/refresh.svg";
    loadingIcon.classList.add("overlay-loading-icon");
    overlay.append(loadingIcon);

    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/maintenances/get_all", (response) => {
        removeOverlay();

        let jsonMaintenances = JSON.parse(response);
        if (!jsonMaintenances) return;

        for (let maintenance in jsonMaintenances) {
            if (jsonMaintenances[maintenance]["is_passed"]) continue;
            maintenanceContainer.append(generateMaintenanceContainer(jsonMaintenances[maintenance]));
        }
    });
};

function generateMaintenanceContainer(data) {
    let container = document.createElement("div");
    container.classList.add("panel-main-maintenance");
    container.id = `maintenance${data["id"]}`;

    let containerLeft = document.createElement("div");
    containerLeft.classList.add("panel-main-maintenance-left");

    let containerRight = document.createElement("div");
    containerRight.classList.add("panel-main-maintenance-right");

    let maintenanceDating = document.createElement("span");
    maintenanceDating.innerHTML = `${data["is_active"] ? `<span class="maintenance-active">Active</span> jusqu'` : `Du ${data["starting_date"]} `}au ${data["ending_date"]}`;

    let reason = document.createElement("p");
    reason.innerText = data["reason"];

    let createdBy = document.createElement("h2");
    createdBy.innerText = `Initiée par ${data["created_by"]["nick"]}`;

    containerLeft.append(maintenanceDating);
    containerLeft.append(reason);
    containerLeft.append(createdBy);

    let editButton = document.createElement("a");
    editButton.classList.add("maintenance-button");
    editButton.classList.add("edit");
    editButton.onclick = () => {
        panel.openEditMaintenancePanel(data["id"]);
    };
    let editButtonIcon = document.createElement("img");
    editButtonIcon.src = "https://assets.roomsthegame.com/images/icons/write.svg";
    editButtonIcon.title = "Editer";
    editButton.append(editButtonIcon);

    let removeButton = document.createElement("a");
    removeButton.classList.add("maintenance-button");
    removeButton.classList.add("remove");
    removeButton.onclick = () => {
        panel.removeMaintenance(data["id"]);
    };
    let removeButtonIcon = document.createElement("img");
    removeButtonIcon.src = "https://assets.roomsthegame.com/images/icons/cross.svg";
    removeButtonIcon.title = "Supprimer";
    removeButton.append(removeButtonIcon);

    containerRight.append(editButton);
    containerRight.append(removeButton);

    container.append(containerLeft);
    container.append(containerRight);

    return container;
}

panel.removeMaintenance = (id) => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/maintenances/remove?mid=${id}`, (response) => {
        let container = document.getElementById(`maintenance${id}`);
        if (container) container.remove();
        addNotification("success", "Maintenance supprimée !", 5);
    });
};

panel.openEditMaintenancePanel = (id) => {
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/maintenances/get?mid=${id}`, (response) => {
        let jsonMaintenance = JSON.parse(response);
        if (!jsonMaintenance) return;

        let overlay = createOverlay(document.body);

        let overlayCloseButton = document.createElement("a");
        overlayCloseButton.classList.add("overlay-close-button");
        overlayCloseButton.innerText = "x";
        overlayCloseButton.onclick = () => {
            overlay.remove();
        };
        overlay.append(overlayCloseButton);

        let overlayContainer = document.createElement("div");
        overlayContainer.classList.add("overlay-container");
        overlay.append(overlayContainer);

        let updateMaintenanceReason = document.createElement("input");
        updateMaintenanceReason.classList.add("overlay-input");
        updateMaintenanceReason.id = "updateMaintenanceReason";
        updateMaintenanceReason.placeholder = "Raison de la maintenance";
        updateMaintenanceReason.type = "text";
        updateMaintenanceReason.value = jsonMaintenance["reason"];
        overlayContainer.append(updateMaintenanceReason);

        overlayContainer.innerHTML += "<span>Date de début</span>";
        let updateMaintenanceStartingDate = document.createElement("input");
        updateMaintenanceStartingDate.id = "updateMaintenanceStartingDate";
        updateMaintenanceStartingDate.classList.add("overlay-input");
        updateMaintenanceStartingDate.type = "datetime-local";
        updateMaintenanceStartingDate.value = jsonMaintenance["starting_date"];
        overlayContainer.append(updateMaintenanceStartingDate);

        overlayContainer.innerHTML += "<span>Date de fin</span>";
        let updateMaintenanceEndingDate = document.createElement("input");
        updateMaintenanceEndingDate.classList.add("overlay-input");
        updateMaintenanceEndingDate.id = "updateMaintenanceEndingDate";
        updateMaintenanceEndingDate.type = "datetime-local";
        updateMaintenanceEndingDate.value = jsonMaintenance["ending_date"];
        overlayContainer.append(updateMaintenanceEndingDate);

        let updateMaintenanceButton = document.createElement("a");
        updateMaintenanceButton.classList.add("btn-primary");
        let updateMaintenanceButtonIcon = document.createElement("img");
        updateMaintenanceButtonIcon.src = "https://assets.roomsthegame.com/images/icons/write.svg";
        updateMaintenanceButton.append(updateMaintenanceButtonIcon);
        updateMaintenanceButton.innerHTML += "Enregistrer";
        updateMaintenanceButton.onclick = () => {
            panel.updateMaintenance(jsonMaintenance["id"]);
        };
        overlayContainer.append(updateMaintenanceButton);
    });
};

panel.openNewMaintenancePanel = () => {
    let overlay = createOverlay(document.body);

    let overlayCloseButton = document.createElement("a");
    overlayCloseButton.classList.add("overlay-close-button");
    overlayCloseButton.innerText = "x";
    overlayCloseButton.onclick = () => {
        overlay.remove();
    };
    overlay.append(overlayCloseButton);

    let overlayContainer = document.createElement("div");
    overlayContainer.classList.add("overlay-container");
    overlay.append(overlayContainer);

    let newMaintenanceReason = document.createElement("input");
    newMaintenanceReason.classList.add("overlay-input");
    newMaintenanceReason.id = "newMaintenanceReason";
    newMaintenanceReason.placeholder = "Raison de la maintenance";
    newMaintenanceReason.type = "text";
    overlayContainer.append(newMaintenanceReason);

    overlayContainer.innerHTML += "<span>Date de début</span>";
    let newMaintenanceStartingDate = document.createElement("input");
    newMaintenanceStartingDate.id = "newMaintenanceStartingDate";
    newMaintenanceStartingDate.classList.add("overlay-input");
    newMaintenanceStartingDate.type = "datetime-local";
    overlayContainer.append(newMaintenanceStartingDate);

    overlayContainer.innerHTML += "<span>Date de fin</span>";
    let newMaintenanceEndingDate = document.createElement("input");
    newMaintenanceEndingDate.id = "newMaintenanceEndingDate";
    newMaintenanceEndingDate.classList.add("overlay-input");
    newMaintenanceEndingDate.type = "datetime-local";
    overlayContainer.append(newMaintenanceEndingDate);

    let newMaintenanceButton = document.createElement("a");
    newMaintenanceButton.classList.add("btn-primary");
    let newMaintenanceButtonIcon = document.createElement("img");
    newMaintenanceButtonIcon.src = "https://assets.roomsthegame.com/images/icons/save.svg";
    newMaintenanceButton.append(newMaintenanceButtonIcon);
    newMaintenanceButton.innerHTML += "Creer";
    newMaintenanceButton.onclick = () => {
        panel.createMaintenance();
    };
    overlayContainer.append(newMaintenanceButton);
};

panel.createMaintenance = () => {
    let newMaintenanceReason = document.getElementById("newMaintenanceReason");
    let newMaintenanceStartingDate = document.getElementById("newMaintenanceStartingDate");
    let newMaintenanceEndingDate = document.getElementById("newMaintenanceEndingDate");

    if (!newMaintenanceEndingDate) return;
    if (!newMaintenanceStartingDate) return;
    if (!newMaintenanceEndingDate) return;

    if (newMaintenanceReason.value.length <= 5 || !newMaintenanceStartingDate.value || !newMaintenanceEndingDate.value) {
        addNotification("warning", "Vous devez saisir des valeurs valides !", 5);
    } else {
        let newMaintenanceData = new FormData();
        newMaintenanceData.set("reason", newMaintenanceReason.value);
        newMaintenanceData.set("starting_date", newMaintenanceStartingDate.value);
        newMaintenanceData.set("ending_date", newMaintenanceEndingDate.value);

        newAJAXBasicRequest(
            "POST",
            `https://assets.roomsthegame.com/php/maintenances/create`,
            (response) => {
                addNotification("success", "Maintenance créée avec succès !", 5);
                removeOverlay();
                window.location.reload();
            },
            newMaintenanceData
        );
    }
};

panel.updateMaintenance = (id) => {
    let updateMaintenanceReason = document.getElementById("updateMaintenanceReason");
    let updateMaintenanceStartingDate = document.getElementById("updateMaintenanceStartingDate");
    let updateMaintenanceEndingDate = document.getElementById("updateMaintenanceEndingDate");

    if (!updateMaintenanceReason) return;
    if (!updateMaintenanceStartingDate) return;
    if (!updateMaintenanceEndingDate) return;

    if (updateMaintenanceReason.value.length <= 5 || !updateMaintenanceStartingDate.value || !updateMaintenanceEndingDate.value) {
        addNotification("warning", "Vous devez saisir des valeurs valides !", 5);
    } else {
        let updateMaintenanceData = new FormData();
        updateMaintenanceData.set("reason", updateMaintenanceReason.value);
        updateMaintenanceData.set("starting_date", updateMaintenanceStartingDate.value);
        updateMaintenanceData.set("ending_date", updateMaintenanceEndingDate.value);

        newAJAXBasicRequest(
            "POST",
            `https://assets.roomsthegame.com/php/maintenances/update?mid=${id}`,
            (response) => {
                addNotification("success", "Maintenance mise a jour avec succès !", 5);
                removeOverlay();
                window.location.reload();
            },
            updateMaintenanceData
        );
    }
};
