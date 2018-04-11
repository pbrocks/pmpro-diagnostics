jQuery(document).ready(function($) {
	$('#pbrx-form').submit(function() {
		$('#pbrx_loading').show();
		$('#pbrx_submit').attr('disabled', true);
		
      data = {
      	action: 'pbrx_get_results',
      	pbrx_nonce: if_pbrx_vars.pbrx_nonce
      };

     	$.post(ajaxurl, data, function(response){
			$("#pbrx-results").html(response);
			$('#pbrx_loading').show();
			$('#pbrx_submit').attr('disabled', false);

			console.log (response);
		});

		return false;
	});
});