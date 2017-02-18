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
    Websocket.sendIntent("subscribeChatroom", [chatId]);

    Websocket.expectIntent("subscribeChatroomResult", function(data) {
	if (data[0]) {
  	    //Find the div for the chatroom
  	    for (var i = 0; i < Chat.chatList.length; i++) {
		if (Chat.chatList[i].chatId == data[2]) {
		    var chatId = Chat.chatList[i].chatId;
		    var divId = Chat.chatList[i].divId;
		    //'tis our div!
		    //Add the text field...

		    $("#" + divId).find(".chatTextfield").html('<input type="text" placeholder="' + (data[1] == 1 ? "Skriv her, trykk enter for å sende" : "Kun clan-chiefs kan skrive her!") + '" class="chatBox" />');

		    //Add enter listener if we can write
		    if (data[1]) {
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

  		    Chat.chatWrite(data[2], "<i>Koblet til chatten</i>");
  		    break; //We don't need to search any more
		}
  	    }
    	} else {
    	    Chat.chatWrite(data[2], "<i>Kunne ikke bli med i chatten: " + data[3] + "</i>");
    	    console.log("Failed to join chat");
    	}
    });
    
    $("#" + divId).html('<div class="chatArea" style="height: ' + (height-25-5-10 - 7 - 5) + 'px;"></div><div class="chatTextfield" style="padding-right:27px;padding-left:0px;margin-right:0px;"></div>');
};

Chat.unbindChat = function(divId) {
    for(var i = 0; i < Chat.chatList.length; i++) {
	if(Chat.chatList[i].divId == divId) {
	    //Chat.sendMessage({intent: "unsubscribeChatroom", data: [this.chatList[i].chatId]});
	    Websocket.sendIntent("unsubscribeChatroom", [Chat.chatList[i].chatId]);
	    Chat.chatList.splice(i, 1);
	}
    }
}

Chat.sendMsg = function(chatId, msg) {
    Websocket.sendIntent("chatMessage", [chatId, encodeURIComponent(msg)]);
    Websocket.expectIntent("chatMessageResult", function(data){
	if (data[0]) {
    	    Chat.chatWrite(data[1], data[2]);
    	} else {
    	    console.log("Got an error when sending chat message to channel " + data[1] + ": " + data[2]);
    	}
    });
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

Chat.init = function() {
    console.log("Initializing chat");
    if(!Websocket.isConnected() && !Websocket.isConnecting()) {
	Websocket.connect(Websocket.getDefaultConnectUrl());
	Websocket.onOpen = function() {
	    Websocket.authenticate();
	};
    }
    Websocket.addHandler("chat", function(data) {
	Chat.chatWrite(data[0], data[1]);
    });
    
};
