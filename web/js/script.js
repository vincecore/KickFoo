$(document).ready(function(){
    $('#topscorer-stats').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "aaSorting": [[ 2, "desc" ]],
        "aoColumnDefs": [
            {
                "sClass": 'align-left',
                "aTargets": [ 1 ]
            }
        ],
        "fnDrawCallback": function ( oSettings ) {
            if ( oSettings.bSorted || oSettings.bFiltered ) {
                for (var i=0, iLen=oSettings.aiDisplay.length; i<iLen; i++) {
                    $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
                }
            }
        }
    });
    $('#game-stats').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "aaSorting": [[ 2, "desc" ]],
        "aoColumnDefs": [
            {
                "sClass": 'align-left',
                "aTargets": [ 1 ]
            }
        ],
        "fnDrawCallback": function ( oSettings ) {
            if ( oSettings.bSorted || oSettings.bFiltered ) {
                for (var i=0, iLen=oSettings.aiDisplay.length; i<iLen; i++) {
                    $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
                }
            }
        }
    });
    $('#team-stats').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "aaSorting": [[ 2, "desc" ]],
        "aoColumnDefs": [
            {
                "sClass": 'align-left',
                "aTargets": [ 1 ]
            }
        ],
        "fnDrawCallback": function ( oSettings ) {
            if ( oSettings.bSorted || oSettings.bFiltered ) {
                for (var i=0, iLen=oSettings.aiDisplay.length; i<iLen; i++) {
                    $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
                }
            }
        }
    });
    $('#reportrange').daterangepicker(
    {
        ranges: {
            'From The Start': ['today', 'today'],
            'This Week': [Date.today().setWeek(Date.today().getWeek()), Date.today().setWeek(Date.today().getWeek()).add({ days: 6 })],
            'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
        }
    },
        function(start, end, range) {
            window.location = currentRoute + '?start='+start.toString('yyyy/M/d')+'&end='+end.toString('yyyy/M/d')+'&range='+range;
            return false;
        }
    );
        /* Stats */
    $(document).on("change", "#stats-filter-time", function(){
        var filter = $('#stats-filter-time').val();
        window.location = '/admin/stats/switch-time-filter?filter='+filter;
        return false;
    });

    /* API TEST */
    $(document).on("click", "#add-goal-submit", function(){
        var form = $('#add-goal');
        $('#add-goal-result').val('Loading...');
        $.ajax( {
            type: "POST",
                url: form.attr('action')+'?call=add-goal',
                data: form.serialize(),
                success: function( response ) {
                    $('#add-goal-result').val(response);
                }
            }
        )
        return false;
    });
    $(document).on("click", "#claim-goal-submit", function(){
        var form = $('#claim-goal');
        $('#claim-goal-result').val('Loading...');
        $.ajax( {
            type: "POST",
                url: form.attr('action')+'?call=claim-goal',
                data: form.serialize(),
                success: function( response ) {
                    $('#claim-goal-result').val(response);
                }
            }
        )
        return false;
    });
    $(document).on("click", "#switch-submit", function(){
        var form = $('#switch');
        $('#switch-result').val('Loading...');
        $.ajax( {
            type: "POST",
                url: form.attr('action')+'?call=switch-players',
                data: form.serialize(),
                success: function( response ) {
                    $('#switch-result').val(response);
                }
            }
        )
        return false;
    });
});

function addGoal(data)
{
    var goalType = 'a goal';
    if (data.goal.type == "owngoal") {
	    goalType = 'an owngoal';
    }

    var float = 'left';
    if (data.goal.team.id == data.game.teamOne.id) {
        if (data.goal.type == 'owngoal') {
            float = 'right';
        } else {
            float = 'left';
        }
    } else {
        if (data.goal.type == 'owngoal') {
            float = 'left';
        } else {
            float = 'right';
        }
    }

    var goalEvent;
    goalEvent = '<div class="clearfix">';
    goalEvent += '<div class="game-event media show-' + float + '">';
    goalEvent += ' <a class="pull-' + float + '" href="#">';
    goalEvent += '  <img class="media-object avatar" src="/avatars/' + data.player.id + '.jpg" width="64"  />'
    goalEvent += ' </a>';
    goalEvent += ' <div class="media-body align-' + float +'">';
    goalEvent += '  <h4 class="media-heading">';
    goalEvent += data.player.firstname + ' scored ' + goalType + '!';
    goalEvent += '  </h4>';
    goalEvent += data.goal.goalsTeamOne + ' - ' + data.goal.goalsTeamTwo;
    goalEvent += ' </div>';
    goalEvent += '</div>';
    goalEvent += '</div>';

    $(goalEvent).hide().prependTo('#game-events').slideDown("slow");
}

function updateScore(game)
{
    $('.game-info .playerOne').html(game.playerOneTeamOne.firstname + ' - ' + game.playerTwoTeamOne.firstname);
    $('.game-info .game-score').html('<span>' + game.goalsTeamOne + '</span> - <span>' + game.goalsTeamTwo + '</span>');
    $('.game-info .playerTwo').html(game.playerOneTeamTwo.firstname + ' - ' + game.playerTwoTeamTwo.firstname);
}

function deleteLastGoal(game)
{
    $('#game-events div:first').remove();
    updateScore(game);
}