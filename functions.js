(function($) {
	$(document).ready(function(){
		$( 'a.ajax-post' ).off( 'click' ).on( 'click', function( e ) {
			e.preventDefault();
			const post_id = $(this).attr( 'id' );
			$('#myModal').modal('show')
			$.ajax({
				cache: false,
				timeout: 8000,
				url: php_array.admin_ajax,
				type: "POST",
				data: ({ action:'theme_post_example', id:post_id }),
				beforeSend: function() {					
					$( '#single-post-container' ).html( 'Loading' );
				},
				success: function( response,){
					$( '#single-post-container' ).html( $( response ) );
				},
				error: function( jqXHR, textStatus, errorThrown ){
					console.log( 'The following error occured: ' + textStatus, errorThrown );	
				}
			});
		});
	});
})(jQuery);
