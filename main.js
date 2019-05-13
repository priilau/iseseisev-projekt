/*jshint esversion:6*/

let friendlist = document.querySelector("#friend-list");
let chatbox = document.querySelector("#chatbox");
let inputbox = document.querySelector("#inputbox");
let userlist = document.querySelector("#user-list");

document.getElementById('msg-form').addEventListener('submit', function(e) {
    e.preventDefault();
});

let friends = [];
let testFriends = [];
let messages = [];
let testMessages = [];
let users = [];
let testUsers = [];
let chatInterval;
let flistInterval;
let ulistInterval;
let friendCheck = false;

let friendTbody = document.createElement("tbody");

function RenderFriend(id, email){
	//HideFriends();
	let fContainer = document.createElement("div");
	fContainer.id = "remove-friend";
	let fEmailBlock = document.createElement("div");
	fEmailBlock.classList.add("friend");
	//fEmailBlock.classList.add("active");
	fEmailBlock.id = "friend-" + id;
	let fEmailLabel = document.createElement("span");
	fEmailLabel.innerText = email;

	fEmailLabel.addEventListener("click", function(){
		clearInterval(chatInterval);
		HideFriends();
		fEmailBlock.classList.add("active");
		fEmailLabel.classList.add("active");
		UpdateChat(id);
	});
	let removeFriendBtn = document.createElement("input");
	removeFriendBtn.type = "button";
	removeFriendBtn.id = "button";
	removeFriendBtn.value = "Remove friend";
	removeFriendBtn.addEventListener("click", function(){
		clearInterval(chatInterval);
		clearInterval(flistInterval);
		for(let i = 0; i < friends.length; i++){
			if(friends[i].user_id == id){
				DeleteAllChatDOMs();
				//console.log(friends[i].user_id + " & " + id);
			}
		}
		DeleteAllFriendDOMs();
		DeleteAllUserDOMs();
		RemoveFromFriends(id);
		UpdateLists();
	});
	fEmailBlock.appendChild(fEmailLabel);
	fEmailBlock.appendChild(removeFriendBtn);
	fContainer.appendChild(fEmailBlock);
	friendlist.appendChild(fContainer);
}

function RenderUser(id, email){
	let uEmailContainer = document.createElement("div");
	uEmailContainer.id = "add-user";
	let uEmail = document.createElement("div");
	uEmail.id = "user-" + id;
	uEmail.innerText = email;
	let uAddBtn = document.createElement("input");
	uAddBtn.type = "button";
	uAddBtn.id = "button";
	uAddBtn.value = "Add to friends";
	uAddBtn.addEventListener("click", function(){
		clearInterval(flistInterval);
		for(let i = 0; i < friends.length; i++){
			if(friends[i].user_id == id){
				friendCheck = true;
			}
		}
		if(friendCheck){
			alert("This user is already in your friend list!");  // ei suutnud SQL käsku välja mõelda, lahendasin probleemi alert'i kasutades
		} else {
			AddToFriends(id);
			DeleteAllFriendDOMs();
			DeleteAllUserDOMs();
			UpdateLists();
			friendCheck = false;
		}
	});
	uEmailContainer.appendChild(uEmail);
	uEmailContainer.appendChild(uAddBtn);
	userlist.appendChild(uEmailContainer);
}

function CreateMessage(message, recieverId, senderId/*, timestamp*/){
	let fMessageContainer = document.createElement("div");
	fMessageContainer.id = "message";
	let fMessage = document.createElement("div");
	//let fMsgTimestamp = document.createElement("div");
	//fMsgTimestamp.id = "timestamp";
	if(userID == recieverId){
		fMessage.id = "recieved-msg";
    } else if(userID == senderId){
        fMessage.id = "sent-msg";
	}
	let fMsgSpan = document.createElement("span");
	fMsgSpan.id = "msg";
	fMsgSpan.innerHTML = message;
	fMessage.appendChild(fMsgSpan);
	//fMsgTimestamp.innerHTML = timestamp;
	fMessageContainer.appendChild(fMessage);
	//fMessageContainer.appendChild(fMsgTimestamp);
	chatbox.appendChild(fMessageContainer);
	chatbox.scrollTop = chatbox.scrollHeight;
}

function CreateMsgInputBox(){
	let inputContainer = document.createElement("div");
	inputContainer.id = "textarea";
	let btnContainer = document.createElement("div");
	btnContainer.id = "input-btns";
	let input = document.createElement("textarea");
	input.maxLength = "256";
	input.placeholder = "Write a message(max 256 characters)...";
	input.id = "input";
	/*
	let sendImgBtn = document.createElement("input");
	sendImgBtn.type = "button";
	sendImgBtn.id = "chat-btn";
	sendImgBtn.value = "Send an image";
	sendImgBtn.addEventListener("click", function(){
		//SendMessage();
	});
	*/
	let sendMsgBtn = document.createElement("input");
	sendMsgBtn.type = "submit";
	sendMsgBtn.id = "big-btn";
	sendMsgBtn.value = "Send message";
	sendMsgBtn.addEventListener("click", function(){
		if(input.value != ""){
			intervalCheck = false;
			clearInterval(chatInterval);
			let activeFriend = document.querySelectorAll(".friend.active").item(0);
			let friendID = activeFriend.id.slice(7, activeFriend.length);
			SendMessage(input.value, friendID);
			input.value = "";
			UpdateChat(friendID);
		}
	});
	inputContainer.appendChild(input);
	//btnContainer.appendChild(sendImgBtn);
	btnContainer.appendChild(sendMsgBtn);
	inputbox.appendChild(inputContainer);
	inputbox.appendChild(btnContainer);
}

function HideFriends() {
	let frnds = document.querySelectorAll(".friend");
	//console.log(frnds);
	for(let i = 0, frnd; frnd = frnds[i]; i++) {
		if(frnd.classList.contains("active")) {
			frnd.classList.remove("active");
		}
	}

	let tabs = document.querySelectorAll("#remove-friend span");
	//console.log(tabs);
	for(let i = 0, tab; tab = tabs[i]; i++) {
		if(tab.classList.contains("active")) {
			tab.classList.remove("active");
		}
	}
}

function UpdateChat(id){
	DeleteAllChatDOMs();
	GetMessages(id);
	GetTestMessages(id);
	chatInterval = setInterval(function(){
		//console.log(testMessages);
		GetTestMessages(id);
		//console.log("tick");
		if(messages.length != testMessages.length){
			//console.log(intervalCheck);
			DeleteAllChatDOMs();
			GetMessages(id);
		}
	}, 1000);		
}

function UpdateLists(){
	GetUsers();
	GetFriends();
	GetTestFriends();
	flistInterval = setInterval(function(){
		GetTestFriends();
		if(friends.length != testFriends.length){
			//console.log(intervalCheck);
			//console.log(friends);
			DeleteAllUserDOMs();
			GetUsers();
			DeleteAllFriendDOMs();
			GetFriends();
		}
	}, 1000);
	ulistInterval = setInterval(function(){
		GetTestUsers();
		if(users.length != testUsers.length){
			//console.log(intervalCheck);
			//console.log(friends);
			DeleteAllUserDOMs();
			GetUsers();
		}
	}, 1000);			
}


function DeleteAllChatDOMs() {
	let msgs = document.querySelectorAll("#message");
	for(let i = 0, m; m = msgs[i]; i++) {
		//console.log(m);
		m.parentElement.removeChild(m);
	}
}

function DeleteAllFriendDOMs() {
	let frnds = document.querySelectorAll("#remove-friend");
	for(let i = 0, f; f = frnds[i]; i++) {
		//console.log(m);
		f.parentElement.removeChild(f);
	}
}

function DeleteAllUserDOMs() {
	let usrs = document.querySelectorAll("#add-user");
	for(let i = 0, u; u = usrs[i]; i++) {
		//console.log(m);
		u.parentElement.removeChild(u);
	}
}
/*
function DeleteFriendListDOM(id) {
	let e = document.querySelector("#friend-" + id);
	e.parentElement.remove(e);
}

function DeleteUserListDOM(id) {
	let e = document.querySelector("#user-" + id);
	e.parentElement.remove(e);
}
*/
function SendMessage(msg, friendID){
	let formData = new FormData();
	formData.append("actionP", "SendMessage");
	formData.append("message", msg);
	formData.append("friendID", friendID);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", "functions.php");
	xhttp.send(formData);
}


function AddToFriends(friendID){
	let formData = new FormData();
	formData.append("actionP", "AddFriend");
	formData.append("friendID", friendID);

	let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "functions.php");
	xhttp.send(formData);
}

function RemoveFromFriends(friendID){
	let formData = new FormData();
	formData.append("actionP", "RemoveFriend");
	formData.append("friendID", friendID);

	let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "functions.php");
	xhttp.send(formData);
}

function GetTestMessages(friendID) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			testMessages = [];
			for(let i in data) {
				testMessages[i] = data[i];
			}
        }
	};
    xhttp.open("GET", "functions.php?actionG=GetMessages&friendID=" + friendID);
	xhttp.send();
}

function GetMessages(friendID) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			messages = [];
			for(let i in data) {
				messages[i] = data[i];
				CreateMessage(data[i].message, data[i].recieverId, data[i].senderId/*, data[i].timestamp*/);
			}
        }
	};
    xhttp.open("GET", "functions.php?actionG=GetMessages&friendID=" + friendID);
	xhttp.send();
}
function GetTestFriends() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			testFriends = [];
			for(let i in data) {
				testFriends[i] = data[i];
			}
        }
	};
    xhttp.open("GET", "functions.php?actionG=GetFriends");
	xhttp.send();
}

function GetFriends() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			friends = [];
			for(let i in data) {
				friends[i] = data[i];
				//console.log(friends[i]);
				RenderFriend(data[i].user_id, data[i].email);
			}
        }
    };
    xhttp.open("GET", "functions.php?actionG=GetFriends");
	xhttp.send();
}

function GetTestUsers() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			testUsers = [];
			for(let i in data) {
				testUsers[i] = data[i];
			}
        }
	};
    xhttp.open("GET", "functions.php?actionG=GetUsers");
	xhttp.send();
}

function GetUsers() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
			let data = JSON.parse(xhttp.responseText);
			users = [];
			for(let i in data) {
				users[i] = data[i];
				RenderUser(data[i].user_id, data[i].email);
			}
        }
    };
    xhttp.open("GET", "functions.php?actionG=GetUsers");
	xhttp.send();
}

(function(){
	UpdateLists();
	CreateMsgInputBox();
})();