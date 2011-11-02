<?php
namespace wcf\data\comment;
use wcf\data\comment\response\StructuredCommentResponse;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;

/**
 * Provides methods to handle responses for this comment.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.comment
 * @subpackage	data.comment
 * @category 	Community Framework
 */
class StructuredComment extends DatabaseObjectDecorator implements \Countable, \Iterator {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	public static $baseClass = 'wcf\data\comment\Comment';
	
	/**
	 * list of ordered responses
	 * @var	array<wcf\data\comment\response\StructuredCommentResponse>
	 */
	protected $responses = array();
	
	/**
	 * iterator index
	 * @var	integer
	 */
	private $position = 0;
	
	/**
	 * user profile object
	 * @var	wcf\data\user\UserProfile
	 */
	public $userProfile = null;
	
	/**
	 * Adds an response
	 * 
	 * @param	wcf\data\comment\response\StructuredCommentResponse	$response
	 */
	public function addResponse(StructuredCommentResponse $response) {
		$this->responses[] = $response;
	}
	
	/**
	 * Returns the last responses for this comment.
	 * 
	 * @return	array<wcf\data\comment\response\StructuredCommentReponse>
	 */
	public function getResponses() {
		return $this->responses;
	}
	
	/**
	 * Sets the user's profile.
	 * 
	 * @param	wcf\data\user\UserProfile	$userProfile
	 */
	public function setUserProfile(UserProfile $userProfile) {
		$this->userProfile = $userProfile;
	}
	
	/**
	 * Returns the user's profile.
	 * 
	 * @return	wcf\data\user\UserProfile
	 */
	public function getUserProfile() {
		return $this->userProfile;
	}
	
	/**
	 * @see	\Countable::count()
	 */
	public function count() {
		return count($this->responses);
	}
	
	/**
	 * @see	\Iterator::current()
	 */
	public function current() {
		return $this->responses[$this->position];
	}
	
	/**
	 * @see	\Iterator::key()
	 */
	public function key() {
		return $this->postition;
	}
	
	/**
	 * @see	\Iterator::next()
	 */
	public function next() {
		$this->position++;
	}
	
	/**
	 * @see	\Iterator::rewind()
	 */
	public function rewind() {
		$this->position = 0;
	}
	
	/**
	 * @see	\Iterator::valid()
	 */
	public function valid() {
		return isset($this->responses[$this->position]);
	}
}