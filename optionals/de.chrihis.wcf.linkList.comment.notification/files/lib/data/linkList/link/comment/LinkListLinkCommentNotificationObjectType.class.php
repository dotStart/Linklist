<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/AbstractNotificationObjectType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentNotificationObject.class.php');

/**
 * An implementation of NotificationObjectType to support the usage of link list link comments as a notification object.
 *
 * @author	Christoph H.
 * @copyright	2011 Christoph H. (Chrihis)
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.chrihis.wcf.linkList.comment.notification
 * @subpackage	data.linkList.link.comment
 * @category 	WoltLab Community Framework
 */
class LinkListLinkCommentNotificationObjectType extends AbstractNotificationObjectType {

	/**
	 * @see NotificationObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		// get object
		$comment = new LinkListLinkCommentNotificationObject($objectID);
			if (!$comment->commentID) return null;

			// return object
			return $comment;
	}

	/**
	 * @see NotificationObjectType::getObjectByObject()
	 */
	public function getObjectByObject($object) {
		// build object using its data array
		$comment = new LinkListLinkCommentNotificationObject(null, $object);
			if (!$comment->commentID) return null;

			// return object
			return $comment;
	}

	/**
	 * @see NotificationObjectType::getObjectsByIDArray()
	 */
	public function getObjectsByIDArray($objectIDArray) {
		$comments = array();
		$sql = "SELECT		*
		FROM 		wcf".WCF_N."_linkList_link_comment
		WHERE 		commentID IN (".implode(',', $objectID).")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$comments[$row['commentID']] = new LinkListLinkCommentNotificationObject(null, $row);
		}
		
		// return objects
		return $comments;
	}

	/**
	 * @see NotificationObjectType::getPackageID()
	 */
	public function getPackageID() {
		return WCF::getPackageID('de.chrihis.wcf.linkList');
	}
}
?>