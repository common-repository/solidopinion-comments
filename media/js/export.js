jQuery(document).ready(function() {
	var doExport = jQuery('#do_export').html();
	var exportInProgress = jQuery('#export_in_progress').html();
	if (confirm(doExport)) {
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',  
			async : true,
			data: {
				'action': 'export_to_xml'
			},
			beforeSend: function() {
				var div = jQuery('<div></div>')
				div.attr('id', 'loading');
				div.html('<p style="font-size: 14px;"><strong>'+ exportInProgress +'</strong></p>');
				jQuery('.wrap h2').after(div);
			},
			success : function(response) {
				jQuery('#loading').remove();
				jQuery('.wrap h2').after(response);
			}
		});

	}
});