(function () {
	'use strict';

	function currentPostType() {
		var sel = document.getElementById('mrz_display_post_exp_source_pt');
		return sel ? sel.value : '';
	}

	// Masque les taxonomies qui ne sont pas liées au post type source choisi.
	function updateTaxonomyVisibility() {
		var pt = currentPostType();
		var rows = document.querySelectorAll('.mrz-display-post-exp-taxo-row');
		rows.forEach(function (row) {
			var types = (row.getAttribute('data-object-types') || '').split(',');
			var visible = types.indexOf(pt) !== -1;
			row.classList.toggle('is-hidden', !visible);
			if (!visible) {
				var cb = row.querySelector('input[type="checkbox"]');
				if (cb) { cb.checked = false; }
			}
		});
	}

	// Affiche les réglages propres au format choisi (grille / slider).
	function updateFormatVisibility() {
		var checked = document.querySelector('.mrz-display-post-exp-format-input:checked');
		var format = checked ? checked.value : 'list';
		document.querySelectorAll('.mrz-display-post-exp-when-grid').forEach(function (el) {
			el.style.display = format === 'grid' ? '' : 'none';
		});
		document.querySelectorAll('.mrz-display-post-exp-when-slider').forEach(function (el) {
			el.style.display = format === 'slider' ? '' : 'none';
		});
	}

	// Le sélecteur OU/ET n'a de sens qu'en mode « Cases à cocher ». On le masque
	// pour les autres modes, en ligne taxonomie et en ligne filtre ACF.
	function wireLogicToggle(row, modeSelector, logicElementResolver) {
		var modeSel = row.querySelector(modeSelector);
		if (!modeSel) { return; }
		var target = logicElementResolver(row);
		if (!target) { return; }
		function update() {
			target.style.display = modeSel.value === 'checkbox' ? '' : 'none';
		}
		modeSel.addEventListener('change', update);
		update();
	}

	function initAcfRepeater() {
		var wrap = document.querySelector('.mrz-display-post-exp-acf-filters');
		var tpl = document.getElementById('mrz-display-post-exp-acf-row-template');
		var addBtn = document.querySelector('.mrz-display-post-exp-acf-add');
		if (!wrap || !tpl || !addBtn) { return; }

		function bindRow(row) {
			var removeBtn = row.querySelector('.mrz-display-post-exp-acf-remove');
			if (removeBtn) {
				removeBtn.addEventListener('click', function (e) {
					e.preventDefault();
					row.remove();
				});
			}
			wireLogicToggle(
				row,
				'select[name*="[mode]"]',
				function (r) {
					var logicSel = r.querySelector('select[name*="[logic]"]');
					return logicSel ? logicSel.closest('.mrz-display-post-exp-acf-col') : null;
				}
			);
		}

		wrap.querySelectorAll('.mrz-display-post-exp-acf-row').forEach(bindRow);

		addBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var nextIndex = parseInt(wrap.getAttribute('data-next-index') || '0', 10);
			var html = tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex));
			var temp = document.createElement('div');
			temp.innerHTML = html.trim();
			var newRow = temp.firstChild;
			wrap.appendChild(newRow);
			bindRow(newRow);
			wrap.setAttribute('data-next-index', String(nextIndex + 1));
		});
	}

	function initTaxoLogicToggle() {
		document.querySelectorAll('.mrz-display-post-exp-taxo-row').forEach(function (row) {
			wireLogicToggle(
				row,
				'.mrz-display-post-exp-taxo-mode',
				function (r) {
					var logicSel = r.querySelector('.mrz-display-post-exp-taxo-logic');
					return logicSel ? logicSel.closest('.mrz-display-post-exp-taxo-col') : null;
				}
			);
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		updateTaxonomyVisibility();
		var sel = document.getElementById('mrz_display_post_exp_source_pt');
		if (sel) {
			sel.addEventListener('change', updateTaxonomyVisibility);
		}

		updateFormatVisibility();
		document.querySelectorAll('.mrz-display-post-exp-format-input').forEach(function (input) {
			input.addEventListener('change', updateFormatVisibility);
		});

		initAcfRepeater();
		initTaxoLogicToggle();
	});
})();
