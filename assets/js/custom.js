import { Fancybox } from '@fancyapps/ui/dist/fancybox/';
import $ from 'jquery';

const SELECTORS = Object.freeze({
	gallerySlider: '#gallery_01',
	featuredSlider: '.featured-slider',
	bookGallery: '.js-book-gallery',
	bookGalleryMain: '.js-book-gallery-main',
	bookGalleryItem: '.js-book-gallery-item',
	cardColumns: '.content-loop > .sizes',
	cardGrid: '.mrg-tb',
	cardImages: '.attachment-big-thumb',
	contentBlocks: '.content_block',
	contentBlockLists: '.content_block > .list-group',
	wrapper: '.wrapper',
});
const GALLERY_SLICK_OPTIONS = Object.freeze({
	infinite: true,
	dots: true,
	slidesToShow: 3,
	slidesToScroll: 3,
});
const FEATURED_SLICK_OPTIONS = Object.freeze({
	infinite: true,
	autoplay: true,
	autoplaySpeed: 8000,
	arrows: false,
	dots: false,
	slidesToShow: 4,
	slidesToScroll: 1,
	responsive: [
		{ breakpoint: 1200, settings: { slidesToShow: 3 } },
		{ breakpoint: 992, settings: { slidesToShow: 2 } },
		{ breakpoint: 576, settings: { slidesToShow: 1 } },
	],
});
const CARD_LAYOUT = Object.freeze({
	largeBreakpoint: 1400,
	smallBreakpoint: 940,
	gridClasses: ['col-md-2', 'col-md-3', 'col-md-4', 'col-md-6'],
});

const initializeBookGallery = (gallery) => {
	const mainTrigger = gallery.querySelector(SELECTORS.bookGalleryMain);
	const mainImage = mainTrigger?.querySelector('img');
	const galleryItems = Array.from(gallery.querySelectorAll(SELECTORS.bookGalleryItem));
	let currentIndex = 0;

	if (!mainTrigger || !mainImage || !galleryItems.length) {
		return;
	}

	const getSlides = () => galleryItems.map((item) => ({
		src: item.href,
		thumb: item.querySelector('img'),
		caption: item.dataset.caption ?? '',
	}));

	mainTrigger.addEventListener('click', (event) => {
		event.preventDefault();

		Fancybox.show(getSlides(), {
			startIndex: currentIndex,
			triggerEl: mainImage,
		});
	});

	galleryItems.forEach((item, index) => {
		item.addEventListener('click', (event) => {
			event.preventDefault();

			const thumbnail = item.querySelector('img');
			mainImage.src = item.dataset.previewSrc ?? item.href;
			mainImage.alt = thumbnail?.alt ?? '';
			currentIndex = index;

			galleryItems.forEach((galleryItem) => galleryItem.classList.remove('active'));
			item.classList.add('active');
		});
	});
};

const initializeSlick = () => {
	const $gallerySlider = $(SELECTORS.gallerySlider);
	if ($gallerySlider.length && !$gallerySlider.hasClass('slick-initialized')) {
		$gallerySlider.slick(GALLERY_SLICK_OPTIONS);
	}

	$(SELECTORS.featuredSlider).not('.slick-initialized').slick(FEATURED_SLICK_OPTIONS);
};

const updateCardLayout = () => {
	const cardColumns = document.querySelectorAll(SELECTORS.cardColumns);
	const gridWidth = document.querySelector(SELECTORS.cardGrid)?.clientWidth ?? 0;
	const imageHeight = gridWidth > CARD_LAYOUT.largeBreakpoint ? 440 : gridWidth < CARD_LAYOUT.smallBreakpoint ? 370 : 355;
	const columnClass = gridWidth > CARD_LAYOUT.largeBreakpoint ? 'col-md-3' : gridWidth < CARD_LAYOUT.smallBreakpoint ? 'col-md-6' : 'col-md-4';

	cardColumns.forEach((column) => {
		column.classList.remove(...CARD_LAYOUT.gridClasses);
		column.classList.add(columnClass);
	});

	document.querySelectorAll(SELECTORS.cardImages).forEach((image) => {
		image.style.height = `${imageHeight}px`;
	});
};

const updateContentBlockHeight = () => {
	const contentBlocks = document.querySelectorAll(SELECTORS.contentBlocks);
	const listGroups = Array.from(document.querySelectorAll(SELECTORS.contentBlockLists));
	const wrapper = document.querySelector(SELECTORS.wrapper);
	const maxHeight = Math.max(0, ...listGroups.map((listGroup) => listGroup.getBoundingClientRect().height));

	contentBlocks.forEach((contentBlock) => {
		contentBlock.style.height = `${maxHeight}px`;
	});

	if (wrapper) {
		document.body.style.height = `${wrapper.getBoundingClientRect().height}px`;
	}
};

document.addEventListener('DOMContentLoaded', () => {
	initializeSlick();
	document.querySelectorAll(SELECTORS.bookGallery).forEach(initializeBookGallery);

	const $preloader = $('#page-preloader');
	if ($preloader.length) {
		$preloader.find('.spinner').fadeOut();
		$preloader.delay(350).fadeOut('slow');
	}

	document.querySelectorAll('.attachment-small-thumb').forEach((thumbnail) => thumbnail.classList.add('media-object'));
	document.querySelectorAll('.page-numbers').forEach((pageNumber) => pageNumber.classList.add('pagination'));
	document.querySelectorAll('.current').forEach((current) => current.parentElement?.classList.add('active'));
	document.querySelectorAll('.cat-item-7').forEach((item) => item.classList.add('treeview'));
	document.querySelectorAll('.children').forEach((children) => children.classList.add('treeview-menu'));
	document.querySelectorAll('iframe').forEach((frame) => frame.removeAttribute('width'));
	document.querySelectorAll('#pass1, #pass2').forEach((input) => input.classList.add('form-control', 'input-lg'));

	document.querySelector('#write')?.addEventListener('click', (event) => {
		event.preventDefault();
		window.WebBooksBootstrap?.showModal('#write-me');
	});

	updateCardLayout();
	updateContentBlockHeight();

	let resizeFrame = 0;
	window.addEventListener('resize', () => {
		window.cancelAnimationFrame(resizeFrame);
		resizeFrame = window.requestAnimationFrame(() => {
			updateCardLayout();
			updateContentBlockHeight();
		});
	});
});
