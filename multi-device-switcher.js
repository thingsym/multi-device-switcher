
(function($) {
	$(function() {
		if ( !$('#admin-tabs').length ) return;
		$('#admin-tabs').prepend('<ul><\/ul>');
		$('#admin-tabs > fieldset').each(function (i) {
			id = $(this).attr('id');
			caption = $(this).find('h3.label').text();
			$('#admin-tabs > ul').append('<li><a href="#' + id + '"><span>' + caption + '<\/span><\/a><\/li>');
		});

		$('#admin-tabs').tabs();
	});
})(jQuery);
