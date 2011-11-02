<?php
namespace wcf\system\event\listener;
use wcf\system\comment\CommentHandler;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * Handles user profile comments.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.comment
 * @subpackage	system.event.listener
 * @category 	Community Framework
 */
class UserProfileCommentListener implements IEventListener {
	public $objectTypeID = 0;
	public $commentList = null;
	
	/**
	 * @see wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		switch ($eventName) {
			case 'readParameters':
				$this->readParameters();
			break;
			
			case 'readData':
				$this->readData($eventObj);
			break;
			
			case 'assignVariables':
				$this->assignVariables();
			break;
		}
	}
	
	protected function readParameters() {
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID('com.woltlab.wcf.user.profileComment');
		if ($this->objectTypeID === null) {
			die('<pre>KERNEL PANIC</pre>');
		}
	}
	
	protected function readData($eventObj) {
		$this->commentList = CommentHandler::getInstance()->getCommentList($this->objectTypeID, $eventObj->userID);
	}
	
	protected function assignVariables() {
		WCF::getTPL()->assign(array(
			'commentList' => $this->commentList,
			'commentObjectTypeID' => $this->objectTypeID
		));
	}
}