
if (typeof sollyscript == "undefined" || !sollyscript) {
	var sollyscript = {};
}
sollyscript.toggler = (function ($) {
	var toggable = {
		onHtml: '<i class="fa fa-square"></i>',
		offHtml: '<i class="fa fa-square-o"></i>',
		registerHandler: function (selector, cb) {
			$(document).off('click.toggler', selector).on('click.toggler', selector, function (e) {
				e.preventDefault();
				var $self = $(this);
				var url = $self.attr('href');
				$.ajax({
					url: url,
					dataType: 'json',
					success: function (data) {
						$self
							.html(data.value ? toggable.onHtml : toggable.offHtml);
						$.isFunction(cb) && cb(true, data);
					},
					error: function (xhr) {
						$.isFunction(cb) && cb(false, xhr);
					}
				});
				return false;
			});
		}
	};
	return toggable;
})(jQuery);