jQuery(document).ready(function(){
	if (! jQuery('#admin-tabs').length) return;
	jQuery('#admin-tabs').prepend('<ul><\/ul>');
	jQuery('#admin-tabs > fieldset').each(function (i) {
		id = jQuery(this).attr('id');
		caption = jQuery(this).find('h3.label').text();
		jQuery('#admin-tabs > ul').append('<li><a href="#'+id+'"><span>'+caption+'<\/span><\/a><\/li>');
	});

	jQuery('#admin-tabs').tabs();
});
