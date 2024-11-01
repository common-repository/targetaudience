jQuery(document).ready(function($){
	$("#wpta_add_audience").click(function(e){
        e.preventDefault();

		var invalid = false;
        $('.form-invalid').removeClass('form-invalid');

		if($('#name').val() == ''){
			$('#name').closest('.form-field').addClass('form-invalid');
			invalid = true;
		}
        if($('#alternative-1').val() == ''){
            $('#alternative-1').closest('.form-field').addClass('form-invalid');
            invalid = true;
        }
        if($('#alternative-2').val() == ''){
            $('#alternative-2').closest('.form-field').addClass('form-invalid');
            invalid = true;
        }
		if(invalid) return;

		$.ajax({
			type: 'POST',
			url: wpta.ajaxurl,
			data: {action: 'wpta_add_audience', name: $('#name').val(), alternative_1: $('#alternative-1').val(), alternative_2: $('#alternative-2').val(), nonce: wpta.ajax_nonce},
			success:function(data, textStatus, XMLHttpRequest){
				wpta_refresh_table();
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
	});

	function wpta_refresh_table(){
		$.ajax({
			type: 'POST',
			url: wpta.ajaxurl,
			data: {action: 'wpta_get_table_html', page: $('.audiences-table-container #wpta_page').val(), nonce: wpta.ajax_nonce},
			success: function(data, textStatus, XMLHttpRequest){
				$('.audiences-table-container .table-wrap').html(data);
				$('.add-audience-form-container input[type=text], .add-audience-form-container textarea').val('');
				$('.form-invalid').removeClass('form-invalid');
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
	}

    $(document).on('focus', '.wpta-shortcode', function (event) {
        event.preventDefault();
        $(this).select();

        try {
            document.execCommand('copy');
        } catch (err) {
            console.log('Oops, unable to copy');
        }
    });
});