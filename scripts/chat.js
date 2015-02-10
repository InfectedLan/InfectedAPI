var chatList = [];

//Bootstrap
$(document).ready(function() {
	setInterval(updateChats, 1000);
});

function createChat(divId, chatId, height) {
	chatList.push({"divId": divId, "chatId": chatId, "lastId": -1});
	console.log("Created chat id " + chatId + " for divid " + divId + " with height " + height);
	$("#" + divId).html('<div class="chatArea" height="' + (height-40) + '"></div><input type="text" class="chatBox" />');
	//Listen to enter key
	$("#" + divId).find('.chatBox').keypress({chat: chatId, div: divId}, function(e) {
	    if(e.which == 13) {
	        sendChat(e.data.chat, $(this).val());
	        $(this).val("");
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
			$.getJSON('../api/json/chat/getLastChatMessage.php?id=' + chatList[i].chatId, function(data){ 
				if(data.result != false) { //result is an object if successfull
					if(data.result.id != chatList[i]) {
						//Download chat data
						$.getJSON('../api/json/chat/getLastChatMessages.php?id=' + chatList[i].chatId + '&count=500', function(chatData){ 
							if(chatData.result != false) {
								for(var x = chatData.result.length-1; x >= 0; x--) {
									if(chatData.result[x].admin) {
										$("#" + chatList[i].divId).find(".chatArea").append("<p><b>[Admin]" + chatData.result[x].user + "</b>: " + chatData.result[x].message + "</p>");
									} else {
										$("#" + chatList[i].divId).find(".chatArea").append("<p>" + chatData.result[x].user + ": " + chatData.result[x].message + "</p>");
									}
								}
								//Tell the array that we have the newest content
								chatList[i].lastId = data.result.id;
							} else {
								console.log("Something went wrong during fetching the chat data: " + chatData.message);
								error("Det skjedde en feil under hentingen av chat-data: <br />" + chatData.message);
							}
						});
					} else {
						//All ok, chat is updated
					}
				} else {
					console.log("Something went wrong during fetching the chat: " + data.message);
					error(data.message);
				}
			});
		}
	}
}