function switchTeam2()
{
    var p1 = $('#team2-back option:selected').val();
    var p2 = $('#team2-forward option:selected').val();
    $("#team2-back option[value='" + p2 + "']").attr("selected","selected");
    $("#team2-back").selectmenu("refresh", true);
    $("#team2-forward option[value='" + p1 + "']").attr("selected","selected");
    $("#team2-forward").selectmenu("refresh", true);
}

function switchTeam1()
{
    var p1 = $('#team1-back option:selected').val();
    var p2 = $('#team1-forward option:selected').val();
    $("#team1-back option[value='" + p2 + "']").attr("selected","selected");
    $("#team1-back").selectmenu("refresh", true);
    $("#team1-forward option[value='" + p1 + "']").attr("selected","selected");
    $("#team1-forward").selectmenu("refresh", true);
}

function addGoalToList(game, goal)
{
    $('#goals').show();
    var str = '<li><img class=\"ui-li-icon\" alt="Goal" src="/img/sport_soccer.png"> ' +goal.time +' - '+game.goalsTeamOne + ' - ' + game.goalsTeamTwo +': unclaimed</li>';
    $('ul#goalsList').prepend(str).listview("refresh");
}

function refreshGoalsList(goals)
{
    var list = '';
    $(goals).each(function(index, goal) {
        list += '<li><img class=\"ui-li-icon\" alt="Goal" src="' + goal.img + '"> ' +goal.time +' - '+goal.goalsTeamOne + ' - ' + goal.goalsTeamTwo + ': ' + goal.playerName +' </li>';
    });
    $('ul#goalsList').html(list).listview("refresh");
}

function endGame()
{
    $('#start-game').show();
    $('#end-game').hide();
    $('#delete-goal').hide();
    $('#end-game-notice').show();
    $('#home-button').show();
}

function showPreviousGamesStats(previousGamesStats)
{
    if (previousGamesStats.totalGames > 0) {
        $('#previousGamesStats').html('<p>'+previousGamesStats.teamWithMostWins.name+' won '+previousGamesStats.mostWinsCount+' of '+previousGamesStats.totalGames+' games ('+previousGamesStats.winPercentage+'%).</p>');
    }
}

function registerGoal(url, gameId, element)
{
    if ($('#end-game').is(":visible")) {
        $.mobile.showPageLoadingMsg();
        var pos = new Array();
        pos[0] = $('#team1-back option:selected').val();
        pos[1] = $('#team1-forward option:selected').val();
        pos[2] = $('#team2-back option:selected').val();
        pos[3] = $('#team2-forward option:selected').val();

        var goalType = null;
        if (element.attr('id').indexOf('-own') != -1) {
            goalType = 'owngoal';
        } else {
            goalType = 'regular';
        }

        var splittedPosition = element.attr('id').split('-');

        var player = element.attr('id').replace('goal-','');
        player = player.replace('-own','');
        var position = splittedPosition[2];
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: {
                gameId: gameId,
                playerId: $('#'+ player + ' option:selected').val(),
                players: pos,
                position: position,
                type: goalType,
                score: $('#score').text()
            },
            cache: false,
            dataType: "json",
            success: function(data) {
                var score = data.game.goalsTeamOne + ' - ' + data.game.goalsTeamTwo;
                $('#score').html(score);

                var imgUrl;
                if (data.goal.type === 'owngoal') {
                    imgUrl = '/img/owngoal.jpg';
                } else {
                    imgUrl = '/img/sport_soccer.png';
                }
                $('ul#goalsList').prepend('<li><img src="' + imgUrl + '" alt="Goal" class="ui-li-icon">' + score + ' : ' + data.player.firstname + ' ' + data.player.lastname +'</li>').listview("refresh");
                $('#goals').show();
                $('#delete-goal').show();
                $.mobile.hidePageLoadingMsg();
                if (data.game.end !== null) {
                    $('#start-game').show();
                    $('#end-game').hide();
                    $('#delete-goal').hide();
                    $('#end-game-notice').show();
                    $('#home-button').show();
                }
            },
            error: function(data) {
                alert('Something went wrong!');
                $.mobile.hidePageLoadingMsg();
            }
        });
    } else {
        alert('Game has not been started yet.');
    }
    return false;
}


function deleteLastGoal(url, gameId)
{
    if ($('#end-game').is(":visible")) {
        $.mobile.showPageLoadingMsg();
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: {
                gameId: gameId
            },
            cache: false,
            dataType: "json",
            success: function(data) {
                var score = data.goalsTeamOne + ' - ' + data.goalsTeamTwo;
                $('#score').html(score);
                $('ul#goalsList li:first').remove();
                $('ul#goalsList').listview("refresh");
                $.mobile.hidePageLoadingMsg();
                if (score === '0 - 0') {
                    $('#delete-goal').hide();
                }
            },
            error: function(data) {
                alert('Something went wrong!');
                $.mobile.hidePageLoadingMsg();
            }
        });
    } else {
        alert('Game has not been started yet.');
    }
    return false;
}

function endGame(url, gameId)
{
    if (confirm("Are you sure?")) {
        var pos = new Array();
        pos[0] = $('#team1-back option:selected').val();
        pos[1] = $('#team1-forward option:selected').val();
        pos[2] = $('#team2-back option:selected').val();
        pos[3] = $('#team2-forward option:selected').val();

        if (pos[0].length > 0 && pos[1].length > 0 && pos[2].length > 0 && pos[3].length > 0) {
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    gameId: gameId
                },
                cache: false,
                dataType: "text"
            });
            $('#start-game').show();
            $('#home-button').show();
            $('#end-game').hide();
        } else {
            alert('Please select 4 players');
        }
    }
    return false;
}
