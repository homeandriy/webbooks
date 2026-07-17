const SELECTORS = Object.freeze({
	categoryTreeItem: '.cat-item-7',
	treeviewChildren: '.children',
	treeviews: '.sidebar .treeview',
	offcanvasToggles: "[data-webbooks-toggle='offcanvas']",
	offcanvasCloseButtons: '[data-webbooks-close-offcanvas]',
	offcanvasRows: '.row-offcanvas',
	leftSections: '.left-section',
	rightSections: '.right-section',
	wrapper: '.wrapper',
	header: 'body > .header',
	footer: 'body > footer',
	buttons: '.btn',
});
const OFFCANVAS_BREAKPOINT = 992;
const MOBILE_OFFCANVAS_BREAKPOINT = 767;

const initializeTreeviews = () => {
	document.querySelectorAll(SELECTORS.categoryTreeItem).forEach((item) => item.classList.add('treeview'));
	document.querySelectorAll(SELECTORS.treeviewChildren).forEach((children) => children.classList.add('treeview-menu'));

	document.querySelectorAll(SELECTORS.treeviews).forEach((treeview) => {
		const toggle = treeview.querySelector(':scope > a');
		const menu = treeview.querySelector(':scope > .treeview-menu');
		const icon = toggle?.querySelector('.fa-angle-left, .ion-arrow-down-b');

		if (!toggle || !menu) {
			return;
		}

		let isOpen = treeview.classList.contains('active');
		menu.hidden = !isOpen;
		menu.style.display = isOpen ? 'block' : '';
		toggle.setAttribute('aria-expanded', String(isOpen));
		icon?.classList.toggle('fa-angle-left', !isOpen);
		icon?.classList.toggle('ion-arrow-down-b', isOpen);

		toggle.addEventListener('click', (event) => {
			event.preventDefault();
			isOpen = !isOpen;
			menu.hidden = !isOpen;
			menu.style.display = isOpen ? 'block' : '';
			treeview.classList.toggle('active', isOpen);
			toggle.setAttribute('aria-expanded', String(isOpen));
			icon?.classList.toggle('fa-angle-left', !isOpen);
			icon?.classList.toggle('ion-arrow-down-b', isOpen);
		});

		menu.querySelectorAll('li > a').forEach((link) => {
			const currentMargin = Number.parseInt(window.getComputedStyle(link).marginLeft, 10) || 0;
			link.style.marginLeft = `${currentMargin + 10}px`;
		});
	});
};

const initializeOffcanvas = () => {
	const mobileSidebars = document.querySelectorAll(SELECTORS.leftSections);
	const mobileToggles = document.querySelectorAll(SELECTORS.offcanvasToggles);

	const setMobileOffcanvasState = (isOpen) => {
		mobileSidebars.forEach((sidebar) => {
			sidebar.classList.toggle('is-open', isOpen);
			sidebar.inert = !isOpen;
			sidebar.setAttribute('aria-hidden', String(!isOpen));
		});
		mobileToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', String(isOpen)));
		document.body.classList.toggle('webbooks-offcanvas-open', isOpen);
	};

	const closeMobileOffcanvas = () => setMobileOffcanvasState(false);
	const resetMobileOffcanvas = () => {
		mobileSidebars.forEach((sidebar) => {
			sidebar.classList.remove('is-open');
			sidebar.inert = false;
			sidebar.removeAttribute('aria-hidden');
		});
		mobileToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
		document.body.classList.remove('webbooks-offcanvas-open');
	};

	if (window.innerWidth <= MOBILE_OFFCANVAS_BREAKPOINT) {
		closeMobileOffcanvas();
	}

	document.querySelectorAll(SELECTORS.offcanvasToggles).forEach((toggleButton) => {
		toggleButton.addEventListener('click', (event) => {
			event.preventDefault();
			const isMobile = window.innerWidth <= OFFCANVAS_BREAKPOINT;

			if (window.innerWidth <= MOBILE_OFFCANVAS_BREAKPOINT) {
				setMobileOffcanvasState(!document.body.classList.contains('webbooks-offcanvas-open'));
				return;
			}

			if (isMobile) {
				document.querySelectorAll(SELECTORS.offcanvasRows).forEach((row) => {
					row.classList.toggle('active');
					row.classList.toggle('relative');
				});
				document.querySelectorAll(SELECTORS.leftSections).forEach((section) => section.classList.remove('collapse-left'));
				document.querySelectorAll(SELECTORS.rightSections).forEach((section) => section.classList.remove('strech'));

				return;
			}

			document.querySelectorAll(SELECTORS.leftSections).forEach((section) => section.classList.toggle('collapse-left'));
			document.querySelectorAll(SELECTORS.rightSections).forEach((section) => section.classList.toggle('strech'));
		});
	});

	document.addEventListener('keydown', (event) => {
		if ('Escape' === event.key && document.body.classList.contains('webbooks-offcanvas-open')) {
			closeMobileOffcanvas();
		}
	});

	document.querySelectorAll(SELECTORS.offcanvasCloseButtons).forEach((closeButton) => {
		closeButton.addEventListener('click', closeMobileOffcanvas);
	});

	mobileSidebars.forEach((sidebar) => {
		sidebar.querySelectorAll('a[href]').forEach((link) => {
			link.addEventListener('click', () => {
				if (window.innerWidth <= MOBILE_OFFCANVAS_BREAKPOINT && !link.closest('.treeview')) {
					closeMobileOffcanvas();
				}
			});
		});
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > MOBILE_OFFCANVAS_BREAKPOINT) {
			resetMobileOffcanvas();
		}
	});
};

const updatePageLayout = () => {
	const wrapper = document.querySelector(SELECTORS.wrapper);
	if (!wrapper) {
		return;
	}

	const contentHeight = Math.max(window.innerHeight, wrapper.scrollHeight);
	document.querySelectorAll(SELECTORS.leftSections).forEach((element) => {
		element.style.minHeight = `${contentHeight}px`;
	});
};

document.addEventListener('DOMContentLoaded', () => {
	initializeTreeviews();
	initializeOffcanvas();
	updatePageLayout();

	document.querySelectorAll(SELECTORS.buttons).forEach((button) => {
		button.addEventListener('touchstart', () => button.classList.add('hover'), { passive: true });
		button.addEventListener('touchend', () => button.classList.remove('hover'), { passive: true });
		button.addEventListener('touchcancel', () => button.classList.remove('hover'), { passive: true });
	});

	window.addEventListener('resize', updatePageLayout);
});
