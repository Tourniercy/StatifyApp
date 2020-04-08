const $ = require('jquery');
$( document ).ready(function() {
    $('.hover-card').hover(function() {
        $('#hover-card-' + $(this).attr('name')).css('bottom', '-20px');
    }, function() {
        $('#hover-card-' + $(this).attr('name')).css('bottom', '-10px');
    });
    $(".edit-playlist").on('click', function() {
        var url = $(this).data( "idplaylist" );
        window.location.href = url;
    });
    $(".delete-track").on('click', function() {
        var trackId = $(this).data( "id" );
        var playlistId = $(this).data( "playlistid" );
        var url = $(this).data( "url" );
            $.ajax({
                url:url,
                type: "POST",
                dataType: "json",
                async: true,
                data: {id:trackId,playlistId:playlistId},
                success: function (data)
                {
                    if (data) {
                        $('tr[data-id='+trackId+']').hide('slow', function(){ $('tr[data-id='+trackId+']').remove(); });
                    }
                }
            });
    });
    let canclose = false;
    if(document.getElementById("search-title") !== null)
    {
        $('#search-title').autocomplete({
            open: function() {
                $('.ui-autocomplete').width('400px');
                $('.ui-autocomplete').addClass('list-group');
                $(".add-track").on('click', function() {
                    var trackId = $(this).data( "id" );

                    var playlistId = $("#search-title").data( "playlistid" );
                    var url = $("#search-title").data( "url-add" );
                    var track =  $(this).data( "title" );
                    var artists =  $(this).data( "artists" );
                    var album =  $(this).data( "album" );
                    $.ajax({
                        url:url,
                        type: "POST",
                        dataType: "json",
                        async: true,
                        data: {id:trackId,playlistId:playlistId},
                        success: function (data)
                        {
                            if (data) {
                                $('tbody').append( "<tr data-id='"+trackId+"'>\n" +
                                    "                    <td>"+track+"</td>\n" +
                                    "                    <td>"+artists+"</td>\n" +
                                    "                    <td>"+album+"</td>\n" +
                                    "                    <td> <i class=\"far fa-trash-alt delete-track\" data-id='"+trackId+"' data-playlistid='"+playlistId+"' data-url=\"/deleteTrackFromPlaylist\" style=\"color:#ff4444;cursor: pointer;\" aria-hidden=\"true\"></i></td>\n" +
                                    "                </tr>" );
                            }
                        }
                    });
                });
            },
            source : function(requete, reponse){ // les deux arguments représentent les données nécessaires au plugin
                $.ajax({
                    url:$('#search-title').data('url'),
                    dataType : 'json', // on spécifie bien que le type de données est en JSON
                    type : 'POST',
                    data : {
                        track_startsWith : $('#search-title').val(), // on donne la chaîne de caractère tapée dans le champ de recherche
                        maxRows : 15
                    },
                    success : function(data){
                        reponse($.map(data.items, function(object){
                            return object; // on retourne cette forme de suggestion
                        }));
                    }
                });
            }
        }).data('ui-autocomplete')._renderItem = function(ul, item){
            let artists = '';
            $.each(item.artists, function( index, value ) {
                artists += value.name;
                if (index+1 < item.artists.length) {
                    artists += ', ';
                }

            });
            return $("<li class='ui-autocomplete-row list-group-item'></li>")
                .data("item.autocomplete", item)
                .append($('<div class="row"><div class="col-3"><img src="'+item.album.images[2].url+'" width="64" height="64"></div><div class="col-8"><span><b>'+item.name+' </b>'+artists+'</span></div><div class="col-1"><i data-title="'+item.name+'" data-artists="'+artists+'" data-album="'+item.album.name+'" data-id="'+item.id+'" class="fas fa-plus-circle add-track" style="color: #00C851;cursor: pointer"></i></div></div>'))
                .appendTo(ul);
        };
    }



    $("#ui-id-1").on('click', function(event) {
        event.preventDefault();
    });
    $(".fa-play-circle").on('click', function() {
        var url= $(this).data('url');
        var trackid = $(this).data('track');
        $.ajax({
            data:{'trackid':trackid},
            url:url,
            type: "POST",
            dataType: "json",
            async: true,
            success: function (data)
            {

            }
        });
    });
});

