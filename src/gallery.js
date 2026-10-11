import '../assets/css/jquery.fancybox.css';
import '../assets/css/slick-theme.css';
import '../assets/css/slick.css';
import '../assets/css/gallery.css';
import '../assets/js/jquery.fancybox.js';
import '../assets/js/slick.js';
jQuery(function ($) {
	$('.book-gallery').each(function () {
		const $gallery = $(this);
		const $main = $gallery.find('.book-gallery-main');
		const $thumbs = $gallery.find('.book-gallery-thumbs');
		if ($.fn.slick && $thumbs.children().length > 3) {
			$thumbs.slick({infinite: false, dots: true, slidesToShow: 3, slidesToScroll: 3});
		}
		if ($main.length) {
			$main.wrap('<div class="book-gallery-zoom"></div>');
			const $frame = $main.parent();
			const updateZoom = function () {
				$frame.css('background-image', `url(${$main.attr('data-zoom-image') ? JSON.stringify($main.attr('data-zoom-image')) : 'none'})`);
			};
			updateZoom();
			$frame.on('pointermove', function (event) {
				if (event.pointerType === 'touch') return;
				const bounds = this.getBoundingClientRect();
				const x = Math.max(0, Math.min(100, (event.clientX - bounds.left) / bounds.width * 100));
				const y = Math.max(0, Math.min(100, (event.clientY - bounds.top) / bounds.height * 100));
				$frame.css('background-position', `${x}% ${y}%`);
			});
			$thumbs.on('click', 'a', function (event) {
				event.preventDefault();
				$main.attr('src', $(this).attr('data-image')).attr('data-zoom-image', $(this).attr('data-zoom-image')).removeAttr('srcset sizes');
				$main.attr('alt', $(this).find('img').attr('alt') || '');
				updateZoom();
			});
			$main.on('click', function () {
				if ($.fancybox) {
					$.fancybox($thumbs.find('a').toArray().map(link => ({href: link.getAttribute('href')})));
				}
			});
		}
	});
});
