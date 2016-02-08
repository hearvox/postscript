jQuery(document).ready(function($) {
	$('#psing-form').submit(function() {
		$('#psing_loading').show();
		$('#psing_submit').attr('disabled', true);
		
      data = {
      	action: 'psing_get_results',
      	psing_nonce: psing_vars.psing_nonce
      };

     	$.post(ajaxurl, data, function (response) {
			$('#psing_results').html(response);
			$('#psing_loading').hide();
			$('#psing_submit').attr('disabled', false);
		});	
		
		return false;
	});
});