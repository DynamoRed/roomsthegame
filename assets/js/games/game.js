function launchGame() {
  removeHub();
  if(game.state != 0) return;
  game.state = 1;

  let leftContainer = document.getElementsByClassName("left-containers")[0];

  let gamePlateContainer = document.createElement("div");
  gamePlateContainer.classList.add("container");
  gamePlateContainer.classList.add("game-plate");
  
  let plateContainer = document.createElement("div");
  plateContainer.classList.add("plate-container");

  let plate = document.createElement("div");
  plate.classList.add("plate");

  let size = Math.round(leftContainer.offsetWidth - 50);
  plate.style.setProperty("width", `${size}px`);
  plate.style.setProperty("height", `${size}px`);
  document.documentElement.style.setProperty('--card-size', `${Math.round(size/5)}px`);

  if (leftContainer.offsetHeight < leftContainer.offsetWidth) {
    size = Math.round(leftContainer.offsetHeight - 200);
    plate.style.setProperty("width", `${size}px`);
    plate.style.setProperty("height", `${size}px`);
    document.documentElement.style.setProperty('--card-size', `${Math.round(size/5)}px`);
  }

  plateContainer.append(plate);
  gamePlateContainer.append(plateContainer);
  leftContainer.append(gamePlateContainer);

  displayPlate(plate);
  displayMembers();

  setInterval(() => {
    displayPlate(plate);
  }, 1000);
}

function removeHub() {
  let gamePlayers = document.getElementsByClassName("game-players")[0];
  let gameSettings = document.getElementsByClassName("game-settings")[0];
  gamePlayers.remove();
  gameSettings.remove();
}

function displayPlate(container) {
  newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/get_plate", (response) => {
    let data = JSON.parse(response);
    if(!data) return;

    for(let card in data){
      let cardDiv = document.getElementById(card);
      if(cardDiv){
        if(cardDiv.getAttribute("card_type") !== data[card]['type']){
          cardDiv.setAttribute("card_type", data[card]['type']);

          while(cardDiv.lastChild) cardDiv.lastChild.remove();
          let plateCardInfos = document.createElement("div");
          plateCardInfos.classList.add("plate-card-infos");
          plateCardInfos.innerHTML = `<h1>${data[card]['name']}</h1>
          <p>${data[card]['description']}</p>`;

          cardDiv.append(plateCardInfos);
        }
      } else {
        let plateCard = document.createElement("div");
        plateCard.classList.add("plate-card");
        if(data[card]['type'] != "not_discovered_room") plateCard.classList.add("active");
        plateCard.id = card;
        plateCard.setAttribute("card_type", data[card]['type']);

        let plateCardInfos = document.createElement("div");
        plateCardInfos.classList.add("plate-card-infos");
        plateCardInfos.innerHTML = `<h1>${data[card]['name']}</h1>
        <p>${data[card]['description']}</p>`;

        plateCard.onclick = () => {
          if(data[card]['type'] == "central_room") return;
          newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/games/discover_card?c=${card}`, (response) => {});
          plateCard.classList.add("active");
          plateCard.onclick = () => {};
        }

        plateCard.append(plateCardInfos);
        container.append(plateCard);
      }
    }
  });
}

function displayMembers() {
  newAJAXBasicRequest(
    "GET",
    "https://assets.roomsthegame.com/php/games/get_members",
    response => {
      let jsonMembers = JSON.parse(response);
      if (!jsonMembers) return;
      let gamePlate = document.getElementsByClassName(
        "game-plate"
      )[0];

      let membersContainer = document.createElement("div");
      membersContainer.classList.add("members-container");

      for (let member in jsonMembers) {
        let memberContainer = document.createElement("div");
        memberContainer.classList.add("member-container");
        const color = ["red", "blue", "green", "yellow"];
        memberContainer.style.setProperty("border-color", color[member]);

        let memberName = document.createElement("a");
        memberName.href = `https://account.roomsthegame.com/view/${jsonMembers[member]["uid"]}`;
        memberName.target = "_BLANK";
        memberName.innerText = jsonMembers[member]["is_requester"]
          ? "Vous"
          : jsonMembers[member]["nick"];

        let memberCharacter = document.createElement("img");
        memberCharacter.src = `https://assets.roomsthegame.com/images/gcomponents/characters/${getCharacterById(jsonMembers[member]["character_id"])["name"]}_Half.svg`;
        memberCharacter.classList.add("player-character");
        memberCharacter.id = `pC${jsonMembers[member]["uid"]}`;

        memberContainer.append(memberName);
        memberContainer.append(memberCharacter);
        membersContainer.append(memberContainer);
      }

      gamePlate.append(membersContainer);
      
    }
  );
}
