/**
 * Journalist Portfolio Hub — Multimedia / Video Modal Lightbox
 * Version 1.4.0 (Supports YouTube, Facebook, Vimeo)
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
					'<iframe class="jp-lightbox-iframe" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="true"></iframe>' +
				'</div>';
			document.body.appendChild(lightbox);
		}

		var iframe = lightbox.querySelector('.jp-lightbox-iframe');
		var closeBtn = lightbox.querySelector('.jp-lightbox-close');

		function getEmbedUrl(url) {
			if (!url) return '';
			url = url.trim();

			// 1. YouTube Match
			var ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|shorts\/|watch\?.+&v=))([\w-]{11})/);
			if (ytMatch && ytMatch[1]) {
				return 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&rel=0';
			}

			// 2. Facebook Match
			if (url.indexOf('facebook.com') !== -1 || url.indexOf('fb.watch') !== -1 || url.indexOf('fb.gg') !== -1) {
				if (url.indexOf('facebook.com/plugins/video.php') !== -1) {
					return url;
				}
				return 'https://www.facebook.com/plugins/video.php?href=' + encodeURIComponent(url) + '&show_text=0&autoplay=true';
			}

			// 3. Vimeo Match
			var vimeoMatch = url.match(/vimeo\.com\/(?:video\/)?([0-9]+)/);
			if (vimeoMatch && vimeoMatch[1]) {
				return 'https://player.vimeo.com/video/' + vimeoMatch[1] + '?autoplay=1';
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
				if (videoUrl) {
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
