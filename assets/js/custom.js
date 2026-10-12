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

	$('.next-reed-content').css('overflow-y', 'auto');
});
