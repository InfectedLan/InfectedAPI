module = (function(){
    var pluginObj = {};

    var startGame = function(consoleData){
	var connectUrl = 'steam://connect/' + consoleData.replace(";password ", "/");
	console.log("Connecting to " + connectUrl);
	window.location = connectUrl;
    };

    var getCurrentPicker = function(banData) {
	var turn = banData.turn;
	return banData.clans[turn].name;/*
	for(var i = 0; i < banData.clans[turn].members.length; i++) {
	    if(banData.clans[turn].members[i].chief) {
		return banData.clans[turn].members[i].nick;
	    }
	}
	*/
    };

    pluginObj.renderCustomScreen = function(currMatchData) {
	var banData = currMatchData.banData;
	var banHtml = [];
	banHtml.push('<div class="voteScreen">');
	banHtml.push('<div class="banBoxText">');
	banHtml.push('<br>');
	banHtml.push('<br>');
	if(banData.turn == 2) {
	    banHtml.push('<p style="text-align:right; margin:30px 0px 0px;">Vennligst vent...</p>');
	    banHtml.push('<p style="font-size: 30px; margin-top: 0px; text-align:right;"></p>');
	} else {
	    if(typeof(spectate_mode) === "undefined") {
		banHtml.push('<p style="text-align:right; margin:30px 0px 0px;">Klikk et map når det er din tur til å banne map</p>');
	    } else {
		banHtml.push('<p style="text-align:right; margin:30px 0px 0px;"></p>');
	    }
	    banHtml.push('<p style="font-size: 40px; margin-top: 0px; text-align:right;">Det er <span class="playerNameBanning">"'+ getCurrentPicker(banData) + '"<br /> </span> sin tur til å <b>' + banData.selectType + '</b></p>');
	}
	banHtml.push('<br>');
	banHtml.push('</div>');
	for(var i = 0; i < banData.options.length; i++) {
	    banHtml.push('<div id="banBoxId' + i + '" class="banBox">');
	    if(banData.options[i].isSelected) {
		if(banData.options[i].selectionType==0) {
	            banHtml.push('<img src="images/' + banData.options[i].thumbnailUrl + '_banned.png"/>');
		} else {
		    banHtml.push('<img src="images/' + banData.options[i].thumbnailUrl + '_picked.png"/>');
		}
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
        /*matchData.push('<div style="float:left; position:relative; width:50%; height:100%;">');
          matchData.push('<p style="float:right; position:absolute; bottom:0; right:20px; font-size:30px; margin-bottom:0px;">Map: ' + currMatchData.gameData.mapData[0].name + ' </p>');
          matchData.push('</div>');*/
        matchData.push('<div style="margin: auto; position:relative; height:100%">');
	for(var i = 0; i < currMatchData.gameData.mapData.length; i++) {
            matchData.push('<div class="banBox">');
            matchData.push('<img src="images/' + currMatchData.gameData.mapData[i].thumbnail + '.png" />');
	    matchData.push('<p>' + currMatchData.gameData.mapData[i].name + '</p>');
            matchData.push('</div>');
	}
        matchData.push('</div>');
        matchData.push('</div>');
	if(typeof(spectate_mode) === "undefined") {
            matchData.push('<br />');
            //matchData.push('<p id="startGameBtn" class="acpt acptLarge go">PLAY</p>');
            //matchData.push('<h4 style="text-align: center;">NB: Har du Windows 8 er du NØDT til å koble til med konsollen</h4>');
            matchData.push('<p style="text-align: center;">Skriv i konsollen: <i>' + currMatchData.gameData.connectDetails + '</i></p>');
            //matchData.push('<p class="ippw">Hvert lag er nødt til å skrive !map de_' + data.matchData.gameData.mapData.name.toLowerCase() + ' når de kobler til</p>');
	}
        matchData.push('</div>');
	
	$("#matchArea").html(matchData.join(""));
	$("#startGameBtn").click({consoleData: currMatchData.gameData.connectDetails}, function(e) {
	    startGame(e.data.consoleData);
	});

    };

    pluginObj.decorateCompoPage = function(compo) {
	if(compo.hasMatches) {/*
	    $("#mainContent").append('<h2>Gruppe A</h2><div style="display: block;" class="bracket_container" id="bracket_container_1"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("bracket_container_1", "grp_1");
	    
	    $("#mainContent").append('<h2>Gruppe B</h2><div style="display: block;" class="bracket_container" id="bracket_container_2"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("bracket_container_2", "grp_2");

	    $("#mainContent").append('<h2>Gruppe C</h2><div style="display: block;" class="bracket_container" id="bracket_container_3"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("bracket_container_3", "grp_3");

	    $("#mainContent").append('<h2>Gruppe D</h2><div style="display: block;" class="bracket_container" id="bracket_container_4"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("bracket_container_4", "grp_4");*/
	    $("#customContent").html('<h2>Playoff-bracket</h2><div style="display: block;" class="bracket_container" id="playoffBracket"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("playoffBracket", "playoff");
	    $("#customContent").html('<h2>Lower playoff-bracket</h2><div style="display: block;" class="bracket_container" id="playoffBracketLooser"></div><br />');
	    var source = new DataSource(compo.id);
	    var bracket = source.derive("playoffBracketLooser", "play_looser");
	} else {
	    console.log("Compo has no matches... yet");
	}
    };

    return pluginObj;
})();
