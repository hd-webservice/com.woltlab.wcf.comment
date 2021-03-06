<?php
namespace wcf\data\comment;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\comment\response\StructuredCommentResponse;
use wcf\data\user\UserList;
use wcf\data\user\UserProfile;

/**
 * Provides a structured comment list fetching last responses for every comment.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.comment
 * @subpackage	data.comment
 * @category 	Community Framework
 */
class StructuredCommentList extends CommentList {
	/**
	 * @see	wcf\data\DatabaseObjectList::$sqlLimit
	 */
	public $sqlLimit = 10;
	
	/**
	 * @see	wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'comment.time DESC';
	
	/**
	 * Creates a new structured comment list.
	 * 
	 * @param	integer		$objectTypeID
	 * @param	integer		$objectID
	 */
	public function __construct($objectTypeID, $objectID) {
		parent::__construct();
		
		$this->getConditionBuilder()->add("comment.objectTypeID = ?", array($objectTypeID));
		$this->getConditionBuilder()->add("comment.objectID = ?", array($objectID));
	}
	
	/**
	 * @see	wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		parent::readObjects();
		
		// fetch last response ids
		$responseIDs = array();
		$userIDs = array();
		foreach ($this->objects as &$comment) {
			$lastResponseIDs = $comment->getLastResponseIDs();
			if (!empty($lastResponseIDs)) {
				foreach ($lastResponseIDs as $responseID) {
					$responseIDs[$responseID] = $comment->commentID;
				}
			}
			$userIDs[] = $comment->userID;
			
			$comment = new StructuredComment($comment);
		}
		unset($comment);
		
		// fetch last responses
		if (!empty($responseIDs)) {
			// invert sort order (maintains order within StructuredComment's response array)
			$sqlOrder = (strpos($this->sqlOrderBy, 'ASC') === false) ? 'DESC' : 'ASC';
			
			$responseList = new CommentResponseList();
			$responseList->getConditionBuilder()->add("comment_response.responseID IN (?)", array(array_keys($responseIDs)));
			$responseList->sqlOrderBy = "comment_response.time ".$sqlOrder;
			$responseList->sqlLimit = 0;
			$responseList->readObjects();
			
			foreach ($responseList as $response) {
				$response = new StructuredCommentResponse($response);
				
				$commentID = $responseIDs[$response->responseID];
				$this->objects[$commentID]->addResponse($response);
				
				$userIDs[] = $response->userID;
			}
		}
		
		// fetch user data and avatars
		if (!empty($userIDs)) {
			$userIDs = array_unique($userIDs);
			
			$users = UserProfile::getUserProfiles($userIDs);
			foreach ($this->objects as $comment) {
				if (isset($users[$comment->userID])) {
					$comment->setUserProfile($users[$comment->userID]);
				}
				
				foreach ($comment as $response) {
					if (isset($users[$response->userID])) {
						$response->setUserProfile($users[$response->userID]);
					}
				}
			}
		}
	}
}
