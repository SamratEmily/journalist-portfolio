/**
 * Live AJAX Search & Interactive Search UI for Journalist Portfolio Hub.
 *
 * @package JournalistPortfolio
 * @version 1.4.0
 */

document.addEventListener('DOMContentLoaded', function () {
	const searchInputs = document.querySelectorAll('.jp-search-input');
	if (!searchInputs.length) return;

	searchInputs.forEach(function (input) {
		const form = input.closest('.jp-search-form') || input.parentElement;
		let dropdown = form.querySelector('.jp-live-search-results');

		// Create live search dropdown if not existing
		if (!dropdown) {
			dropdown = document.createElement('div');
			dropdown.className = 'jp-live-search-results';
			dropdown.setAttribute('role', 'listbox');
			dropdown.setAttribute('aria-label', 'Live Search Results');
			form.appendChild(dropdown);
		}

		let debounceTimer = null;
		let activeIndex = -1;

		// Input listener for debounced live search
		input.addEventListener('input', function () {
			const query = input.value.trim();

			// Toggle clear button state
			const clearBtn = form.querySelector('.jp-search-clear-btn');
			if (clearBtn) {
				clearBtn.style.display = query.length ? 'flex' : 'none';
			}

			if (query.length < 2) {
				hideDropdown(dropdown);
				return;
			}

			showLoading(dropdown);

			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(function () {
				fetchLiveResults(query, dropdown);
			}, 280);
		});

		// Focus event: show dropdown if query exists
		input.addEventListener('focus', function () {
			if (input.value.trim().length >= 2 && dropdown.children.length > 0) {
				dropdown.classList.add('is-active');
			}
		});

		// Keyboard navigation inside input
		input.addEventListener('keydown', function (e) {
			if (!dropdown.classList.contains('is-active')) return;

			const items = dropdown.querySelectorAll('.jp-live-item-link');
			if (!items.length) return;

			if (e.key === 'ArrowDown') {
				e.preventDefault();
				activeIndex = (activeIndex + 1) % items.length;
				highlightItem(items, activeIndex);
			} else if (e.key === 'ArrowUp') {
				e.preventDefault();
				activeIndex = (activeIndex - 1 + items.length) % items.length;
				highlightItem(items, activeIndex);
			} else if (e.key === 'Enter') {
				if (activeIndex >= 0 && items[activeIndex]) {
					e.preventDefault();
					items[activeIndex].click();
				}
			} else if (e.key === 'Escape') {
				hideDropdown(dropdown);
				input.blur();
			}
		});
	});

	// Global Keyboard Shortcut: Cmd+K or Ctrl+K to focus search input
	document.addEventListener('keydown', function (e) {
		if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
			e.preventDefault();
			const firstInput = document.querySelector('.jp-search-input');
			if (firstInput) {
				firstInput.focus();
				firstInput.select();
			}
		}
	});

	// Close dropdown when clicking outside
	document.addEventListener('click', function (e) {
		if (!e.target.closest('.jp-search-form') && !e.target.closest('.jp-search-page-form')) {
			document.querySelectorAll('.jp-live-search-results').forEach(function (d) {
				d.classList.remove('is-active');
			});
		}
	});

	// Clear button setup
	document.querySelectorAll('.jp-search-clear-btn').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			const form = btn.closest('form');
			const input = form.querySelector('.jp-search-input') || form.querySelector('.jp-search-page-input');
			if (input) {
				input.value = '';
				btn.style.display = 'none';
				input.focus();
				const dropdown = form.querySelector('.jp-live-search-results');
				if (dropdown) hideDropdown(dropdown);
			}
		});
	});

	/**
	 * Fetch live search results via WP AJAX
	 */
	function fetchLiveResults(query, dropdown) {
		const params = new URLSearchParams({
			action: 'jp_live_search',
			s: query,
			nonce: jpSearchVars.nonce,
		});

		fetch(jpSearchVars.ajax_url + '?' + params.toString())
			.then(function (response) {
				return response.json();
			})
			.then(function (data) {
				if (data.success) {
					renderResults(data.data, query, dropdown);
				} else {
					renderError(dropdown);
				}
			})
			.catch(function () {
				renderError(dropdown);
			});
	}

	/**
	 * Render live search results HTML
	 */
	function renderResults(data, query, dropdown) {
		const results = data.results || [];
		const total = data.total || 0;

		dropdown.innerHTML = '';

		if (results.length === 0) {
			dropdown.innerHTML = `
				<div class="jp-live-empty">
					<span>${jpSearchVars.i18n.no_results}</span>
				</div>
			`;
			dropdown.classList.add('is-active');
			return;
		}

		let html = '<div class="jp-live-header"><span>STORIES MATCHING "' + escapeHTML(query) + '"</span></div>';
		html += '<div class="jp-live-list">';

		results.forEach(function (item) {
			const thumbHTML = item.thumb_url
				? `<img src="${item.thumb_url}" alt="${escapeHTML(item.title)}" class="jp-live-thumb">`
				: `<div class="jp-live-thumb-placeholder"><span class="dashicons dashicons-book-alt"></span></div>`;

			const metaText = [item.category, item.publisher, item.reading_time].filter(Boolean).join(' • ');

			html += `
				<a href="${item.permalink}" class="jp-live-item-link">
					${thumbHTML}
					<div class="jp-live-item-info">
						${item.kicker ? `<span class="jp-live-kicker">${escapeHTML(item.kicker)}</span>` : ''}
						<h4 class="jp-live-item-title">${escapeHTML(item.title)}</h4>
						<div class="jp-live-item-meta">${escapeHTML(metaText)}</div>
					</div>
				</a>
			`;
		});

		html += '</div>';

		if (total > 0) {
			const searchUrl = data.search_url || jpSearchVars.search_url + '?s=' + encodeURIComponent(query) + '&post_type=story';
			const viewAllText = jpSearchVars.i18n.view_all.replace('%d', total);

			html += `
				<a href="${searchUrl}" class="jp-live-footer-link">
					<span>${escapeHTML(viewAllText)}</span>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
			`;
		}

		dropdown.innerHTML = html;
		dropdown.classList.add('is-active');
	}

	function showLoading(dropdown) {
		dropdown.innerHTML = `
			<div class="jp-live-loading">
				<div class="jp-spinner"></div>
				<span>${jpSearchVars.i18n.searching}</span>
			</div>
		`;
		dropdown.classList.add('is-active');
	}

	function renderError(dropdown) {
		dropdown.innerHTML = `
			<div class="jp-live-empty">
				<span>Unable to load search results. Please try again.</span>
			</div>
		`;
		dropdown.classList.add('is-active');
	}

	function hideDropdown(dropdown) {
		dropdown.classList.remove('is-active');
		dropdown.innerHTML = '';
	}

	function highlightItem(items, index) {
		items.forEach(function (el, i) {
			if (i === index) {
				el.classList.add('is-selected');
				el.scrollIntoView({ block: 'nearest' });
			} else {
				el.classList.remove('is-selected');
			}
		});
	}

	function escapeHTML(str) {
		if (!str) return '';
		return str.replace(/[&<>"']/g, function (m) {
			return {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;',
			}[m];
		});
	}
});
