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
var Chat = Chat || {};

/*
 * Chat api overview
 *
 * bindChat(divId, chatId, height) - binds chat to a div. Notice that you have to specify the height in pixels.
 * sendMessage(message) - sends a message
 */

//Private variables
Chat.chatList = [];
Chat.exitError = "";
Chat.connected = false;
Chat.sendQueue = [];

// Public functions
Chat.bindChat = function(divId, chatId, height) {
  for (var i = 0; i < this.chatList.length; i++) {
    if (this.chatList[i].divId == divId) {
  	  console.log("WARNING: Trying to bind a div twice");

      return;
	  }
  }

  this.chatList.push({divId: divId, chatId: chatId});
  this.sendMessage( { intent: "subscribeChatroom", data: [ chatId ] } );

  $("#" + divId).html('<div class="chatArea" style="height: ' + (height-25-5-10 - 7 - 5) + 'px;"></div><div class="chatTextfield" style="padding-right:27px;padding-left:0px;margin-right:0px;"></div>');
};

Chat.unbindChat = function(divId) {
    for(var i = 0; i < this.chatList.length; i++) {
	if(this.chatList[i].divId == divId) {
	    Chat.sendMessage({intent: "unsubscribeChatroom", data: [this.chatList[i].chatId]});
	    this.chatList.splice(i, 1);
	}
    }
}

Chat.sendMessage = function(message) {
  if (!this.connected && message.intent != "auth") {
    this.sendQueue.push(message);
  } else {
	  this.socket.send(JSON.stringify(message)); // Yes, you ARE allowed to mention a variable befire it is created, as long as it isn't called before it is initialized!
  }
};

Chat.onSocketOpen = function(msg) {
  console.log("Socket open");
  Chat.sendMessage({intent: "auth", "data": [getCookie("PHPSESSID")]});
};

Chat.onRecvMessage = function(msg) {
  console.log("Recieved:");
  console.log(msg);

  var packet = JSON.parse(msg.data);

  switch(packet.intent) {
    case "authResult":
	    if (packet.data[0]) {
	      console.log("Successfully authenticated");
	      Chat.connected = true;

        for(var i = 0; i < Chat.sendQueue.length; i++) {
		      Chat.sendMessage(Chat.sendQueue[i]);
	      }

	      Chat.sendQueue = [];
	    } else {
	      error("Vi fikk ikke logget inn på chatserveren! Prøv å trykke F5, og prøv på nytt");
	      socket.close();
	    }
	    break;

    case "subscribeChatroomResult":
	    if (packet.data[0]) {
  	    //Find the div for the chatroom
  	    for (var i = 0; i < Chat.chatList.length; i++) {
		      if (Chat.chatList[i].chatId == packet.data[2]) {
		        var chatId = Chat.chatList[i].chatId;
		        var divId = Chat.chatList[i].divId;
		        //'tis our div!
		        //Add the text field...

            $("#" + divId).find(".chatTextfield").html('<input type="text" placeholder="' + (packet.data[1] == 1 ? "Skriv her, trykk enter for å sende" : "Kun clan-chiefs kan skrive her!") + '" class="chatBox" />');

            //Add enter listener if we can write
            if (packet.data[1]) {
	            $("#" + divId).find('.chatBox').keypress({chat: chatId, div: divId}, function(e) {
                if (e.which == 13) {
    				      if ($(this).val().length > 0) {
  		              Chat.sendMsg(e.data.chat, $(this).val());
  		              $(this).val("");
  		            } else {
  		              error("Chatmeldingen er for kort!");
  		            }
  	            }
	            });
		        }

  		      Chat.chatWrite(packet.data[2], "<i>Koblet til chatten</i>");
  		      break; //We don't need to search any more
          }
  	    }
    	} else {
    	    Chat.chatWrite(packet.data[2], "<i>Kunne ikke bli med i chatten: " + packet.data[3] + "</i>");
    	    console.log("Failed to join chat");
    	}
  	  break;

    case "chat":
	    Chat.chatWrite(packet.data[0], packet.data[1]);
	    break;

    case "chatMessageResult":
    	if (packet.data[0]) {
    	    Chat.chatWrite(packet.data[1], packet.data[2]);
    	} else {
    	    console.log("Got an error when sending chat message to channel " + packet.data[1] + ": " + packet.data[2]);
    	}
	    break;

    case "default":
	    console.log("ERROR: Unsupported intent: " + packet.intent);
	    break;
    }
};

Chat.sendMsg = function(chatId, msg) {
  this.sendMessage({"intent": "chatMessage", data: [chatId, msg]});
};

Chat.getChatDiv = function(chatId) {
  for (var i = 0; i < Chat.chatList.length; i++) {
  	if (Chat.chatList[i].chatId == chatId) {
	    if ($("#" + Chat.chatList[i].divId).length == 0) {
		    console.log("Chat " + Chat.chatList[i].chatId + " at divId " + Chat.chatList[i].divId + " is gone! Removing");
		    Chat.chatList.splice(i, 1);
		    i--;

        return null;
	    }

      return Chat.chatList[i].divId;
  	}
  }

  return null;
};

Chat.chatWrite = function(chatId, msg) {
  var chatDivId = this.getChatDiv(chatId);

  if (chatDivId != null) {
	  // Write message to bottom
	  $("#" + chatDivId).find(".chatArea").append("<div>" + msg + "</div>");
	  //Scroll down
	  $("#" + chatDivId).find(".chatArea").scrollTop($("#" + chatDivId).find(".chatArea")[0].scrollHeight);
  }
};

Chat.onClose = function(msg) {
  console.log("Disconnected");

  if (this.exitError == "") {
	  error("Vi mistet tilkobling til serveren. Vennligst oppdater siden for å prøve å koble til på nytt.");
  } else {
	  error(this.exitError);
  }
};

Chat.init = function() {
  console.log("Initializing");

  var url = window.location.href;
  //Autodetect magic: Let's convert http to ws, and https to wss
  url = url.replace("http://", "ws://");
  url = url.replace("https://", "wss://");
  //Now, let's remove the other part of the URL we don't need
  url = url.substr(0, url.indexOf("/", 6)) + "/websocket";

  console.log("Connecting to " + url);

  this.socket = new WebSocket(url);
  this.socket.onopen = this.onSocketOpen;
  this.socket.onmessage = this.onRecvMessage;
  this.socket.onclose = this.onClose;

  console.log("Init done!");
};

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');

  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];

    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }

    if (c.indexOf(name) == 0) {
      return c.substring(name.length,c.length);
    }
  }

  return "";
};

/*
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
*/
