 jQuery(document).ready(function($) {
		var $preloader = $('#page-preloader'),
				$spinner   = $preloader.find('.spinner');
		$spinner.fadeOut();
		$preloader.delay(350).fadeOut('slow');

		$('.card-content').css('overflow-y', 'auto');
		$('.attachment-small-thumb').addClass('img-fluid')


		// $('.bdr-b ').delay(2500).fadeIn();

		$('.cat-item-7').addClass('treeview');
		$('.children').addClass('treeview-menu');

		$('iframe').attr('width', '');
		$('#pass1').addClass('form-control form-control-lg');
		$('#pass2').addClass('form-control form-control-lg')

		$( '.content-loop' ).on( 'click','.load-post', function( e ) {
			e.preventDefault();

			/** Get Post ID */
			let post_id = $(this).attr( 'id' );

			/** Ajax Call */
			$.ajax({
				cache: false,
				timeout: 8000,
				url: webbooksConfig.admin_ajax,
				type: "POST",
				dataType: 'json',
				data: ({ action:'theme_post_example', id:post_id, nonce: webbooksConfig.nonce }),

				beforeSend: function() {
						window.WebBooksUI.showModal('#myModal');
						$( '#container_for_post' ).html( 'Loading' );
				},

				success: function( data, textStatus, jqXHR ){
						if (!data || !data.success) {
							return;
						}

						var $ajax_response = $( data.data.html );
						$( '#container_for_post' ).html('');
						window.WebBooksUI.showModal('#myModal');

						$( '#container_for_post' ).append( $ajax_response );

				},

				error: function( jqXHR, textStatus, errorThrown ){
						console.log( 'The following error occured: ' + textStatus, errorThrown );
				},
			});
		});
	$('.next-reed-content').css('overflow-y', 'auto');
});
