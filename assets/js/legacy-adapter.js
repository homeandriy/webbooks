export function initBootstrapBridge(bootstrap) {
	window.WebBooksBootstrap = {
		showModal(selector) {
			const modal = document.querySelector(selector);
			if (modal) {
				bootstrap.Modal.getOrCreateInstance(modal).show();
			}
		},
	};
}
