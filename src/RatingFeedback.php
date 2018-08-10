<?php

class RatingFeedback extends DataObject {

	private static $db = [
		'Rating' => 'Int',
		'Comments' => 'Text'
	];

	private static $has_one = [
		'Page' => 'SiteTree',
		'SubmittedBy' => 'Member'
	];

	private static $summary_fields = [
		'ID' => 'ID',
		'Created' => 'Created',
		'Page.Title' => 'Title',
		'Rating' => 'Rating',		
		'Comments' => 'Comments',
		'SubmittedBy.Title' => 'Submitted By'	
	];

	private static $default_sort = 'Created DESC';

	/**
	* Permissions
	*/
	public function canCreate($member = null) 
	{
		return false;
	}

	public function canView($member = null) 
	{
		return Permission::check('CMS_ACCESS', 'any', $member);
	}

	public function canEdit($member = null) 
	{
	  return false;
	}

	public function canDelete($member = null) 
	{
		return Permission::check('CMS_ACCESS', 'any', $member);
	}

	/**
	* Stats
	*/
	public function getAverage() 
	{
		if ($this->PageID) {
			return round(RatingFeedback::get()->filter('PageID', $this->PageID)->avg('Rating'), 2);
		}
	}

	public function getTotalVotes() 
	{
		if ($this->PageID) {
			return RatingFeedback::get()->filter('PageID', $this->PageID)->count();
		}
	}

}