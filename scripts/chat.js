var chatList = [];

//Bootstrap
$(document).ready(function() {
	setInterval(updateChats, 3000);
});

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
												$("#" + chatList[chatListIndex].divId).find(".chatArea").append("<span><b>[Admin]" + chatData.result[x].user + "</b>: " + chatData.result[x].message + "<br /></span>");
											} else {
												$("#" + chatList[chatListIndex].divId).find(".chatArea").append("<span>" + chatData.result[x].user + ": " + chatData.result[x].message + "<br /></span>");
											}
										}
										//Tell the array that we have the newest content
										chatList[chatListIndex].lastId = data.result.id;
										//Scroll down
										$("#" + chatList[chatListIndex].divId).find(".chatArea").scrollTop($("#" + chatList[chatListIndex].divId).find(".chatArea")[0].scrollHeight);
									} else {
										console.log("Something went wrong during fetching the chat data: " + chatData.message);
										error("Det skjedde en feil under hentingen av chat-data: <br />" + chatData.message);
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