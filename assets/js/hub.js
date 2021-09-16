function createParty(){
    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/create", (response) => {
        console.log(response);
        let res = JSON.parse(response);
        if(!res) return;
        if(res['state'] != 'success'){
            window.location = `https://play.roomsthegame.com/?e=${res['details']}`;
            return;
        }
        window.location = `https://play.roomsthegame.com/game/${res['pcode']}`;
    });
    generateParties()
}

function generateParties() {
    newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/games/get_all", (response) => {
        let partiesJson = JSON.parse(response);
        if(!partiesJson) return;

        let partiesContainer = document.getElementsByClassName("parties-container")[0];

        for(let party in partiesJson) {
            if(partiesJson[party]["ended"] || 
              document.getElementById(`party${partiesJson[party]["pcode"]}`) ||
              partiesJson[party]["private"] == "1") continue;
            
            let partyContainer = document.createElement("div");
            partyContainer.classList.add("party-container");
            partyContainer.id = `party${partiesJson[party]["pcode"]}`;
            
            let partyOwner = document.createElement("div");
            partyOwner.classList.add("owner");
            partyOwner.innerHTML = "Partie de :";

            let partyOwnerName = document.createElement("a");
            partyOwnerName.href = `https://account.roomsthegame.com/view/${partiesJson[party]["manager_uid"]}`;
            partyOwnerName.target = "_blank";
            partyOwnerName.innerHTML = partiesJson[party]["manager_nick"];

            let partyOwnerAvatar = document.createElement("img");
            partyOwnerAvatar.src = partiesJson[party]["manager_pic"];

            let joinButton = document.createElement("a");
            joinButton.classList.add("btn-primary");
            joinButton.classList.add("btn-join");
            joinButton.href = `https://play.roomsthegame.com/game/${partiesJson[party]["pcode"]}`;
            joinButton.innerHTML = 'Rejoindre'

            let countMember = document.createElement("span");
            countMember.innerHTML = `${partiesJson[party]["count_members"]}/${partiesJson[party]["max_members"]}`;

            partyOwner.append(partyOwnerName);
            partyOwner.append(partyOwnerAvatar);
            partyContainer.append(partyOwner);
            partyContainer.append(countMember);
            partyContainer.append(joinButton);
            partiesContainer.append(partyContainer);
        }
    });
}

generateParties()