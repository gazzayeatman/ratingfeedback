<?php

class Controller_HandleRatingFeedback extends Extension {

	private static $allowed_actions = [
		'RatingFeedbackForm'
	];

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
		$options = OptionsetField::create('Rating', 'Rate this', $stars)->addExtraClass('ratingfeedback_stars');

		// Include field if needed
		if ($this->owner->data()->includeRating()) {			
			$fields->push($options);
		}

		// Build Comment Field
		$comments = TextareaField::create('Comments', 'Add a comment')->addExtraClass('ratingfeedback_comments');
		
		if ($this->owner->data()->includeFeedback()) {
			$fields->push($comments);
		}

		// Config Form
		$actions = new FieldList($action = new FormAction('recordRating', 'Submit'));
		$required = new RequiredFields('Rating');

		$form = new Form($this->owner, __FUNCTION__, $fields, $actions, $required);

		$form->addExtraClass('ratingfeedback_form');
		$form->legend = 'Rating/Feedback form';
		$form->disableSecurityToken();
		$form->setFormMethod('POST');

		if($rating = Session::get('RatingBlock'. $this->owner->ID)) {
			
			if ($rating->PageID === $this->owner->ID) {
				$submitted = true;

				$form->loadDataFrom($rating)
				->addExtraClass('disabled')
				->transform(new DisabledTransformation());
			
				$options->performDisabledTransformation(true);

				$submittedComments = trim($rating->Comments);
			}			
		}

		$form->setRedirectToFormOnValidationError(true);

		// Enable Spam Protection
		if(!Director::isDev()) {
			$form->enableSpamProtection();
		}

		$data = new ArrayData(array(
			'Submitted' => $submitted,
			'SubmittedComments' => $submittedComments,
			'Title' => ($submitted) ? '' : $title,
			'Intro' => ($submitted) ? $this->owner->data()->getBlockSuccess() : $this->owner->data()->getBlockIntro()
		));

		return $form
			->customise($data)
			->setTemplate('forms/RatingForm');
	}

	/**
	 * Rating block submission action
	 */
	public function recordRating($data, $form) 
	{
		$rating = new RatingFeedback();
		$form->saveInto($rating);
		$rating->PageID = $this->owner->ID;
		$rating->write();

		Session::set('RatingBlock'. $this->owner->ID, $rating);

		// Redirect
		$url = Controller::join_links($this->owner->Link(), '#rating-' . $this->owner->ID);

		return $this->owner->redirect($url);
	}

}