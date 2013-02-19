(function($) {
	var $form          = $('#social-login-form');
	var $loginFeedback = $('.social-messages');

	$form.on('submit', function(e) {
		e.preventDefault();

		$loginFeedback.removeClass()
		       .addClass('alert alert-info')
		       .html($loginFeedback.data('wait'));

		$.ajax({
			type:     'POST',
			url:      $form.prop('action'),
			dataType: 'json',
			data:     $form.serialize(),

			success: function(data) {
				$loginFeedback.removeClass('alert-info alert-danger')
				              .addClass('alert-success')
				              .html(data.message);

				// Move on
				if (typeof data.redirect !== 'undefined') {
					window.location.href = data.redirect;
				}
				else {
					window.location.reload();
				}

				// Put the redirect message for slow net
				// connections
				setTimeout(function() {
					$loginFeedback.html($loginFeedback.data('redirecting'));
				}, 1000);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var response = $.parseJSON(jqXHR.responseText);
				$loginFeedback.removeClass('alert-info alert-success')
				              .addClass('alert-danger')
				              .html(response.message);
			}
		});

		return false;
	});
})(jQuery);
