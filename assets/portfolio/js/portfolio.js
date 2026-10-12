/**
 * Native interactions for the portfolio page.
 *
 * Replaces the legacy jQuery, Skel, Scrolly, Scrollzer and util runtime.
 */
(() => {
	'use strict';

	const MOBILE_BREAKPOINT = 960;
	const ACTIVE_LINK_CLASS = 'active';
	const VISIBLE_HEADER_CLASS = 'header-visible';

	const getHashTarget = (link) => {
		const href = link.getAttribute('href');

		if (!href || !href.startsWith('#')) {
			return null;
		}

		return document.getElementById(href.slice(1));
	};

	const initializeLoadingState = () => {
		const { body } = document;

		body.classList.add('is-loading');
		window.addEventListener('load', () => body.classList.remove('is-loading'), { once: true });
	};

	const initializeScrollNavigation = () => {
		const navigationLinks = Array.from(document.querySelectorAll('#nav a[href^="#"], a.scrolly[href^="#"]'));
		const targetLinks = navigationLinks
			.map((link) => ({ link, target: getHashTarget(link) }))
			.filter(({ target }) => target);

		if (!targetLinks.length) {
			return;
		}

		const setActiveLink = (activeTarget) => {
			targetLinks.forEach(({ link, target }) => {
				link.classList.toggle(ACTIVE_LINK_CLASS, target === activeTarget);
			});
		};

		targetLinks.forEach(({ link, target }) => {
			link.addEventListener('click', (event) => {
				event.preventDefault();
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });
				history.replaceState(null, '', `#${target.id}`);
				setActiveLink(target);
			});
		});

		const observer = new IntersectionObserver(
			(entries) => {
				const activeEntry = entries
					.filter((entry) => entry.isIntersecting)
					.sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];

				if (activeEntry) {
					setActiveLink(activeEntry.target);
				}
			},
			{ rootMargin: '-20% 0px -60%', threshold: [0.1, 0.4, 0.7] }
		);

		targetLinks.forEach(({ target }) => observer.observe(target));

		const initialTarget = document.getElementById(window.location.hash.slice(1)) ?? targetLinks[0].target;
		setActiveLink(initialTarget);
	};

	const initializeMobileHeader = () => {
		const header = document.getElementById('header');

		if (!header) {
			return;
		}

		const toggleContainer = document.createElement('div');
		toggleContainer.id = 'headerToggle';

		const toggle = document.createElement('button');
		toggle.type = 'button';
		toggle.className = 'toggle';
		toggle.setAttribute('aria-controls', 'header');
		toggle.setAttribute('aria-expanded', 'false');
		toggle.setAttribute('aria-label', 'Open navigation');

		toggleContainer.append(toggle);
		document.body.append(toggleContainer);

		const closeHeader = () => {
			document.body.classList.remove(VISIBLE_HEADER_CLASS);
			toggle.setAttribute('aria-expanded', 'false');
		};

		toggle.addEventListener('click', () => {
			const isVisible = document.body.classList.toggle(VISIBLE_HEADER_CLASS);
			toggle.setAttribute('aria-expanded', String(isVisible));
		});

		header.querySelectorAll('a').forEach((link) => {
			link.addEventListener('click', () => {
				if (window.innerWidth <= MOBILE_BREAKPOINT) {
					closeHeader();
				}
			});
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				closeHeader();
			}
		});

		window.matchMedia(`(min-width: ${MOBILE_BREAKPOINT + 1}px)`).addEventListener('change', closeHeader);
	};

	document.addEventListener('DOMContentLoaded', () => {
		initializeLoadingState();
		initializeScrollNavigation();
		initializeMobileHeader();
	});
})();
