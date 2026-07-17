const SELECTORS = Object.freeze({
	categoryTreeItem: '.cat-item-7',
	treeviewChildren: '.children',
	treeviews: '.sidebar .treeview',
	offcanvasToggles: "[data-webbooks-toggle='offcanvas']",
	offcanvasRows: '.row-offcanvas',
	leftSections: '.left-section',
	rightSections: '.right-section',
	wrapper: '.wrapper',
	header: 'body > .header',
	footer: 'body > footer',
	buttons: '.btn',
});
const OFFCANVAS_BREAKPOINT = 992;

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
	document.querySelectorAll(SELECTORS.offcanvasToggles).forEach((toggleButton) => {
		toggleButton.addEventListener('click', (event) => {
			event.preventDefault();
			const isMobile = window.innerWidth <= OFFCANVAS_BREAKPOINT;

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
