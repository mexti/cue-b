$(document).ready(function() {
	$.extend($.validator.messages, {
		required: "Required",
		remote: "Incorrect"
	});
	$('#logonForm').validate({
		rules: {
			username: {
				required: true
			},
			password: {
				required: true
			}
		},
		errorPlacement: function(error,element) {
			$('#alerts').append('<div class="alert alert-danger alert-dismissible fade in"><button type="button" class="close" data-dismiss="alert" aria-label="close"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-triangle fa-lg"></i> '+element+'</div>');
			$('.alert-dismissible').delay(3000).fadeOut('slow', function() {
				$(this).alert('close');
			});
		},
		messages: {
			username: { required: 'Required' },
			password: { required: 'Required' }
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			$('#logonForm').removeClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
			$('#logonForm').removeClass('has-error');
		},
		onkeyup: function(element) {
			$('#logonForm').removeClass('has-error');
		},
		focusCleanup: true,
		focusInvalid: false,
		submitHandler: function(form) {
			event.preventDefault();
			$.post('/lib/ajax/logonmodule.php',
			{
				username: function() { return $('#username').val(); },
				password: function() { return $('#password').val(); }
			},
			function(data,status) {
				var result = $.parseJSON(data);
				if(result.success=='true') {
					form.submit();
				} else {
					$('#logonForm').addClass('has-error');
					$('#alerts').append('<div class="alert alert-danger alert-dismissible fade in"><button type="button" class="close" data-dismiss="alert" aria-label="close"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-triangle fa-lg"></i> '+result.message+'</div>');
					$('.alert-dismissible').delay(3000).fadeOut('slow', function() {
						$(this).alert('close');
					});
				}
			});
		}
	});
});