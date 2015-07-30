function attachDominantColor() {
	jQuery('.trigger-rebuild').click(function() {
		jQuery(this).html('Building Color Palette...');
		storage = jQuery('input[name*="dominant-override"]');
		storage.val('trigger-rebuild');
		storage.change();
	});
	jQuery('.dominant-colour-square').click(function() {
		//Unselect all others, and select us.
		el = jQuery(this);
		storage = jQuery('input[name*="dominant-override"]');
		storage.val(el.data('col'));
		storage.change();
		jQuery(el.parent().find('.selected')).removeClass('selected');
		jQuery(el).addClass('selected');
	});
}