<?php

class Controller_HandleRatingFeedback extends Extension {

	private static $allowed_actions = [
		'RatingFeedbackForm'
	];

	public function onBeforeInit()
	{
		// Require the necessary javascript
		$default_js_script = Config::inst()->get('Controller_HandleRatingFeedback', 'default_js_script');

		if ($default_js_script 
			&& filter_var($default_js_script, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]) !== false) 
		{
			$path = sprintf('%s/javascript/ratingfeedback-%s.src.js',  RATINGFEEDBACK_DIR, $default_js_script);
			if (Director::fileExists($path)) {
				Requirements::javascript($path);
			}			
		}

		// Require necessary css
		$default_css_script = Config::inst()->get('Controller_HandleRatingFeedback', 'default_css_script');

		if ($default_css_script 
			&& filter_var($default_css_script, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]) !== false) 
		{
			$path = sprintf('%s/css/ratingfeedback-%s.css',  RATINGFEEDBACK_DIR, $default_css_script);
			if (Director::fileExists($path)) {
				Requirements::css($path);
			}			
		}
	}

	public function RatingFeedbackForm()
	{
		// Config
		$maxstars = ($this->owner->data()->getBlockMaxStars()) ? $this->owner->data()->getBlockMaxStars() : 5;
		$title = $this->owner->data()->getBlockTitle();

		// Flags
		$submitted = false;
		$submittedComments = null;

		$referrer = $this->owner->Link();

		// By default, record current page ID
		// And currentUserID if logged in
		$fields = new FieldList([
			HiddenField::create('PageID', '', $this->owner->ID),
			HiddenField::create('SubmittedByID', '', Member::currentUserID())
		]);

		// Build Star Rating Field
		$stars = [];
		for ($i=1; $i<=$maxstars; $i++) {
			$stars[$i] = sprintf('%s star%s', $i, ($i > 1) ? 's' : '');
		}
		$options = OptionsetField::create('Rating', 'Rate this', $stars)->addExtraClass('field--starrating');
		$options->setTemplate('StarRatingField');

		// Include field if needed
		if ($this->owner->data()->includeRating()) {			
			$fields->push($options);
		}

		// Build Comment Field
		$comments = TextareaField::create('Comments', 'Add a comment')->addExtraClass('field--comment');
		
		if ($this->owner->data()->includeFeedback()) {
			$fields->push($comments);
		}

		// Config Form
		$actions = new FieldList($action = new FormAction('recordRating', 'Submit'));
		$required = new RequiredFields('Rating');

		$form = new Form($this->owner, __FUNCTION__, $fields, $actions, $required);

		$form->addExtraClass('ratingfeedback-form');
		$form->setAttribute('data-rating-type', $this->owner->data()->getRatingType());

		$form->disableSecurityToken();
		$form->setFormMethod('POST');

		// If form is submitted
		if($rating = Session::get('RatingBlock'. $this->owner->ID)) {
			
			if ((int) $rating->PageID === $this->owner->ID) {
				$submitted = true;
				$form->addExtraClass('submitted');

				$form->loadDataFrom($rating)
				->addExtraClass('disabled')
				->transform(new DisabledTransformation());
			
				$options->performDisabledTransformation(true);

				$submittedComments = trim($rating->Comments);
			}			
		}

		$form->setRedirectToFormOnValidationError(true);

		// Enable Spam Protection
		if($this->owner->data()->enableSpamProtection()) {
			$form->enableSpamProtection();
		}

		$data = new ArrayData(array(
			'anchor' => 'rating'.$this->owner->ID,
			'Submitted' => $submitted,
			'SubmittedComments' => $submittedComments,
			'Title' => ($submitted) ? '' : $title,
			'Intro' => ($submitted) ? $this->owner->data()->getBlockSuccess() : $this->owner->data()->getBlockIntro(),
			'IncludeRating' => $this->owner->data()->includeRating(),
			'IncludeFeedback' => $this->owner->data()->includeFeedback()
		));

		return $form
			->customise($data)
			->setTemplate('forms/RatingForm')
			->setHTMLID($this->owner->data()->getHTMLID());
	}

	/**
	 * Rating block submission action
	 */
	public function recordRating($data, $form) 
	{
		$rating = new RatingFeedback();
		$form->saveInto($rating);
		$rating->write();

		Session::set('RatingBlock'. $this->owner->ID, $rating);

		// Redirect
		$url = Controller::join_links($this->owner->Link(), '#'.$this->owner->data()->getHTMLID());

		return $this->owner->redirect($url);
	}

}