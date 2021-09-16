game = {};
game.characters = {};
game.state = 0;
game.characters.skins = [];

window.onload = () => {
    game.pcode = window.location.pathname.replace("/game/", "");
    document.addEventListener("click", () => {
        new Audio("https://assets.roomsthegame.com/sounds/click.mp3").play();
    }, true);

    const msgInput = document.getElementById("msg-input");
    msgInput.focus();

    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/get_characters", (response) => {
        if(!JSON.parse(response)) return;
        game.characters.skins = JSON.parse(response);

        newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/games/add_member?pcode=${game.pcode}`, (response) => {
            console.log(response);
            let container = document.getElementById("membersContainer");  
            game.showMembers(container);
            let intSM = setInterval(() => { 
                if(game.state != 0){ window.clearInterval(intSM); return; }
                game.showMembers(container) 
            }, 3000);
        });
    });    
}

game.showMembers = membersContainer => {
    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/get_members", (response) => {
        let jsonMembers = JSON.parse(response);
        if (!jsonMembers) return;

        for(let memberCont in membersContainer){
            if(!memberCont.id) continue;
            let isFind = false;
            for(let member in jsonMembers){
                if(jsonMembers[member]['uid'] === memberCont.id) isFind = true;
            }

            if(!isFind){
                memberCont.remove();
                new Audio("https://assets.roomsthegame.com/sounds/member_disappear.mp3").play();
            }
        } 

        for(let member in jsonMembers){
            if(!document.getElementById(jsonMembers[member]['uid'])){
                new Audio("https://assets.roomsthegame.com/sounds/member_appear.mp3").play();
                let playerContainer = document.createElement("div");
                playerContainer.classList.add("player-container");
                playerContainer.id = jsonMembers[member]['uid'];

                let playerContainerTop = document.createElement("div");
                playerContainerTop.classList.add("player-container-top");

                if(jsonMembers[member]['is_manager']){
                    let playerManagerIcon = document.createElement("img");
                    playerManagerIcon.classList.add("manager-icon");
                    playerManagerIcon.title = "Administrateur de la partie";
                    playerManagerIcon.src = "https://assets.roomsthegame.com/images/icons/manager.svg";
                    playerContainerTop.append(playerManagerIcon);
                }

                let playerAvatar = document.createElement("img");
                playerAvatar.classList.add("player-avatar");
                playerAvatar.src = jsonMembers[member]['avatar'];
                playerContainerTop.append(playerAvatar);

                let playerInfos = document.createElement("div");
                playerInfos.classList.add("player-infos");
                let playerName = document.createElement("a");
                playerName.href = `https://account.roomsthegame.com/view/${jsonMembers[member]['uid']}`;
                playerName.target = "_BLANK";
                playerName.innerText = jsonMembers[member]['is_requester'] ? "Vous" : jsonMembers[member]['nick'];
                let playerXP = document.createElement("span");
                playerXP.innerText = "500 xp";
                playerInfos.append(playerName);
                playerInfos.append(playerXP);
                
                playerContainerTop.append(playerInfos);

                playerContainer.append(playerContainerTop);

                let playerContainerMiddle = document.createElement("div");
                playerContainerMiddle.classList.add("player-container-middle");

                if(jsonMembers[member]['is_requester']){
                    let playerPreviousCharacterButton = document.createElement("a");
                    playerPreviousCharacterButton.onclick = () => { game.characters.change(jsonMembers[member]['uid'], "previous"); };
                    let previousArrow = document.createElement("img");
                    previousArrow.src = "https://assets.roomsthegame.com/images/icons/arrow_back.svg";
                    playerPreviousCharacterButton.append(previousArrow);
                    playerContainerMiddle.append(playerPreviousCharacterButton);
                    
                    let playerNextCharacterButton = document.createElement("a");
                    playerNextCharacterButton.onclick = () => { game.characters.change(jsonMembers[member]['uid'], "next"); };
                    let nextArrow = document.createElement("img");
                    nextArrow.src = "https://assets.roomsthegame.com/images/icons/arrow_next.svg";
                    playerNextCharacterButton.append(nextArrow);
                    playerContainerMiddle.append(playerNextCharacterButton);
                }

                playerContainer.append(playerContainerMiddle);

                let playerCharacter = document.createElement("img");
                playerCharacter.classList.add("player-character");
                playerCharacter.id = `pC${jsonMembers[member]['uid']}`;
                playerCharacter.setAttribute('cId', jsonMembers[member]['character_id']);
                playerCharacter.src = `https://assets.roomsthegame.com/images/gcomponents/characters/${getCharacterById(jsonMembers[member]['character_id'])['name']}_Half.svg`;
                playerContainer.append(playerCharacter);

                membersContainer.append(playerContainer);
            } else if(!jsonMembers[member]['is_requester']) {
                let character = document.getElementById(`pC${jsonMembers[member]['uid']}`);
                if(!character || !character.getAttribute('cId')) return;
                if(character.getAttribute('cId') !== jsonMembers[member]['character_id']){
                    character.setAttribute('cId', jsonMembers[member]['character_id']);
                    character.src = `https://assets.roomsthegame.com/images/gcomponents/characters/${getCharacterById(jsonMembers[member]['character_id'])['name']}_Half.svg`;
                }
            }
        }
    });
}

game.characters.change = (uid, step) => {
    let character = document.getElementById(`pC${uid}`);
    if(!character || !character.getAttribute('cId')) return;
    let cAttrNumber = character.getAttribute('cId');
    let cNumber = getCharacterPos(cAttrNumber);
    step === "previous" ? cNumber--: cNumber++;
    if(cNumber >= game.characters.skins.length) cNumber = 0;
    else if(cNumber < 0) cNumber = game.characters.skins.length-1;
    cAttrNumber = game.characters.skins[cNumber]['id'];
    character.setAttribute('cId', cAttrNumber);
    character.src = `https://assets.roomsthegame.com/images/gcomponents/characters/${game.characters.skins[cNumber]['name']}_Half.svg`;
    newAJAXBasicRequest("GET", `https://assets.roomsthegame.com/php/games/set_character?character_id=${parseInt(cAttrNumber)}`, (response) => {});
}

window.addEventListener('beforeunload', e => {
    e.preventDefault();
    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/remove_member", (response) => {console.log(response)});
    e.returnValue = '';
});

function getCharacterById(id){
    for(let skin in game.characters.skins){
        if(game.characters.skins[skin]['id'] === id) return game.characters.skins[skin];
    }
}

function getCharacterPos(id){
    for(let i = 0; i < game.characters.skins.length; i++){
        if(game.characters.skins[i]['id'] === id) return i;
    }
}