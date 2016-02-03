/**
 * This file is part of InfectedCompo.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */
//Clojures are sexy
Websocket = (function() {
    var wsObject = {};
    //Private variables
    var connected = false;
    var authenticated = false;
    var socket = null;
    var listeners = {};
    var expectedIntents = {};

    var packetQueue = []; 

    var customOpenHandler = null;
    var customCloseHandler = null;
    //Public variables
    

    //Private functions
    var onOpen = function() {
	console.log("WebSocket connected");
	if(customOpenHandler != null) {
	    customOpenHandler();
	}
    };
    var onClose = function() {
	console.log("WebSocket disconnected");
	if(customCloseHandler != null) {
	    customCloseHandler();
	}
    };
    var onMessage = function(msg) {
	var packet = JSON.parse(msg.data);
	if(typeof(packet.intent) !== "undefined" && typeof(packet.data) !== "undefined") {
	    var wasHandler = false;
	    if(typeof(listeners[packet.intent]) !== "undefined" && listeners[packet.intent].length > 0) {
		for(var i = 0; i < listeners[packet.intent].length; i++) {
		    listeners[packet.intent][i](packet.data);
		}
		wasHandled = true;
	    }
	    if(typeof(expectedIntents[packet.intent]) !== "undefined" && expectedIntents[packet.intent].length > 0) {
		var exp = expectedIntents[packet.intent].shift();
		exp(packet.data);
		wasHandled = true;
	    }

	    if(!wasHandled) {
		error("Vi fikk en pakke vi ikke vet hvordan vi skal håndtere!");
	    }
	} else {
	    error("Vi mottok en feil-pakke!");
	}
    };
    var sendPacket = function(packet) {
	if(connected) {
	    socket.send(JSON.stringify(packet));
	} else {
	    console.log("Tried to send packet without a connection:");
	    console.log(packet);
	}
    };

    var getCookie = function(cname) {
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
    //Public functions
    wsObject.isConnected = function() {
	return connected;
    };
    wsObject.isAuthenticated = function() {
	return authenticated;
    };
    wsObject.getDefaultConnectUrl = function() {
	var url = window.location.href;
	//Autodetect magic: Let's convert http to ws, and https to wss
	url = url.replace("http://", "ws://");
	url = url.replace("https://", "wss://");
	//Now, let's remove the other part of the URL we don't need
	url = url.substr(0, url.indexOf("/", 6)) + "/websocket";
	return url;
    };
    wsObject.connect = function(url) {
	url = (typeof(url) === "undefined" ? this.getDefaultConnectUrl() : url);
	
	Console.log("WebSocket connecting to " + url);
	socket = new WebSocket(url);
	socket.onopen = onOpen;
	socket.onmessage = onMessage;
	socket.onclose = onClose;
    };
    wsObject.addHandler = function(intent, handler) {
	if(typeof(listeners[intent]) === "undefined") {
	    listeners[intent] = [handler];
	} else {
	    listeners[intent].push(handler);
	}
    };
    wsObject.sendIntent = function(intent, data) {
	if(!connected && intent != "auth") {
	    packetQueue.push({intent: intent, data: data});
	} else {
	    sendPacket({intent: intent, data: data});
	}
    };
    wsObject.expectIntent = function(intent, handler) {
	if(typeof(expectedIntents[intent]) === "undefined") {
	    expectedIntents[intent] = [handler];
	} else {
	    expectedIntents[intent].push(handler);
	}
    };

    wsObject.authenticate = function(sessId) {
	sessId = (typeof(sessId) !== "undefined" ? sessId : getCookie("PHPSESSID"));
	this.sendIntent("auth", [sessId]);
    };

    //"Constructor stuff"
    wsObject.addHandler("authResult", function(data) {
	if(data[0]) {
	    authenticated = true;
	    for(var i = 0; i < packetQueue.length; i++) {
		sendPacket(packetQueue[i]);
	    }
	    packetQueue = [];
	} else {
	    error("Vi fikk ikke logget inn på websocket-serveren!");
	    socket.close();
	}
    });
    
    return wsObject;
})();

