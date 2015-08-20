/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//JoMS suggested this as OOP javascript style. Ty JoMs <3
var Chat = function(){
    
};
//This is apparently how you add functions...
Chat.prototype.init = function() {
    //Set up private variables
    function onSocketOpen(msg) {
	console.log("Socket open");
	this.sendMessage({intent: "auth", "data": [getCookie("PHPSESSID")]});
    }

    function onRecvMessage(msg) {
	console.log("Recieved:");
	console.log(msg);
	var packet = JSON.parse(msg.data);
	switch(packet.intent) {
	case "authResult":
	    if(packet.data[0]) {
		console.log("Successfully authenticated");
	    } else {
		error("Vi fikk ikke logget inn på chatserveren! Prøv å trykke F5, og prøv på nytt");
	    }
	    break;
	case "default":
	    console.log("ERROR: Unsupported intent: " + packet.intent);
	    break;
	}
    }

    function onClose(msg) {
	console.log("Disconnected");
	error("Vi mistet tilkobling til serveren. Vennligst oppdater siden for å prøve å koble til på nytt.");
    }
    var chatList = [];

    this.bindChat = function(divId, chatId) {
	for(var i = 0; i < chatList.length; i++) {
	    if(chatList[i].divId == divId) {
		console.log("WARNING: Trying to bind a div twice");
		return;
	    }
	}
	chatList.push({divId: divId, chatId: chatId});

	this.sendMessage({intent: "subscribeChatroom", data: [chatId]});
    }

    this.sendMessage = function(message) {
	socket.send(JSON.stringify(message)); //Yes, you ARE allowed to mention a variable befire it is created, as long as it isn't called before it is initialized!
    }
	
    var url = window.location.href;
    //Autodetect magic: Let's convert http to ws, and https to wss
    url = url.replace("http://", "ws://");
    url = url.replace("https://", "wss://");
    //Now, let's remove the other part of the URL we don't need
    url = url.substr(0, url.indexOf("/", 6)) + ":1337/";
    console.log("Connecting to " + url);
    this.socket = new WebSocket(url);
    this.socket.onopen = onSocketOpen;
    this.socket.onmessage = onRecvMessage;
    this.socket.onclose = onClose;
    
};

function socketSend(msg) {
	try {
		socket.send(msg);
	} catch(ex) {
		log(ex);
	}
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function createChat(divId, chatId, height) {
	for(var i = 0; i < chatList.length; i++) {
		if(chatList[i].divId == divId) {
			console.log("Div id " + divId + " allready exists. Deleting older occurance of it");
			chatList.splice(i, 1);
		}
	}
	chatList.push({"divId": divId, "chatId": chatId, "lastId": -1});
	console.log("Created chat id " + chatId + " for divid " + divId + " with height " + height);

	$.getJSON('../api/json/chat/isInChat.php?id=' + chatId, function(data) {
		if(data.result != false) {
			$("#" + divId).html('<div class="chatArea" style="height: ' + (height-25-5-10 - 7 - 5) + 'px;"></div><div style="padding-right:27px;padding-left:0px;margin-right:0px;"><input type="text" placeholder="' + (data.result.response ? "Skriv her, trykk enter for å sende" : "Kun clan-chiefs kan skrive her!") + '" class="chatBox" /></div>');
			//Listen to enter key
			if(data.result.response == true) {
				$("#" + divId).find('.chatBox').keypress({chat: chatId, div: divId}, function(e) {
					if(e.which == 13) {
						if($(this).val().length > 0) {
							sendChat(e.data.chat, $(this).val());
							$(this).val("");
						} else {
							error("Chatmeldingen er for kort!");
						}
					}
				});
			}
		} else {
			error(data.message);
		}
	});
}
function sendChat(chatId, message) {
	$.getJSON('../api/json/chat/sendMessage.php?id=' + chatId + '&message=' + encodeURIComponent(message), function(data){ 
		if(data.result != false) {
			//success!
			updateChats();
		} else {
			error(data.message);
			console.log("Something went wrong while sending a chat message: " + data.message);
		}
	});
}

function updateChats() {
	for(var i = 0; i < chatList.length; i++) {
		if($("#" + chatList[i].divId).length == 0) {
			console.log("Chat " + chatList[i].chatId + " at divId " + chatList[i].divId + " is gone! Removing");
			chatList.splice(i, 1);
			i--;
		} else {
			//Download new data
			$.getJSON('../api/json/chat/getLastChatMessage.php?id=' + chatList[i].chatId, (function() {
				var chatListIndex = i;
				return function(data){ 
					if(data.result != false) { //result is an object if successfull
						if(data.result.id != chatList[chatListIndex].lastId) {
							//Download chat data
							$.getJSON('../api/json/chat/getLastChatMessages.php?id=' + chatList[chatListIndex].chatId + '&count=500', (function () {

								return function(chatData){ 
									if(chatData.result != false) {
										$("#" + chatList[chatListIndex].divId).find(".chatArea").html("");
										for(var x = chatData.result.length-1; x >= 0; x--) {
											if(chatData.result[x].admin) {
												$("#" + chatList[chatListIndex].divId).find(".chatArea").append("<span>[" + chatData.result[x].time + "]<b>[Admin]" + chatData.result[x].user + "</b>: " + chatData.result[x].message + "<br></span>");
											} else {
												$("#" + chatList[chatListIndex].divId).find(".chatArea").append("<span>[" + chatData.result[x].time + "]" + chatData.result[x].user + ": " + chatData.result[x].message + "<br></span>");
											}
										}
										//Tell the array that we have the newest content
										chatList[chatListIndex].lastId = data.result.id;
										//Scroll down
										$("#" + chatList[chatListIndex].divId).find(".chatArea").scrollTop($("#" + chatList[chatListIndex].divId).find(".chatArea")[0].scrollHeight);
									} else {
										console.log("Something went wrong during fetching the chat data: " + chatData.message);
										error("Det skjedde en feil under hentingen av chat-data: <br>" + chatData.message);
									}
								};
							}) ());
						} else {
							//All ok, chat is updated
						}
					} else {
						console.log("Something went wrong during fetching the chat: " + data.message);
						error(data.message);
					}
				};
			}) () );
		}
	}
}
