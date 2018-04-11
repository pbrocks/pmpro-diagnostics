jQuery( document ).ready(function($) {
	$( ".progress-bar-progress.fixed > span" ).each(function() {
		$( this )
			.data( "origWidth", $( this ).width() )
			.width( 0 )
			.animate({
				width: $( this ).data( "origWidth" )
			}, 1200);
	});
});
