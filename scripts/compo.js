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
/******************************************************
 * HTML
 */

var login_html = '<div id="loginbox"><script src="scripts/login.js"></script><form class="login" method="post"><ul><li><input class="input" type="text" name="identifier" placeholder="Brukernavn, E-post eller Telefon"></li><li><input class="input" name="password" type="password" placeholder="Passord"></li><li><input class="button" id="submit" name="submit" type="submit" value="Logg inn"></li></ul></form><br /><i>Du bruker samme bruker på composiden og ticketsiden</i></div>';

var sidebar_html = '<div id="content" style="display:none;"><div id="leftColumn"><div id="profileBox"><div id="userProfilePic"></div><div id="userName"></div><div><a id="editUserLabel" href="javascript:editUser()">Endre profil</a><a id="logOutLabel" href="javascript:logout()">Logg ut</a></div></div><div id="teamBox"><p style="position:absolute; top:-45px;">Teams</p><div id="teamData"><h3>Laster inn...</h3></div><p id="addTeam"><span style="font-size:20px; margin-top:-15px;">+</span> Add Team</p></div><div id="chatBox"><div id="chatContainer"></div></div></div><div id="rightColumn"><div id="banner"></div><div id="mainContent"></div></div></div>';

var newTeam_html = '<h1>Lag team</h1><table><tr><td width="50%"><table><tr><td>Teamname:</td><td><input type="text" id="clanName" /></td></tr><tr><td>Teamtag:</td><td><input type="text" id="clanTag" /></td></tr><tr><td>Compo:</td><td><select id="compoSelect"></select></td></tr><tr><td><div id="addClanButtonWrapper"><input id="btnRegisterClan" type="button" value="Lag klan!" /></div></td></tr></table></td><td width="50%">Invite teammates: <input id="inviteSearchBox" type="text" /><br /><div id="searchResultsResultPane"></div><br /><h3>Invited players:</h3><div id="invidedPlayers"></div></td></tr></table>';

var clan_html = '<h1 id="clanLabel">Clan</h1><br /><h3 id="compoLabel">compo</h3><br /><b id="qualifiedNotification">notification</b><br /><br /><h2>Medlemmer</h2><table id="playingTable"></table><br /><h2>Step-in medlemmer</h2><table id="stepinTable"></table><br /><h2>Inviterte medlemmer</h2><table id="invitedTable"></table>';

/******************************************************
 * Page master class
 */
var Page = function() {

};

//If this doesn't return true, it is expected that fading in is handled manually
Page.prototype.render = function() {
    $("body").html("<h1>Placeholder, please fix</h1>");
    return true;
};

//Called as soon as we know we are transferring to this page. Used to load stuff while the old page is animating out
Page.prototype.onInit = function() {

}
//Called as soon as the page starts fading out
Page.prototype.onDeInit = function() {
    console.log("Goodbye!");
};


/******************************************************
 * Login page
 */

var LoginPage = function() {
    Page.call(this);
};

//Make LoginPage a subclass of Page. Javascript OOP is weird
LoginPage.prototype = Object.create(Page.prototype);
LoginPage.prototype.constructor = LoginPage;

LoginPage.prototype.render = function() {
    $("body").html(login_html);
    return true;
};

/*****************************************************
 * Index page
 */

var IndexPage = function() {
    Page.call(this);
};

IndexPage.prototype = Object.create(Page.prototype);
IndexPage.prototype.constructor = IndexPage;
IndexPage.prototype.render = function() {
    $("#mainContent").html("<h1>Velkommen til infected compo v2!</h1><br /><br />");
    $("#mainContent").html("Velkommen til infected compo v2! Systemet har blitt omskrevet fra bunnen, og vi håper dere vil like den nye siden.");
    return true;
};

/*****************************************************
 * Clan page
 */

var ClanPage = function() {
    Page.call(this);
};

ClanPage.prototype = Object.create(Page.prototype);
ClanPage.prototype.constructor = ClanPage;
ClanPage.prototype.render = function() {
    $("#mainContent").html(clan_html);
    console.log("Starting downloads for clanPage");
    var currentClanId = location.hash.substr(1).split("-")[1];
    var compoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function() {});
    var compoDataTask = new DownloadDatastoreTask("json/clan/getClanData.php?id=" + encodeURIComponent(currentClanId), "clan-" + currentClanId+"-data", function() {}, false);
    var userDataTask = new DownloadDatastoreTask("json/user/getUserData.php", "userData", function(data){});
    
    var downloadManager = new PageDownloadWaiter([compoListTask, compoDataTask, userDataTask], function() {
	$.getScript(api_path + "scripts/clan.js").done(function(script, status) {
	    $("#mainContent").fadeIn(300);
	}).fail(function(jqxhr, settings, exception) {
	    console.log(exception);
	});
    });
    downloadManager.start();
    return false;
};


/*****************************************************
 * Compo list page
 */

var CompoPage = function() {
    Page.call(this);
};

CompoPage.prototype = Object.create(Page.prototype);
CompoPage.prototype.constructor = CompoPage;
CompoPage.prototype.render = function() {
    $("#mainContent").html("");
    console.log("Starting downloads for compoPage");
    var currentCompoId = location.hash.substr(1).split("-")[1];
    var compoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function() {});
    var compoDataTask = new DownloadDatastoreTask("json/compo/getCompoData.php?id=" + encodeURIComponent(currentCompoId), "compo-" + currentCompoId+"-data", function() {}, false);
    
    var downloadManager = new PageDownloadWaiter([compoListTask, compoDataTask], function() {
	console.log("Yeyyy we finished!");
	//Header
	var compo = null;
	for(var i = 0; i < datastore["compoList"].length; i++) {
	    if(datastore["compoList"][i].id == currentCompoId) {
		compo = datastore["compoList"][i];
		$("#mainContent").append("<h1>" + datastore["compoList"][i].title + "</h1><br />");
		break;
	    }
	}
	//Clans currently in
	var clanList = datastore["compo-" + currentCompoId + "-data"].clans;
	//Sort into qualified and non-qualified clans
	var qualifiedClans = [];
	var unQualifiedClans = [];
	for(var i = 0; i < clanList.length; i++) {
	    if(clanList[i].qualified) {
		qualifiedClans.push(clanList[i]);
	    } else {
		unQualifiedClans.push(clanList[i]);
	    }
	}
	//Render compo list
	if(compo.participantLimit != 0) {
	    $("#mainContent").append("<h3>Kvalifiserte lag(" + qualifiedClans.length + " av " + compo.participantLimit + "):</h3>");
	} else {
	    $("#mainContent").append("<h3>Kvalifiserte lag:</h3>");
	}
	$("#mainContent").append("<br /><br />");

	var addClanLink = function(id, text) {
	    //console.log("Adding clan link for " + text + "(id " + id + ")");
	    $("#mainContent").find("ul").last().append('<li class="teamEntry" >' + text +"</li>");
	    $("#mainContent").find("li").last().on('click', function() {
		window.location="index.php#clan-" + id;
	    });
	};
	
	if(qualifiedClans.length == 0) {
	    $("#mainContent").append("<i>Ingen lag er kvalifiserte enda</i>");
	} else {
	    $("#mainContent").append("<ul></ul>");
	    for(var i = 0; i < qualifiedClans.length; i++) {
		addClanLink(qualifiedClans[i].id, qualifiedClans[i].name);
	    }
	}

	$("#mainContent").append("<br /><br />");

	if(unQualifiedClans.length != 0) {
	    $("#mainContent").append("<h3>Ukvalifiserte lag:</h3>");
	    $("#mainContent").append("<ul></ul>");
	    for(var i = 0; i < unQualifiedClans.length; i++) {
		addClanLink(unQualifiedClans[i].id, unQualifiedClans[i].name);
	    }
	    if(qualifiedClans.length == compo.participantLimit) {
		$("#mainContent").append('<i>Disse lagene mangler spillere, eller rakk ikke å fylle laget før alle plassene ble tatt. Disse vil ha en sjanse til å få en plass om et kvalifisert lag må melde seg ut, eller blir diskvalifisert.</i>');
	    } else {
		$("#mainContent").append('<i>Disse lagene mangler spillere, og vil ikke kunne delta før de har fyllt laget.</i>');
	    }
	}
	/*
	$("#mainContent").append("<ul></ul>");
	for(var i = 0; i < clanList.length; i++) {
	    $("#mainContent").find("ul").first().append("<li>ayoo</li>");
	}
	*/
	loadCompoPlugin(currentCompoId, function() {
	    compoPlugins[currentCompoId].decorateCompoPage(compo);
	    $("#mainContent").fadeIn(300);
	});
    });
    downloadManager.start();
    return false;
};

/*****************************************************
 * New team page
 */

var NewTeamPage = function() {
    Page.call(this);
};

NewTeamPage.prototype = Object.create(Page.prototype);
NewTeamPage.prototype.constructor = NewTeamPage;
NewTeamPage.prototype.render = function() {
    $("#mainContent").html(newTeam_html);
    
    var compoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function() {
	for(var i = 0; i < datastore["compoList"].length; i++) {
	    $("#compoSelect").append($('<option>', {value: datastore["compoList"][i].id, text: datastore["compoList"][i].title}));
	}
	$.getScript(api_path + "scripts/addTeam.js").done(function(script, status) {
	    $("#mainContent").fadeIn(300);
	}).fail(function(jqxhr, settings, exception) {
	    console.log(exception);
	});
    });
    compoListTask.start();
    return false;
};

/*****************************************************
 * Current match page
 */

var CurrentMatchPage = function() {
    Page.call(this);
};

CurrentMatchPage.prototype = Object.create(Page.prototype);
CurrentMatchPage.prototype.constructor = CurrentMatchPage;
CurrentMatchPage.prototype.render = function() {
    var userDataTask = new DownloadDatastoreTask("json/user/getUserData.php", "userData", function(data){
	Match.renderSite(true);
	$("#mainContent").fadeIn(300);
    });
    userDataTask.start();
    return false;
};
CurrentMatchPage.prototype.onDeInit = function() {
    console.log("Unsubscribing to chatroom");
    Match.handleUnload();
};

/*****************************************************
 * Download manager
 */

var DownloadDatastoreTask = function(url, name, onFinished, ignoreIfExisting) {
    this.url = url;
    this.onFinished = onFinished;
    this.name = name;
    this.ignoreIfExisting = (typeof(ignoreIfExisting) !== 'undefined' ? ignoreIfExisting : true);
};

DownloadDatastoreTask.prototype.start = function() {
    if(this.ignoreIfExisting && typeof(datastore[this.name]) !== "undefined") {
	console.log("Ignoring datastore download " + this.url + " as it allready exists");
	this.onFinished(datastore[this.name]);
	if(typeof(this.downloadMaster) !== "undefined") {
	    this.downloadMaster.success(this);
	}
    } else if(typeof(downloadingDatastores[this.name]) !== "undefined") { //Checks if we have a download going for it
	console.log("Allready downloading data, putting this function in the finished queue: " + this.url);
	var _this = this;
	if(typeof(this.onFinished) !== "undefined") {
	    downloadingDatastores[this.name].push(this.onFinished);
	}
	if(typeof(this.downloadMaster) !== "undefined") {
	    var _this = this;
	    downloadingDatastores[this.name].push(function() {
		_this.downloadMaster.success(_this);
	    });
	}
    } else {
	console.log("Downloading new datastore " + this.url + ".");
	downloadingDatastores[this.name] = [];
	var _thiss = this;
	$.getJSON(api_path + this.url, function(data){
	    console.log("Done downloading " + _thiss.url);
	    if(data.result == true)
	    {
		datastore[_thiss.name] = data.data;
		_thiss.onFinished(data.data);
		//Run other download functions
		for(var i = 0; i < downloadingDatastores[_thiss.name].length; i++) {
		    downloadingDatastores[_thiss.name][i](data.data);
		}
		delete downloadingDatastores[_thiss.name];
		if(typeof(_thiss.downloadMaster) !== "undefined") {
		    _thiss.downloadMaster.success(_thiss);
		}
	    } else {
		if(typeof(_thiss.downloadMaster) !== "undefined") {
		    _thiss.downloadMaster.fail(_thiss);
		}
	    }
	});
    }
};

//Don't uncomment this, will break DownloadDatastoreTask.start(). Times i have stumbled upon this line wondering what i did wrong: 1
//DownloadDatastoreTask.prototype.downloadMaster = null;

//doneEvent takes either a string or a function. if string, it will fade in the div with an id specified. If function, it will run the function
var PageDownloadWaiter = function(tasks, doneEvent) {
    this.tasks = tasks;
    this.downloaded = 0;
    this.doneEvent = doneEvent;
    for(var i = 0; i < this.tasks.length; i++) {
	this.tasks[i].downloadMaster = this;
    }
    console.log("Download manager initialized");
};

PageDownloadWaiter.prototype.start = function() {
    for(var i = 0; i < this.tasks.length; i++) {
	this.tasks[i].start();
    }
};

PageDownloadWaiter.prototype.fail = function(task) {
    error("Det skjedde en feil under nedlastingen av nødvendig data. Prøv å oppdatere siden");
    console.log("Failed download: " + task);
};

PageDownloadWaiter.prototype.success = function(task) {
    console.log("Download waiter completed!");
    this.downloaded++;
    if(this.downloaded == this.tasks.length) {
	if(typeof(this.doneEvent) == "function") {
	    this.doneEvent();
	} else {
	    $("#" + this.doneEvent).fadeIn(300);
	}
    }
};

function getDatastore(url, name, onFetch){
    if(typeof(datastore[name]) === "undefined") {
	var downloader = new DownloadDatastoreTask(url, name, onFetch);
	downloader.start();
    } else {
	onFetch(datastore[name]);
    }
};

/*****************************************************
 * Page bookkeeping
 */

var pages = {index: new IndexPage(), compo: new CompoPage(), newTeam: new NewTeamPage(), clan: new ClanPage(), currentMatch: new CurrentMatchPage()};
var currentPage = "login";
var datastore = {}; //This is where we store data we have downloaded
var downloadingDatastores = {};
var compoPlugins = [];
var hasRenderedChat = false;

//Startup


//Used to get unique div id's when needed
var getUniqueId = (function(){
    var uuidCounter = 0;
    return function() {
	return uuidCounter++;
    };
})();

function getPageName() {
    return location.hash.substring(1).split("-")[0];
}

/*****************************************************
 * Page handling
 */

function gotoPage(hashId) {
    console.log("going to page " + hashId);
    if(typeof(pages[hashId]) === 'undefined') {
	console.log("Tried to navigate to non-existing page: " + hashId);
	return;
    }
    if(currentPage == "login") {
	renderSidebar();
	currentPage = hashId;
	console.log("Starting transfer to " + hashId);
	pages[hashId].onInit();
	var result = pages[hashId].render();
	//We want to do the javascript before things are faded in
	if(result) {
	    $("#mainContent").fadeIn(300);
	}
	Match.init();
    } else {
	pages[currentPage].onDeInit();
	pages[hashId].onInit();
	$("#mainContent").fadeOut(300, function(){
	    currentPage = hashId;
	    var result = pages[hashId].render();
	    if(result) {
		$("#mainContent").fadeIn(300);
	    }
	    
	});
    }
    renderBanner(); //Update the banner selected state
}

function refresh() {
    datastore = []; //Clear all stored data
    currentPage == "login";
    if(location.hash.length>0) {
	if(typeof(pages[location.hash.substring(1).split("-")[0]]) !== 'undefined') {
	    gotoPage(location.hash.substring(1).split("-")[0]);
	} else {
	    gotoPage("index");
	}
    } else {
	gotoPage("index");
    }
}

function renderSidebar() {
    $("body").html(sidebar_html);
    var userDataTask = new DownloadDatastoreTask("json/user/getUserData.php", "userData", function(data){
	console.log("Got user data: " + data);
	$("#userProfilePic").html('<img src="' + data.avatar.thumb + '" />');
	$("#userName").html('<p>' + data.displayName + '</p>');
	$("#content").fadeIn(300);
    });
    userDataTask.start();
    renderBanner();
    renderClanList();
    $("#addTeam").click(function() {
	window.location = "index.php#newTeam";
    });
}

function renderClanList() {
    //We need to have the user data before we can do anything, as we need to know if the user has accepted a match or not. We can not allways guarantee the user data to be here before WS data.
    if(Match.isInMatch()) {
	var userDataTask = new DownloadDatastoreTask("json/user/getUserData.php", "userData", function(data){
	    if(Match.shouldAcceptMatch(data.id)) {
		$("#teamData").html("<center><h1 style='top: -10px;'>Game ready</h1></center><p id='smallAccept' class='acpt acptSmall'>ACCEPT</p>");
		$("#addTeam").hide();
		$("#smallAccept").click(/*{matchId: data.matchData.id}, */function(e) {
		    //acceptMatch(e.data.matchId);
		    Match.acceptMatch();
		    window.location = "index.php#currentMatch";
		});
	    }	else {
		$("#teamData").html("<center><h1>Gamet ditt er klart!</h1> Vennligst gå <a href='index.php#currentMatch'>hit</a> for å starte</center>");
		$("#addTeam").remove();
	    }
	});
	userDataTask.start(); //Ensure we have user data.
    } else {
	var clanListTask = new DownloadDatastoreTask("json/compo/getCompoStatus.php", "clanList", function(data){
	    //Add teams
	    $("#teamData").html("");
	    for(var i = 0; i < data.clans.length; i++) {
		$("#teamData").append('<div class="teamEntry" id="teamHeaderId' + data.clans[i].id + '"><h1>' + data.clans[i].tag + '</h1><h3> - ' + data.clans[i].compo.tag + '</h3>');
		$("#teamHeaderId" + data.clans[i].id).click({teamId: data.clans[i].id}, function(e){window.location="index.php#clan-" + e.data.teamId});
	    }
	    //Render invites
	    var acceptInvite = function(inviteId) {
		$.getJSON('json/invite/acceptInvite.php?id=' + encodeURIComponent(inviteId), function(data){
		    if(data.result) {
			renderClanList();
		    } else {
			error(data.message);
		    }
		});
	    }
	    var declineInvite = function(inviteId) {
		$.getJSON('json/invite/declineInvite.php?id=' + encodeURIComponent(inviteId), function(data){
		    if(data.result) {
			renderClanList();
		    } else {
			error(data.message);
		    }
		});
	    }
	    for(var i = 0; i < data.invites.length; i++) {
		$("#teamData").append('<div class="teamEntry" id="teamHeaderId' + data.invites[i].clanData.id + '"><h1>' + data.invites[i].clanData.tag + '</h1><h3> - ' + data.invites[i].compo.tag + '</h3><br /><i class="teamEntry" id="inviteAccept' + data.invites[i].id + '">Godta</i> - <i class="teamEntry" id="inviteDecline' + data.invites[i].id + '">Avslå</i></div>');
		$("#inviteAccept" + data.invites[i].id).click({inviteId: data.invites[i].id}, function(e){
		    acceptInvite(e.data.inviteId);
		});
		$("#inviteDecline" + data.invites[i].id).click({inviteId: data.invites[i].id}, function(e){
		    declineInvite(e.data.inviteId);
		});
		$("#teamInviteId" + data.invites[i].clanData.id).click({teamId: data.invites[i].clanData.id}, function(e){
		    window.location="index.php?page=team&id=" + e.data.teamId
		});
	    }
	    $("#addTeam").show();
	}, false);
	clanListTask.start();
    }
}

function renderChat() {
    var acompoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function() {});
    var clanListTask = new DownloadDatastoreTask("json/compo/getCompoStatus.php", "clanList", function() {});
    var chatDownloadManager = new PageDownloadWaiter([acompoListTask, clanListTask], function() {
	if(!hasRenderedChat) {
	    if(datastore["clanList"].clans.length>0) {
		console.log("We have a clan. We will use the first one as the chattable clan");
		for(var i = 0; i < datastore["compoList"].length; i++) {
		    if(datastore["clanList"].clans[0].compo.id == datastore["compoList"][i].id) {
			$("#chatBox").prepend('<div class="boxTitle"><p class="boxTitleText">Chat - ' + datastore["compoList"][i].title + '</p></div>');
			Chat.bindChat("chatContainer", datastore["compoList"][i].chat, 415);
			hasRenderedChat = true;
			return;
		    }
		}
	    }
	}
    });
    chatDownloadManager.start();
};

function renderBanner() {
    var compoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function(data){
	$("#banner").html("");
	var currentCompoId = -1;
	if(location.hash.substr(1).split("-")[0]=="compo") {
	    currentCompoId = location.hash.substr(1).split("-")[1];
	    console.log("Current compo id: " + currentCompoId);
	}
	for(var i = 0; i < datastore["compoList"].length; i++) {
	    $("#banner").append('<div id="compoBtn' + i + '" class="gameType ' + (datastore["compoList"][i].id == currentCompoId ? ' selected' : '') + '"><p>' + datastore["compoList"][i].tag + '</p></div>');
	    var compo = datastore["compoList"][i];
	    $("#compoBtn"+i).click({compo: compo}, function(event){
		window.location = "index.php#compo-"+event.data.compo.id;
	    });
	}
	if(Match.isInMatch()) {
	    $("#banner").append('<div id="compoBtnCurr" class="gameType ' + (getPageName() == "currentMatch" ? ' selected' : '') + '"><p>Current match</p></div>');
	    $("#compoBtnCurr").click(function(event) {
		window.location = "index.php#currentMatch";
	    });
	    
	}
    });
    compoListTask.start();
}

function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
	var pair = vars[i].split("=");
	if (pair[0] == variable) {
	    return pair[1];
	}
    } 
    return null;
}

function getCompoData(compoId) {
    for(var i = 0; i < datastore["compoList"].length; i++) {
	if(datastore["compoList"][i].id == compoId) {
	    return datastore["compoList"][i];
	}
    }
}

function loadCompoPlugin(compoId, onDone) {
    if(typeof(compoPlugins[compoId]) !== "undefined") {
	if(typeof(onDone) !== "undefined") {
	    onDone();
	}
	return;
    }
    var data = getCompoData(compoId);
    $.getScript(api_path + "plugins/compo/" + data.pluginJavascript.compoPlugin).done(function(script, status) {
	//Move the plugin to a permanent place once the script is evaluated
	console.log("Done downloading compo data!");
	compoPlugins[data.id] = module;
	delete module;
	if(typeof(onDone) !== "undefined") {
	    console.log("done");
	    onDone();
	}
    }).fail(function(jqxhr, settings, exception) {
	console.log(exception);
    });
}
