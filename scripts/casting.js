/*
 * This file is part of InfectedCrew.
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
var Template = function() {};
Template.prototype.renderCreateFields = function() {
    $("#customPreferences").html("<i>Test template, please ignore</i>");
};
Template.prototype.getData = function() {
    return {};
};
Template.prototype.render = function() {
};

var CsgoUserTemplate = function() {};
CsgoUserTemplate.prototype = Object.create(Template.prototype);
CsgoUserTemplate.prototype.constructor = CsgoUserTemplate;
CsgoUserTemplate.prototype.render = function() {
    var contentHtml = [];
    contentHtml.push('<div class="split">');

    contentHtml.push('<div class="solo" style="float: left; position: relative; top: 290px; left: 300px;"><img src="../content/castingAssets/' + castingPageData.avatar + '" /><div class="imageComment"><span><p>' + castingPageData.userRole + '</p><p>' + castingPageData.userName + '</p></div></span></div>');
    contentHtml.push('<div class="greyedBox solo" style="float: right; position:relative; top: 290px; right: 300px; ">');
    contentHtml.push('<ul>');

    contentHtml.push('<li>Kills: ' + castingPageData.kills + '</li>');
    contentHtml.push('<li>Assists: ' + castingPageData.assists + '</li>');
    contentHtml.push('<li>Deaths: ' + castingPageData.deaths + '</li>');
    contentHtml.push('<li>K/D: ' + castingPageData.kdRatio + '</li>');
    contentHtml.push('<li>H/S ratio: ' + castingPageData.hsRatio + '</li>');
    contentHtml.push('<li>Entry kills: ' + castingPageData.entryKills + '</li>');
    contentHtml.push('<li>Clutch rounds: ' + castingPageData.clutchRounds + '</li>');
    
    contentHtml.push('</ul>');
    contentHtml.push('</div>');
    
    contentHtml.push('</div>');
    contentHtml.push('<div class="teamDiv">' + castingPageData.teamName + '</div>');
    $("#content").html(contentHtml.join(""));
};

CsgoUserTemplate.prototype.renderCreateFields = function() {
    var preferenceHtml = [];
    preferenceHtml.push('<table>');
    preferenceHtml.push('<tr><td>Navn på bruker:</td><td><input id="userName" type="text" placeholder="Skriv et navn her..." /></td></tr>');
    preferenceHtml.push('<tr><td>Rolle:</td><td><input id="userRole" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bilde:</td><td><span id="csgoAvatar"></span></td></tr>');
    preferenceHtml.push('<hr />');
    preferenceHtml.push('<tr><td>Kills:</td><td><input id="kills" type="text" placeholder="Antall drap" /></td></tr>');
    preferenceHtml.push('<tr><td>Assists:</td><td><input id="assists" type="text" placeholder="Antall assists" /></td></tr>');
    preferenceHtml.push('<tr><td>Deaths:</td><td><input id="deaths" type="text" placeholder="Antall dødsfall" /></td></tr>');
    preferenceHtml.push('<tr><td>KD:</td><td><input id="kdRatio" type="text" placeholder="KD-ratio" /></td></tr>');
    preferenceHtml.push('<tr><td>HS:</td><td><input id="hsRatio" type="text" placeholder="Hva er HS-ratio?" /></td></tr>');
    preferenceHtml.push('<tr><td>Entry kills:</td><td><input id="entryKills" type="text" placeholder="Entry kills!" /></td></tr>');
    preferenceHtml.push('<tr><td>Clutch rounds:</td><td><input id="clutchRounds" type="text" placeholder="Clutch rounds" /></td></tr>');
    preferenceHtml.push('<tr><td>Team-navn:</td><td><input id="teamName" type="text" placeholder="Teamets navn" /></td></tr>');
    preferenceHtml.push('</table>');
    $("#customPreferences").html(preferenceHtml.join(""));
    createImageUploader("csgoAvatar", "csgoAvatar");
};
CsgoUserTemplate.prototype.getData = function() {
    return {userName: $("#userName").val(),
	    avatar: window.csgoAvatar,
	    userRole: $("#userRole").val(),
	    kills: $("#kills").val(),
	    assists: $("#assists").val(),
	    deaths: $("#deaths").val(),
	    kdRatio: $("#kdRatio").val(),
	    hsRatio: $("#hsRatio").val(),
	    entryKills: $("#entryKills").val(),
	    clutchRounds: $("#clutchRounds").val(),
	    teamName: $("#teamName").val()};
};

//new thing
var CastingCamTemplate = function() {};
CastingCamTemplate.prototype = Object.create(Template.prototype);
CastingCamTemplate.prototype.constructor = CastingCamTemplate;
CastingCamTemplate.prototype.render = function() {
    var contentHtml = [];
    contentHtml.push('<div class="agendaBox"><div class="spacerr"></div>' + castingPageData.agendaData + '</div>');
    $("#content").html(contentHtml.join(""));
};

CastingCamTemplate.prototype.renderCreateFields = function() {
    var preferenceHtml = [];
    preferenceHtml.push('<textarea id="agendaData" rows="10" cols="50" />');
    $("#customPreferences").html(preferenceHtml.join(""));
};
CastingCamTemplate.prototype.getData = function() {
     return {agendaData: $("#agendaData").val()};
};

var CsgoTeamTemplate = function() {};
CsgoTeamTemplate.prototype = Object.create(Template.prototype);
CsgoTeamTemplate.prototype.constructor = CsgoTeamTemplate;
CsgoTeamTemplate.prototype.render = function() {
    var contentHtml = [];
    contentHtml.push('<div class="spacer"></div>');
    contentHtml.push('<div class="tripleSplit">');

    contentHtml.push('<div class="tripleImg"><img src="../content/castingAssets/' + castingPageData.user1Image + '" /><div class="imageComment"><span><p>' + castingPageData.user1Role + '</p><p>' + castingPageData.user1Name + '</p></div></span></div>');
    contentHtml.push('<div class="tripleImg"><img src="../content/castingAssets/' + castingPageData.user3Image + '" /><div class="imageComment"><span><p>' + castingPageData.user3Role + '</p><p>' + castingPageData.user3Name + '</p></div></span></div>');
        contentHtml.push('<div class="tripleImg"><img src="../content/castingAssets/' + castingPageData.user2Image + '" /><div class="imageComment"><span><p>' + castingPageData.user2Role + '</p><p>' + castingPageData.user2Name + '</p></div></span></div>');
    
    contentHtml.push('</div>');
    contentHtml.push('<div class="tripleSplit">');

    contentHtml.push('<div class="tripleImg"><img src="../content/castingAssets/' + castingPageData.user4Image + '" /><div class="imageComment"><span><p>' + castingPageData.user4Role + '</p><p>' + castingPageData.user4Name + '</p></div></span></div>');

    contentHtml.push('<div class="teamDivv tripleImg">' + castingPageData.teamName + '</div>');
    contentHtml.push('<div class="tripleImg"><img src="../content/castingAssets/' + castingPageData.user5Image + '" /><div class="imageComment"><span><p>' + castingPageData.user5Role + '</p><p>' + castingPageData.user5Name + '</p></div></span></div>');
    
    contentHtml.push('</div>');
    
    //contentHtml.push('</div>');
    $("#content").html(contentHtml.join(""));
};

CsgoTeamTemplate.prototype.renderCreateFields = function() {
    var preferenceHtml = [];
    preferenceHtml.push('<table>');
    preferenceHtml.push('<tr><td>Navn på team:</td><td><input id="teamName" type="text" placeholder="Skriv et navn her..." /></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('<tr><td>Bruker 1 navn:</td><td><input id="user1Name" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 1 rolle:</td><td><input id="user1Role" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 1 Bilde:</td><td><span id="user1Image"></span></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('<tr><td>Bruker 2 navn:</td><td><input id="user2Name" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 2 rolle:</td><td><input id="user2Role" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 2 Bilde:</td><td><span id="user2Image"></span></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('<tr><td>Bruker 3 navn:</td><td><input id="user3Name" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 3 rolle:</td><td><input id="user3Role" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 3 Bilde:</td><td><span id="user3Image"></span></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('<tr><td>Bruker 4 navn:</td><td><input id="user4Name" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 4 rolle:</td><td><input id="user4Role" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 4 Bilde:</td><td><span id="user4Image"></span></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('<tr><td>Bruker 5 navn:</td><td><input id="user5Name" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 5 rolle:</td><td><input id="user5Role" type="text" placeholder="Skriv rollen her" /></td></tr>');
    preferenceHtml.push('<tr><td>Bruker 5 Bilde:</td><td><span id="user5Image"></span></td></tr>');
    preferenceHtml.push('<tr>&nbsp;</tr>');
    preferenceHtml.push('</table>');
    $("#customPreferences").html(preferenceHtml.join(""));
    createImageUploader("user1Image", "user1Image");
    createImageUploader("user2Image", "user2Image");
    createImageUploader("user3Image", "user3Image");
    createImageUploader("user4Image", "user4Image");
    createImageUploader("user5Image", "user5Image");
};
CsgoTeamTemplate.prototype.getData = function() {
    return {user1Image: window.user1Image,
	    user2Image: window.user2Image,
	    user3Image: window.user3Image,
	    user4Image: window.user4Image,
	    user5Image: window.user5Image,
	    teamName: $("#teamName").val(),
	    user1Role: $("#user1Role").val(),
	    user1Name: $("#user1Name").val(),
	    user2Role: $("#user2Role").val(),
	    user2Name: $("#user2Name").val(),
	    user3Role: $("#user3Role").val(),
	    user3Name: $("#user3Name").val(),
	    user4Role: $("#user4Role").val(),
	    user4Name: $("#user4Name").val(),
	    user5Role: $("#user5Role").val(),
	    user5Name: $("#user5Name").val()};
};

var templates = {
    default: new Template(), csgoUserTemplate: new CsgoUserTemplate(), csgoTeamTemplate: new CsgoTeamTemplate(), castingCamTemplate: new CastingCamTemplate()
};
$(document).ready(function() {
    templates.default.renderCreateFields();
    $("#templateSelector").on('change', function(){
	$("#customPreferences").fadeOut(100, function(){
	    templates[$("#templateSelector").val()].renderCreateFields();
	    $("#customPreferences").fadeIn(100);
	});
    });
});
function renderCasting() {
  templates[template].render();  
};
function createCastingPage() {
    $.getJSON("../api/json/compo/addCastingPage.php?name=" + encodeURIComponent($("#castingPageName").val()) + "&template=" + encodeURIComponent($("#templateSelector").val()) + "&data=" + encodeURIComponent(JSON.stringify(templates[$("#templateSelector").val()].getData())), function(data){
	if(data.result) {
	    location.reload();
	} else {
	    error(data.message);
	}
    });
};

function createImageUploader(containerId, variableName) {
    $("#" + containerId).html('<form action="../api/json/compo/uploadCastingAsset.php" method="post" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="15728640" /><label for="file">Filnavn:</label><input type="file" name="file" id="file"></form>');
    $("#"+containerId).find("form").find("#file").change(function(){
	$("#"+containerId).find("form").submit();
    });
    var options = {
	success: function(responseText, statusText, xhr, $form) {
	    var data = jQuery.parseJSON(responseText);
	    if (data.result) {
		$("#"+containerId).html("<i>Lastet opp: " + data.uploadedName + "</i>");
		window[variableName] = data.uploadedName;
	    } else {
		error(data.message);
		$("#"+containerId).find("form").fadeIn();
	    }
	}
    };
    $("#"+containerId).find("form").last().ajaxForm(options);
}
