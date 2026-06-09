(function () {
	'use strict';

	/**
	 * Normalise une chaîne pour la recherche : sans accents, en minuscules.
	 */
	function normalize(s) {
		return String(s || '')
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, "")
			.toLowerCase();
	}

	function debounce(fn, wait) {
		var t = null;
		return function () {
			var ctx = this;
			var args = arguments;
			clearTimeout(t);
			t = setTimeout(function () { fn.apply(ctx, args); }, wait);
		};
	}

	function readData(wrapper) {
		var holder = wrapper.querySelector('.mrz-display-post-exp-data');
		if (!holder) { return null; }
		try {
			return JSON.parse(holder.textContent);
		} catch (e) {
			return null;
		}
	}

	/**
	 * Slider natif (scroll-snap). Retourne une API { refresh }.
	 */
	function initSlider(sliderEl, track) {
		var perView = parseInt(sliderEl.getAttribute('data-per-view'), 10) || 1;
		var perViewTablet = parseInt(sliderEl.getAttribute('data-per-view-tablet'), 10) || perView;
		var perViewMobile = parseInt(sliderEl.getAttribute('data-per-view-mobile'), 10) || 1;
		var gap = parseInt(sliderEl.getAttribute('data-gap'), 10);
		if (isNaN(gap)) { gap = 16; }
		var loop = sliderEl.getAttribute('data-loop') === '1';
		var autoplay = sliderEl.getAttribute('data-autoplay') === '1';
		var autoplayDelay = parseInt(sliderEl.getAttribute('data-autoplay-delay'), 10) || 4000;

		var prevBtn = sliderEl.querySelector('.mrz-display-post-exp-slider-prev');
		var nextBtn = sliderEl.querySelector('.mrz-display-post-exp-slider-next');
		var dotsEl = sliderEl.querySelector('.mrz-display-post-exp-dots');

		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var autoplayTimer = null;
		var animFrame = null;
		var scrollDuration = parseInt(sliderEl.getAttribute('data-speed'), 10);
		if (isNaN(scrollDuration)) { scrollDuration = 500; }

		function currentPerView() {
			if (window.matchMedia('(max-width: 768px)').matches) { return perViewMobile; }
			if (window.matchMedia('(max-width: 1024px)').matches) { return perViewTablet; }
			return perView;
		}

		function applyVars() {
			sliderEl.style.setProperty('--mrz-per-view', String(currentPerView()));
			sliderEl.style.setProperty('--mrz-gap', gap + 'px');
		}

		function pageCount() {
			var count = track.children.length;
			var pv = currentPerView();
			return Math.max(1, Math.ceil(count / pv));
		}

		function currentPage() {
			var w = track.clientWidth;
			if (w <= 0) { return 0; }
			return Math.round(track.scrollLeft / w);
		}

		function updateArrows() {
			if (!prevBtn && !nextBtn) { return; }
			var maxScroll = track.scrollWidth - track.clientWidth - 1;
			var atStart = track.scrollLeft <= 1;
			var atEnd = track.scrollLeft >= maxScroll;
			if (prevBtn) { prevBtn.disabled = !loop && atStart; }
			if (nextBtn) { nextBtn.disabled = !loop && atEnd; }
		}

		function updateDots() {
			if (!dotsEl) { return; }
			var pages = pageCount();
			// (Re)génère les puces si le nombre a changé.
			if (dotsEl.children.length !== pages) {
				dotsEl.innerHTML = '';
				for (var i = 0; i < pages; i++) {
					var b = document.createElement('button');
					b.type = 'button';
					b.setAttribute('role', 'tab');
					b.setAttribute('aria-label', String(i + 1));
					(function (idx) {
						b.addEventListener('click', function () { goToPage(idx); });
					})(i);
					dotsEl.appendChild(b);
				}
			}
			var active = currentPage();
			Array.prototype.forEach.call(dotsEl.children, function (dot, idx) {
				dot.setAttribute('aria-current', idx === active ? 'true' : 'false');
			});
		}

		// Défilement programmatique adouci (easing ease-in-out). Le snap est
		// désactivé le temps de l'animation pour ne pas entrer en conflit, puis
		// rétabli une fois arrivé pile sur un point d'aimantation.
		function smoothScrollTo(targetLeft) {
			if (animFrame) {
				cancelAnimationFrame(animFrame);
				animFrame = null;
				track.style.scrollSnapType = '';
			}
			var maxScroll = track.scrollWidth - track.clientWidth;
			targetLeft = Math.max(0, Math.min(targetLeft, maxScroll));
			var start = track.scrollLeft;
			var dist = targetLeft - start;
			if (Math.abs(dist) < 1) { return; }
			if (reduceMotion) { track.scrollLeft = targetLeft; return; }

			var startTime = null;
			track.style.scrollSnapType = 'none';
			function easeInOutCubic(t) {
				return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
			}
			function step(ts) {
				if (startTime === null) { startTime = ts; }
				var t = Math.min(1, (ts - startTime) / scrollDuration);
				track.scrollLeft = start + dist * easeInOutCubic(t);
				if (t < 1) {
					animFrame = requestAnimationFrame(step);
				} else {
					track.scrollLeft = targetLeft;
					track.style.scrollSnapType = '';
					animFrame = null;
				}
			}
			animFrame = requestAnimationFrame(step);
		}

		function goToPage(idx) {
			smoothScrollTo(idx * track.clientWidth);
		}

		function next() {
			var maxScroll = track.scrollWidth - track.clientWidth - 1;
			if (loop && track.scrollLeft >= maxScroll) {
				smoothScrollTo(0);
			} else {
				smoothScrollTo(track.scrollLeft + track.clientWidth);
			}
		}

		function prev() {
			if (loop && track.scrollLeft <= 1) {
				smoothScrollTo(track.scrollWidth);
			} else {
				smoothScrollTo(track.scrollLeft - track.clientWidth);
			}
		}

		function startAutoplay() {
			if (!autoplay || reduceMotion || autoplayTimer) { return; }
			autoplayTimer = setInterval(next, autoplayDelay);
		}

		function stopAutoplay() {
			if (autoplayTimer) {
				clearInterval(autoplayTimer);
				autoplayTimer = null;
			}
		}

		if (nextBtn) { nextBtn.addEventListener('click', next); }
		if (prevBtn) { prevBtn.addEventListener('click', prev); }

		track.addEventListener('scroll', debounce(function () {
			updateArrows();
			updateDots();
		}, 80));

		window.addEventListener('resize', debounce(function () {
			applyVars();
			updateArrows();
			updateDots();
		}, 150));

		if (autoplay && !reduceMotion) {
			['mouseenter', 'focusin', 'pointerdown'].forEach(function (ev) {
				sliderEl.addEventListener(ev, stopAutoplay);
			});
			['mouseleave', 'focusout'].forEach(function (ev) {
				sliderEl.addEventListener(ev, startAutoplay);
			});
		}

		function refresh() {
			if (animFrame) {
				cancelAnimationFrame(animFrame);
				animFrame = null;
				track.style.scrollSnapType = '';
			}
			applyVars();
			track.scrollLeft = 0;
			updateArrows();
			updateDots();
			stopAutoplay();
			startAutoplay();
		}

		applyVars();
		return { refresh: refresh };
	}

	function initInstance(wrapper) {
		var data = readData(wrapper);
		if (!data || !data.config) { return; }

		var config = data.config;
		var items = data.items || [];
		var listEl = wrapper.querySelector('.mrz-display-post-exp-list');
		if (!listEl) { return; }

		// Logique OU/ET par filtre, déduite des filtres déclarés.
		var filterLogic = { tax: {}, acf: {} };
		(data.filters || []).forEach(function (f) {
			if (f.type === 'tax') { filterLogic.tax[f.taxonomy] = f.logic || 'or'; }
			else if (f.type === 'acf') { filterLogic.acf[f.field] = f.logic || 'or'; }
		});

		var currentFilters = { tax: {}, acf: {} };
		var searchQuery = '';
		var page = 1;

		var searchEnabled = config.search && config.search.enabled;

		// Index de recherche (titre + champs ACF texte choisis), normalisé.
		var searchIndex = {};
		if (searchEnabled) {
			items.forEach(function (it) {
				searchIndex[it.id] = normalize((it.title || '') + ' ' + (it.searchText || ''));
			});
		}

		// Slider éventuel.
		var sliderApi = null;
		if (config.format === 'slider') {
			var sliderEl = wrapper.querySelector('.mrz-display-post-exp-slider');
			if (sliderEl) { sliderApi = initSlider(sliderEl, listEl); }
		}

		// Pagination éventuelle (liste / grille).
		var perPage = parseInt(config.perPage, 10) || 0;
		var paginationEl = wrapper.querySelector('.mrz-display-post-exp-pagination');

		function toIntArray(arr) {
			return arr.map(function (v) { return parseInt(v, 10); }).filter(function (v) { return !isNaN(v); });
		}

		function passesFilters(item) {
			// Taxonomies.
			for (var slug in currentFilters.tax) {
				if (!currentFilters.tax.hasOwnProperty(slug)) { continue; }
				var selected = currentFilters.tax[slug];
				if (!selected || !selected.length) { continue; }
				var itemTerms = (item.terms && item.terms[slug]) || [];
				var logic = filterLogic.tax[slug] || 'or';
				if (logic === 'and') {
					for (var i = 0; i < selected.length; i++) {
						if (itemTerms.indexOf(selected[i]) === -1) { return false; }
					}
				} else {
					var anyTax = false;
					for (var j = 0; j < selected.length; j++) {
						if (itemTerms.indexOf(selected[j]) !== -1) { anyTax = true; break; }
					}
					if (!anyTax) { return false; }
				}
			}

			// Champs ACF.
			for (var field in currentFilters.acf) {
				if (!currentFilters.acf.hasOwnProperty(field)) { continue; }
				var sel = currentFilters.acf[field];
				if (!sel || !sel.length) { continue; }
				var raw = item.acfValues ? item.acfValues[field] : undefined;
				var vals = Array.isArray(raw) ? raw.map(String) : (raw != null ? [String(raw)] : []);
				var alogic = filterLogic.acf[field] || 'or';
				if (alogic === 'and') {
					for (var k = 0; k < sel.length; k++) {
						if (vals.indexOf(sel[k]) === -1) { return false; }
					}
				} else {
					var anyAcf = false;
					for (var l = 0; l < sel.length; l++) {
						if (vals.indexOf(sel[l]) !== -1) { anyAcf = true; break; }
					}
					if (!anyAcf) { return false; }
				}
			}

			// Recherche texte.
			if (searchQuery !== '') {
				var hay = searchIndex[item.id] || '';
				if (hay.indexOf(searchQuery) === -1) { return false; }
			}

			return true;
		}

		function buildItemEl(item) {
			var el;
			if (config.itemClickAction === 'link' && item.url) {
				el = document.createElement('a');
				el.href = item.url;
				el.className = 'mrz-display-post-exp-item-wrap mrz-display-post-exp-item-link';
			} else {
				el = document.createElement('div');
				el.className = 'mrz-display-post-exp-item-wrap';
			}
			el.innerHTML = item.html || '';
			return el;
		}

		function renderList(visible) {
			listEl.innerHTML = '';

			if (!visible.length) {
				var empty = document.createElement('div');
				empty.className = 'mrz-display-post-exp-empty';
				empty.textContent = (config.i18n && config.i18n.empty) || 'Aucun résultat.';
				listEl.appendChild(empty);
				if (paginationEl) { paginationEl.hidden = true; }
				if (sliderApi) { sliderApi.refresh(); }
				return;
			}

			var slice = visible;
			// Pagination uniquement en liste / grille.
			if (perPage > 0 && config.format !== 'slider') {
				var totalPages = Math.max(1, Math.ceil(visible.length / perPage));
				if (page > totalPages) { page = totalPages; }
				var start = (page - 1) * perPage;
				slice = visible.slice(start, start + perPage);
				updatePagination(totalPages);
			} else if (paginationEl) {
				paginationEl.hidden = true;
			}

			slice.forEach(function (item) {
				listEl.appendChild(buildItemEl(item));
			});

			if (sliderApi) { sliderApi.refresh(); }
		}

		function updatePagination(totalPages) {
			if (!paginationEl) { return; }
			paginationEl.hidden = totalPages <= 1;
			var cur = paginationEl.querySelector('.mrz-display-post-exp-page-current');
			var tot = paginationEl.querySelector('.mrz-display-post-exp-page-total');
			var prev = paginationEl.querySelector('.mrz-display-post-exp-page-prev');
			var next = paginationEl.querySelector('.mrz-display-post-exp-page-next');
			if (cur) { cur.textContent = String(page); }
			if (tot) { tot.textContent = String(totalPages); }
			if (prev) { prev.disabled = page <= 1; }
			if (next) { next.disabled = page >= totalPages; }
		}

		function applyFilters(opts) {
			opts = opts || {};
			if (!opts.keepPage) { page = 1; }
			var visible = items.filter(passesFilters);
			renderList(visible);
			if (config.urlFilters) { writeUrl(); }
		}

		// --- URL sync ---
		var urlPrefix = 'dpe_' + data.id + '_';

		function readUrl() {
			if (!config.urlFilters || typeof URLSearchParams === 'undefined') { return; }
			var params = new URLSearchParams(window.location.search);
			params.forEach(function (value, key) {
				if (key.indexOf(urlPrefix) !== 0) { return; }
				var rest = key.slice(urlPrefix.length);
				var parts = value.split(',').filter(Boolean);
				if (rest.indexOf('tax_') === 0) {
					currentFilters.tax[rest.slice(4)] = toIntArray(parts);
				} else if (rest.indexOf('acf_') === 0) {
					currentFilters.acf[rest.slice(4)] = parts.map(String);
				}
			});
		}

		function writeUrl() {
			if (typeof URLSearchParams === 'undefined' || !window.history || !window.history.replaceState) { return; }
			var params = new URLSearchParams(window.location.search);
			// Purge les clés de cette instance.
			var toDelete = [];
			params.forEach(function (v, key) {
				if (key.indexOf(urlPrefix) === 0) { toDelete.push(key); }
			});
			toDelete.forEach(function (key) { params.delete(key); });

			Object.keys(currentFilters.tax).forEach(function (slug) {
				var sel = currentFilters.tax[slug];
				if (sel && sel.length) { params.set(urlPrefix + 'tax_' + slug, sel.join(',')); }
			});
			Object.keys(currentFilters.acf).forEach(function (field) {
				var sel = currentFilters.acf[field];
				if (sel && sel.length) { params.set(urlPrefix + 'acf_' + field, sel.join(',')); }
			});

			var qs = params.toString();
			var newUrl = window.location.pathname + (qs ? '?' + qs : '') + window.location.hash;
			window.history.replaceState(null, '', newUrl);
		}

		// --- Inputs de filtre ---
		function valueFor(input, type) {
			var v = input.value;
			return type === 'tax' ? parseInt(v, 10) : String(v);
		}

		function bindFilterInputs() {
			wrapper.querySelectorAll('.mrz-display-post-exp-filter-input').forEach(function (input) {
				var type = input.getAttribute('data-filter-type');
				var key = type === 'tax' ? input.getAttribute('data-taxonomy') : input.getAttribute('data-field');
				if (!key) { return; }

				function syncFromInput() {
					var bucket = currentFilters[type];
					if (input.tagName === 'SELECT' || input.type === 'radio') {
						var val = input.value;
						if (val === '') { delete bucket[key]; }
						else { bucket[key] = [valueFor(input, type)]; }
					} else if (input.type === 'checkbox') {
						var groupInputs = wrapper.querySelectorAll(
							'.mrz-display-post-exp-filter-input[data-filter-type="' + type + '"]' +
							(type === 'tax' ? '[data-taxonomy="' + key + '"]' : '[data-field="' + key + '"]')
						);
						var selected = [];
						groupInputs.forEach(function (cb) {
							if (cb.checked) { selected.push(valueFor(cb, type)); }
						});
						if (selected.length) { bucket[key] = selected; }
						else { delete bucket[key]; }
					}
				}

				input.addEventListener('change', function () {
					syncFromInput();
					applyFilters();
				});

				// État initial (filtre forcé, restauration URL → coché au rendu).
				if ((input.tagName === 'SELECT' && input.value !== '') ||
					((input.type === 'radio' || input.type === 'checkbox') && input.checked)) {
					syncFromInput();
				}
			});
		}

		function syncInputsFromState() {
			wrapper.querySelectorAll('.mrz-display-post-exp-filter-input').forEach(function (input) {
				var type = input.getAttribute('data-filter-type');
				var key = type === 'tax' ? input.getAttribute('data-taxonomy') : input.getAttribute('data-field');
				if (!key) { return; }
				var sel = currentFilters[type][key] || [];
				if (input.tagName === 'SELECT') {
					input.value = sel.length ? String(sel[0]) : '';
				} else if (input.type === 'radio') {
					input.checked = (input.value === '' && !sel.length) || (sel.length === 1 && String(sel[0]) === input.value);
				} else if (input.type === 'checkbox') {
					input.checked = sel.map(String).indexOf(input.value) !== -1;
				}
			});
		}

		// --- Recherche ---
		var searchInput = wrapper.querySelector('.mrz-display-post-exp-search');
		if (searchInput) {
			searchInput.addEventListener('input', debounce(function () {
				searchQuery = normalize(searchInput.value);
				applyFilters();
			}, 200));
		}

		// --- Bouton réinitialiser ---
		var clearBtn = wrapper.querySelector('.mrz-display-post-exp-search-clear');
		if (clearBtn) {
			clearBtn.addEventListener('click', function () {
				currentFilters = { tax: {}, acf: {} };
				searchQuery = '';
				if (searchInput) { searchInput.value = ''; }
				wrapper.querySelectorAll('.mrz-display-post-exp-filter-input').forEach(function (input) {
					if (input.tagName === 'SELECT') { input.value = ''; }
					else if (input.type === 'radio') { input.checked = input.value === ''; }
					else if (input.type === 'checkbox') { input.checked = false; }
				});
				applyFilters();
			});
		}

		// --- Pagination (liste / grille) ---
		if (paginationEl) {
			var prevBtn = paginationEl.querySelector('.mrz-display-post-exp-page-prev');
			var nextBtn = paginationEl.querySelector('.mrz-display-post-exp-page-next');
			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					if (page > 1) { page--; applyFilters({ keepPage: true }); }
				});
			}
			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					page++; applyFilters({ keepPage: true });
				});
			}
		}

		// --- Toggle filtres mobile ---
		var filtersBlock = wrapper.querySelector('.mrz-display-post-exp-filters');
		var filtersToggle = wrapper.querySelector('.mrz-display-post-exp-filters-toggle');
		if (filtersBlock && filtersToggle) {
			filtersToggle.addEventListener('click', function () {
				var isOpen = filtersBlock.classList.toggle('is-open');
				filtersToggle.setAttribute('aria-expanded', String(isOpen));
			});
		}

		// Init.
		readUrl();
		bindFilterInputs();
		if (config.urlFilters) { syncInputsFromState(); }
		applyFilters();
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-mrz-display-post-exp]').forEach(initInstance);
	});
})();
