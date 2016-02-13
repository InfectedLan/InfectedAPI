module = (function(){
    var pluginObj = {};

    var startGame = function(consoleData){
	var connectUrl = 'steam://connect/' + consoleData.replace(";password ", "/");
	console.log("Connecting to " + connectUrl);
	window.location = connectUrl;
    };

    pluginObj.renderCustomScreen = function(currMatchData) {
	var banData = currMatchData.matchData.banData;
	var banHtml = [];
	banHtml.push('<div class="voteScreen">');
	banHtml.push('<div class="banBoxText">');
	banHtml.push('<br>');
	banHtml.push('<br>');
	if(banData.turn == 2) {
	    banHtml.push('<p style="text-align:right; margin:30px 0px 0px;">Vennligst vent...</p>');
	    banHtml.push('<p style="font-size: 30px; margin-top: 0px; text-align:right;"></p>');
	} else {
	    banHtml.push('<p style="text-align:right; margin:30px 0px 0px;">Klikk et map når det er din tur til å banne map</p>');
	    banHtml.push('<p style="font-size: 30px; margin-top: 0px; text-align:right;">Det er <span class="playerNameBanning">"'+ getCurrentPicker(data) + '" </span> sin tur til å banne</p>');
	}
	banHtml.push('<br>');
	banHtml.push('</div>');
	for(var i = 0; i < banData.options.length; i++) {
	    banHtml.push('<div id="banBoxId' + i + '" class="banBox">');
	    if(banHtml.options[i].isBanned) {
	        banHtml.push('<img src="images/' + banData.options[i].thumbnailUrl + '_banned.png"/>');
	    } else {
		banHtml.push('<img src="images/' + banData.options[i].thumbnailUrl + '.png"/>');
	    }
	    banHtml.push('<p>' + banData.options[i].name + '</p>');
	    banHtml.push('</div>');
	}
        banHtml.push('</div>');
        $("#matchArea").html(banHtml.join(""));
        for(var i = 0; i < banData.options.length; i++) {
	    $("#banBoxId" + i).click({mapId: banData.options[i].id}, function(e) {
		Match.banMap(e.data.mapId);
	    });
	}
    };

    pluginObj.renderGameScreen = function(currMatchData) {
	var matchData = [];

	matchData.push('<div class="playScreen">');
        matchData.push('<div style="position:relative; overflow:hidden; height:200px;">');
        matchData.push('<div style="float:left; position:relative; width:50%; height:100%;">');
        matchData.push('<p style="float:right; position:absolute; bottom:0; right:20px; font-size:30px; margin-bottom:0px;">Map: ' + currMatchData.gameData.mapData.name + ' </p>');
        matchData.push('</div>');
        matchData.push('<div style="float:left; position:relative; width:50%;  height:100%">');
        matchData.push('<div class="map">');
        matchData.push('<img src="images/' + currMatchData.gameData.mapData.thumbnail + '.png" />');
        matchData.push('</div>');
        matchData.push('</div>');
        matchData.push('</div>');
        matchData.push('<br />');
        matchData.push('<p id="startGameBtn" class="acpt acptLarge go">PLAY</p>');
        matchData.push('<h4 style="text-align: center;">NB: Har du Windows 8 er du NØDT til å koble til med konsollen</h4>');
        matchData.push('<p style="text-align: center;">Trykk play eller skriv i konsollen: <i>connect ' + currMatchData.gameData.connectDetails + '</i></p>');
        //matchData.push('<p class="ippw">Hvert lag er nødt til å skrive !map de_' + data.matchData.gameData.mapData.name.toLowerCase() + ' når de kobler til</p>');
        matchData.push('</div>');
	$("#matchArea").html(matchData.join(""));
	$("#startGameBtn").click({consoleData: currMatchData.gameData.connectDetails}, function(e) {
	    startGame(e.data.consoleData);
	});
    };

    pluginObj.decorateCompoPage = function() {
	$("#mainContent").append("<h1>Placeholder for brackets</h1>");
    };

    return pluginObj;
})();
