$(document).ready
(
    function()
    {
        $("#player-name").typeahead(
            {
                hint: true,
                highlight: true,
                minLength: 2
            },
            {
                name: 'players',
                displayKey: 'name',
                source: function(q, cb)
                {
                    $.get( "/a/_lookahead_player", {term: q}, function( data ) {
                        cb(data);
                    });
                }
            }
        );
    }
);