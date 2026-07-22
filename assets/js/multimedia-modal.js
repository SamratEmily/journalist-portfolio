/**
 * Journalist Portfolio Hub — Multimedia Modal Lightbox
 * Version 1.3.0
 */
(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		var cards = document.querySelectorAll('.jp-multimedia-card[data-video-url]');
		if (!cards.length) return;

		// Create Lightbox DOM structure if not present
		var lightbox = document.getElementById('jp-multimedia-modal');
		if (!lightbox) {
			lightbox = document.createElement('div');
			lightbox.id = 'jp-multimedia-modal';
			lightbox.className = 'jp-multimedia-lightbox';
			lightbox.innerHTML =
				'<div class="jp-lightbox-container">' +
					'<button type="button" class="jp-lightbox-close" aria-label="Close">&times;</button>' +
					'<iframe class="jp-lightbox-iframe" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
				'</div>';
			document.body.appendChild(lightbox);
		}

		var iframe = lightbox.querySelector('.jp-lightbox-iframe');
		var closeBtn = lightbox.querySelector('.jp-lightbox-close');

		function getEmbedUrl(url) {
			if (!url) return '';
			var match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
			if (match && match[1]) {
				return 'https://www.youtube.com/embed/' + match[1] + '?autoplay=1&rel=0';
			}
			return url;
		}

		function openModal(videoUrl) {
			var embedUrl = getEmbedUrl(videoUrl);
			if (!embedUrl) return;
			iframe.src = embedUrl;
			lightbox.classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function closeModal() {
			lightbox.classList.remove('active');
			iframe.src = '';
			document.body.style.overflow = '';
		}

		cards.forEach(function(card) {
			card.addEventListener('click', function(e) {
				var videoUrl = this.getAttribute('data-video-url');
				if (videoUrl && (videoUrl.indexOf('youtube') !== -1 || videoUrl.indexOf('youtu.be') !== -1)) {
					e.preventDefault();
					openModal(videoUrl);
				}
			});
		});

		closeBtn.addEventListener('click', closeModal);

		lightbox.addEventListener('click', function(e) {
			if (e.target === lightbox) {
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
