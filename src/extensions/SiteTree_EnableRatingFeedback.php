<?php

/**
* This extension provides a SiteTree object with the options to activate the FeedbackRating functionality.
*/
class SiteTree_EnableRatingFeedback extends DataExtension {

	private static $db = [
		'EnableRatingFeedback' => "Enum('Disabled, Rating and Feedback, Rating only, Feedback only')",
		'RatingBlockMaxStars' => 'Int',
		'RatingBlockTitle' => 'Varchar(255)',
		'RatingBlockIntro' => 'HTMLText',
		'RatingBlockSuccess' => 'HTMLText'
	];

	public function updateCMSFields(FieldList $fields)
	{
		// Display Feedback options
		$enableFeedback = DropdownField::create('EnableRatingFeedback', 'Feedback/Rating', $this->owner->dbObject('EnableRatingFeedback')->enumValues());

		// Allow to override default content
		$config = SiteConfig::current_site_config();
		$maxStar = NumericField::create('RatingBlockMaxStars', 'Rating/Feedback Stars')
				->setRightTitle('Default Rating/Feedback Stars used if left blank');
		$title = TextField::create('RatingBlockTitle', 'Rating/Feedback Block Title')
				->setRightTitle('Default Rating/Feedback Block Title used if left blank');
		$intro = HTMLEditorField::create('RatingBlockIntro', 'Rating/Feedback Block Intro')->setRows(3)
				->setRightTitle('Default Rating/Feedback Block Intro used if left blank');
		$success = HTMLEditorField::create('RatingBlockSuccess', 'Rating/Feedback Block Success message')->setRows(3)
				->setRightTitle('Default Rating/Feedback Block Success Message used if left blank');

		$fields->addFieldsToTab('Root.Feedback', [$enableFeedback, $maxStar, $title, $intro, $success]);		
	}

	/**
	* By default, enum will not have any value in DB
	* so this helps figure out if it is disabled or not
	*
	* @return Boolean
	*/
	public function getRatingFeedbackEnabled()
	{
		return ($this->owner->EnableRatingFeedback !== null && $this->owner->EnableRatingFeedback !== 'Disabled');
	}

	public function includeRating()
	{
		return ($this->owner->EnableRatingFeedback == 'Rating and Feedback' || $this->owner->EnableRatingFeedback == 'Rating only');
	}

	public function includeFeedback()
	{
		return ($this->owner->EnableRatingFeedback == 'Rating and Feedback' || $this->owner->EnableRatingFeedback == 'Feedback only');
	}

	public function getBlockMaxStars()
	{
		return ($this->owner->RatingBlockMaxStars) ? $this->owner->RatingBlockMaxStars : (int) SiteConfig::current_site_config()->DefaultRatingBlockMaxStars;
	}

	public function getBlockTitle()
	{
		return ($this->owner->RatingBlockTitle) ? $this->owner->RatingBlockTitle : SiteConfig::current_site_config()->DefaultRatingBlockTitle;
	}

	public function getBlockIntro()
	{
		return ($this->owner->RatingBlockIntro) ? $this->owner->RatingBlockIntro : SiteConfig::current_site_config()->DefaultRatingBlockIntro;
	}

	public function getBlockSuccess()
	{
		return ($this->owner->RatingBlockSuccess) ? $this->owner->RatingBlockSuccess : SiteConfig::current_site_config()->DefaultRatingBlockSuccess;
	}

	/**
	* To set a data-attibute on the form
	* for javascript function
	*/
	public function getRatingType()
	{
		$filter = URLSegmentFilter::create();
		$t = $filter->filter($this->owner->EnableRatingFeedback);

		return $t;
	}

	public function getHTMLID()
	{
		return sprintf('rating%s', $this->owner->ID);
	}
}