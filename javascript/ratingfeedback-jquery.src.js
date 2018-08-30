(function($) {

	$(document).ready(function() {

		function init() {
			// Rating Only and Rating And Feedback
			var form = $('.ratingfeedback-form');

			if (form.length > 0) {
				var inputs = form.find('input, textarea');
				inputs.on('change input', function(e) {
					form.not('.interacted-with').addClass('interacted-with');
				});

				var stars = form.find('input'),
					conditionallyRequiredComment = form.find('[data-require-if-less-than]');
				
				if (conditionallyRequiredComment.length == 1) {
					var requireIfLessThan = conditionallyRequiredComment.data('require-if-less-than');

					stars.on('change', function (e) {
						var rating = $(this).val();

						if (rating < requireIfLessThan) {
							conditionallyRequiredComment.attr('required', 'required').attr('aria-required', 'true');
						} else {
							conditionallyRequiredComment.removeAttr('required').removeAttr('aria-required');
						}
					});
				}
				
				var commentLabel = form.find('.rating-comment label');
				if (commentLabel.length > 0) {
					commentLabel.on('click', function(e) {
						form.addClass('interacted-with');
						commentLabel.off('click');
					});
				}

			}
		}

		init();
	});

})(jQuery)