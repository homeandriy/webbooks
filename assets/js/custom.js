 jQuery(document).ready(function($) {
		var $preloader = $('#page-preloader'),
				$spinner   = $preloader.find('.spinner');
		$spinner.fadeOut();
		$preloader.delay(350).fadeOut('slow');

		$('.card-content').css('overflow-y', 'auto');
		$('.attachment-small-thumb').addClass('media-object')


		// $('.bdr-b ').delay(2500).fadeIn();

		// show modal for write
		$('#write').click(function(e) {
				e.preventDefault();
				$('#write-me').modal('show');
		});

		$('.page-numbers').addClass('pagination');
		$('.current').parent('li').addClass('active');

		$('.cat-item-7').addClass('treeview');
		$('.children').addClass('treeview-menu');

		$('iframe').attr('width', '');
		$('#pass1').addClass('form-control input-lg');
		$('#pass2').addClass('form-control input-lg')

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
						$('#myModal').modal('show');
						$( '#container_for_post' ).html( 'Loading' );
				},

				success: function( data, textStatus, jqXHR ){
						if (!data || !data.success) {
							return;
						}

						var $ajax_response = $( data.data.html );
						$( '#container_for_post' ).html('');
						$('#myModal').modal('show');

						$( '#container_for_post' ).append( $ajax_response );

				},

				error: function( jqXHR, textStatus, errorThrown ){
						console.log( 'The following error occured: ' + textStatus, errorThrown );
				},
			});
		});
	//resize card
	// if screen >1400px
	var selectClass = $('.content-loop>.sizes');
	var WidthOnLoad = $('.mrg-tb').width();

	function HasClass () {
		var NameClass;

		if (selectClass.hasClass('col-md-4')) {
				NameClass = 'col-md-4';

				return NameClass;
		};

		if (selectClass.hasClass('col-md-3')   ) {
				NameClass = 'col-md-3';
				return NameClass;
		};

		if (selectClass.hasClass('col-md-2')   ) {
				NameClass = 'col-md-2';
				return NameClass;
		};
	}


	function WidthScreen (Width) {
		var newClass;
		if (Width > 1400) {

			newClass = 'col-md-3';
			let HasNowClass = HasClass();

			selectClass.removeClass(HasNowClass);
			selectClass.addClass(newClass);

			$('.attachment-big-thumb').height(440);
		};


		if (Width < 1400) {
			newClass = 'col-md-4';
			let HasNowClass = HasClass();

			selectClass.removeClass(HasNowClass);
			selectClass.addClass(newClass);
			$('.attachment-big-thumb').height(355);

			// return newClass;
		};

		if (Width < 940) {
			newClass = 'col-md-6';


			let HasNowClass = HasClass();


			selectClass.removeClass(HasNowClass);
			selectClass.addClass(newClass);
			$('.attachment-big-thumb').height(370);
		};
	}
	$(window).on('resize', function () {
		WidthScreen($('.mrg-tb').width());
	});
	WidthScreen(WidthOnLoad);

	$(".rolled").roller({
		infinite: true,
		autoAdvance: !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
		fill:true,
		controls:false,
		pagination:false,
		paged:true,
	});
	$('.next-reed-content').css('overflow-y', 'auto');
});
