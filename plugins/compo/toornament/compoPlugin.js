module = (function(){
    var pluginObj = {};

    pluginObj.renderCustomScreen = function(currMatchData) {
	$("#matchArea").html("<h1>Dette skal ikke skje! Si straks ifra til game-fyren</h1>");
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
	$("#customContent").html('<h2>Deltagere</h2><iframe width="640" height="360" src="https://widget.toornament.com/tournaments/787738634901643264/stages/787746956199477248/?_locale=en_US&theme=light" frameborder="0" allowfullscreen="true"></iframe>');
    };

    return pluginObj;
})();
