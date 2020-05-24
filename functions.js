(function($) {
 
	/** jQuery Document Ready */
	$(document).ready(function(){
 
		$( 'a.ajax-post' ).off( 'click' ).on( 'click', function( e ) { 
 
			/** Prevent Default Behaviour */
			e.preventDefault();
 
			/** Get Post ID */
			var post_id = $(this).attr( 'id' );

			// show modal
			$('#myModal').modal('show')
 
			/** Ajax Call */
			$.ajax({
 
				cache: false,
				timeout: 8000,
				url: php_array.admin_ajax,
				type: "POST",
				data: ({ action:'theme_post_example', id:post_id }),
 
				beforeSend: function() {					
					$( '#single-post-container' ).html( 'Loading' );
				},
 
				success: function( data, textStatus, jqXHR ){
 
					var $ajax_response = $( data );
					console.log($ajax_response );														
					$( '#single-post-container' ).html( $ajax_response );
 
				},
 
				error: function( jqXHR, textStatus, errorThrown ){
					console.log( 'The following error occured: ' + textStatus, errorThrown );	
				},
 
				complete: function( jqXHR, textStatus ){
				}
 
			});
 
		});
 
	});
 
})(jQuery);