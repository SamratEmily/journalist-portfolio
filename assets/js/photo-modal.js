/**
 * Journalist Portfolio Hub — Photo Modal Lightbox with Published Link
 */
(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		var cards = document.querySelectorAll('.jp-photo-card[data-photo-url]');
		if (!cards.length) return;

		// Create Lightbox DOM structure if not present
		var lightbox = document.getElementById('jp-photo-modal');
		if (!lightbox) {
			lightbox = document.createElement('div');
			lightbox.id = 'jp-photo-modal';
			lightbox.className = 'jp-photo-lightbox';
			lightbox.innerHTML =
				'<div class="jp-photo-lightbox-container">' +
					'<button type="button" class="jp-photo-lightbox-close" aria-label="Close">&times;</button>' +
					'<div class="jp-photo-lightbox-body">' +
						'<img class="jp-photo-lightbox-img" src="" alt="">' +
						'<div class="jp-photo-lightbox-meta">' +
							'<span class="jp-photo-lightbox-tags"></span>' +
							'<h3 class="jp-photo-lightbox-title"></h3>' +
							'<p class="jp-photo-lightbox-desc"></p>' +
							'<div class="jp-photo-lightbox-pub-wrap" style="margin-top: 16px;">' +
								'<a class="jp-photo-modal-pub-btn" href="" target="_blank" rel="noopener noreferrer">' +
									'<span>View Published Article</span> ' +
									'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>' +
								'</a>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>';
			document.body.appendChild(lightbox);
		}

		var imgEl = lightbox.querySelector('.jp-photo-lightbox-img');
		var titleEl = lightbox.querySelector('.jp-photo-lightbox-title');
		var descEl = lightbox.querySelector('.jp-photo-lightbox-desc');
		var tagsEl = lightbox.querySelector('.jp-photo-lightbox-tags');
		var pubWrap = lightbox.querySelector('.jp-photo-lightbox-pub-wrap');
		var pubBtn = lightbox.querySelector('.jp-photo-modal-pub-btn');
		var closeBtn = lightbox.querySelector('.jp-photo-lightbox-close');

		function openModal(photoUrl, title, desc, tags, publishedUrl) {
			if (!photoUrl) return;
			imgEl.src = photoUrl;
			imgEl.alt = title || '';
			titleEl.textContent = title || '';
			descEl.textContent = desc || '';
			tagsEl.textContent = tags || '';
			tagsEl.style.display = tags ? 'inline-block' : 'none';
			descEl.style.display = desc ? 'block' : 'none';

			if (publishedUrl) {
				pubBtn.href = publishedUrl;
				pubWrap.style.display = 'block';
			} else {
				pubBtn.href = '';
				pubWrap.style.display = 'none';
			}
			
			lightbox.classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function closeModal() {
			lightbox.classList.remove('active');
			imgEl.src = '';
			document.body.style.overflow = '';
		}

		cards.forEach(function(card) {
			card.addEventListener('click', function(e) {
				// Don't trigger modal if user clicked directly on the pub button
				if (e.target.closest('.jp-photo-pub-btn')) {
					return;
				}
				e.preventDefault();
				var url = this.getAttribute('data-photo-url');
				var title = this.getAttribute('data-title');
				var desc = this.getAttribute('data-desc');
				var tags = this.getAttribute('data-tags');
				var publishedUrl = this.getAttribute('data-published-url');
				openModal(url, title, desc, tags, publishedUrl);
			});
		});

		closeBtn.addEventListener('click', closeModal);

		lightbox.addEventListener('click', function(e) {
			if (e.target === lightbox || e.target.classList.contains('jp-photo-lightbox-container')) {
				closeModal();
			}
		});

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && lightbox.classList.contains('active')) {
				closeModal();
			}
		});
	});
})();
