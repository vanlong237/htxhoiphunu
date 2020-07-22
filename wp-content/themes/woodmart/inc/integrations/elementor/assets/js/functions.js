jQuery(window).on('elementor:init', function() {
	elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
		jQuery(panel.$el).find('.xts-html-block-links select').each(function() {
			changeLink(jQuery(this));
		});

		jQuery(panel.$el).on('change load', '.xts-html-block-links select', function() {
			changeLink(jQuery(this));
		});

		function changeLink($select) {
			var $link = $select.parents('.xts-html-block-links').find('.xts-edit-block-link');
			var selectValue = $select.find('option:selected').val();
			var currentHref = $link.attr('href');

			var newHref = currentHref.split('post=')[0] + 'post=' + selectValue + '&action=elementor';

			if (!selectValue || 0 == selectValue) {
				$link.hide();
			} else {
				$link.attr('href', newHref).show();
			}
		}
	});
});
