var YTControl_Shortcode = {
	players: {},
	
	onReady: function() {
		jQuery('.yc_player').each( function() {
			var element = jQuery(this);
			var id = element.attr('id');
			
			var player = new YT.Player( id, {
				videoId: element.data('vid'),
				playerVars: {
					'autoplay': element.data('play'),
					'autohide': element.data('hide'),
					'theme': element.data('theme'),
					'showinfo': 0,
					'modestbranding': 1,
					'origin': location.host,
				},
				events: {
					'onReady': function( event ) {
						YTControl_Shortcode.players[id] = event.target;
					},
					'onStateChange': function( event ) {
						if ( event.data == YT.PlayerState.PLAYING ) {
							jQuery("#"+id).closest('.ytc-embed').addClass("playing");
						} else if ( event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.ENDED ) {
							jQuery("#"+id).closest('.ytc-embed').removeClass("playing");
						}
					},
				}
			} );
		} );
		
		jQuery(window).on( 'resize', YTControl_Shortcode.updateAll );
		YTControl_Shortcode.updateAll();
	},
	
	updateAll: function( event ) {
		jQuery('.ytc-embed.auto').each( function() {
			var element = jQuery(this);
			var controls = element.children('.ytc-controls');
			if ( controls.length > 0 ) {
				var ratio = 40 / element.width();
				if ( ratio < 0.08 ) {
					element.removeClass('compact');
				} else {
					element.addClass('compact');
				}
			}
		} );
	},
	
	skipTo: function( id, time ) {
		YTControl_Shortcode.players[id].seekTo( time, true );
	},
}

function onYouTubeIframeAPIReady() {
	YTControl_Shortcode.onReady();
}

(function(){ 
	// This code loads the YouTube IFrame Player API asynchronously.
	var tag = document.createElement('script');
	tag.src = "//www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
})();
