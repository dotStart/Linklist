<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/NotificationObject.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/ViewableLinkListLinkComment.class.php');

/**
 * An implementation of NotificationObject to support the usage of a link list link comment as a notification object.
 *
 * @author	Christoph H.
 * @copyright	2011 Christoph H. (Chrihis)
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.chrihis.wcf.linkList.comment.notification
 * @subpackage	data.linkList.link.comment
 * @category 	WoltLab Community Framework
 */
class LinkListLinkCommentNotificationObject extends ViewableLinkListLinkComment implements NotificationObject {

	/**
	 * @see ViewableLinkListLinkComment:__construct
	 */
	public function __construct($commentID, $row = null) {
		// construct from old data if possible
		if (is_object($row)) {
			$row = $row->data;
		}
		parent::__construct($commentID, $row);
	}
		
	/**
	 * @see NotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->commentID;
	}

	/**
	 * @see NotificationObject::getTitle()
	 */
	public function getTitle() {
	}

	/**
	 * @see NotificationObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLinkCommentList&linkID='.$this->linkID.'&commentID='.$this->commentID.'#comment'.$this->commentID;
	}

	/**
	 * @see NotificationObject::getIcon()
	 */
	public function getIcon() {
		return 'message';
	}

	/**
	 * @see ViewableLinkListLinkComment::getFormattedComment()
	 */
	public function getFormattedMessage($outputType = 'text/html') {
		require_once(WCF_DIR.'lib/data/message/bbcode/SimpleMessageParser.class.php');
		return SimpleMessageParser::getInstance()->parse($this->comment);
	}

}
?>