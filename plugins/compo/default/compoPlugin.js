module = (function(){
    var pluginObj = {};

    pluginObj.renderCustomScreen = function(currMatchData) {
	$("#matchArea").html("<h1>Dette skal ikke skje! Si straks ifra til R9S10-fyren</h1>");
    };

    pluginObj.renderGameScreen = function(currMatchData) {
	var matchData = [];

	matchData.push("<h1>Gamet er klart!</h1>");
	matchData.push("<p>Dere er ansvarlige for å lage et custom game. Bruk chatten for å dele informasjon.</p>");
	matchData.push("<br />");
	matchData.push("<i>Si ifra til game når dere er ferdige</i>");

	$("#matchArea").html(matchData.join(""));
    };

    pluginObj.decorateCompoPage = function(compo) {
	$("#mainContent").append('<h2>Playoff-bracket</h2><div style="display: block;" class="bracket_container" id="playoffBracket"></div><br />');
	var source = new DataSource(compo.id);
	var bracket = source.derive("playoffBracket", "playoff");
	$("#mainContent").append('<h2>Looser playoff-bracket</h2><div style="display: block;" class="bracket_container" id="playoffLooserBracket"></div><br />');
	var source = new DataSource(compo.id);
	var bracket = source.derive("playoffLooserBracket", "play_looser");
    };

    return pluginObj;
})();
