<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/object/RenommeeObject.class.php');
require_once(WCF_DIR.'lib/data/user/renommee/Renommee.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/ViewableLinkListLinkComment.class.php');

/**
 * A linklist link comment renommee object. Represents a comment for the renommee system.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.comment.renommee
 * @subpackage data.linkList.link.comment
 * @category 	Renommee System
 */
class LinkListLinkCommentRenommeeObject extends ViewableLinkListLinkComment implements RenommeeObject {
	/**
	 * Creates a new LinkListLinkCommentRenommeeObject object.
	 *
	 * @see ViewableLinkListLinkComment::__construct()
	 */
	public function __construct($commentID, $row = null, $databaseObject = null, $additionalData = array()) {
		if ($commentID !== null) {
			$sql = "SELECT		link_comment.*,
						(SELECT GROUP_CONCAT(evaluatorID SEPARATOR ',') 
						FROM	wcf".WCF_N."_user_renommee
						WHERE	objectID = link_comment.commentID
						AND	objectType = 'linkListLinkComment') AS evaluatorIDs
				FROM		wcf".WCF_N."_linkList_link_comment link_comment
				WHERE 		link_comment.commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		if ($databaseObject !== null && $row === null) {
			// workaround for nasty PHP 5.0.x and PHP 5.1.x Bug
			// @see http://bugs.php.net/bug.php?id=37212
			if (version_compare(PHP_VERSION, '5.2.0') == -1) {
				$row = $additionalData;
				$row['commentID'] = $databaseObject->commentID;
				$row['linkID'] = $databaseObject->linkID;
				$row['userID'] = $databaseObject->userID;
				$row['username'] = $databaseObject->username;
				$row['message'] = $databaseObject->message;
				$row['enableSmilies'] = $databaseObject->enableSmilies;
				$row['enableHtml'] = $databaseObject->enableHtml;
				$row['enableBBCodes'] = $databaseObject->enableBBCodes;
				$row['renommee'] = $databaseObject->renommee;
				$row['rated'] = $databaseObject->rated;
			}
			else $row = array_merge($databaseObject->data, $additionalData);
		}
		parent::__construct(null, $row);
	}

	/**
	 * @see RenommeeObject::access()
	 */
	public function access() {
		// create a new LinkListLink instance
		$link = new LinkListLink($this->linkID, null);
		$link->enter();
	}

	/**
	 * @see RenommeeObject::isRateable
	 */
	public function isRateable(RenommeeObjectType $objectType = null) {
		if ($objectType === null) {
			$objectType = Renommee::getRenommeeObjectTypeObject('linkListLinkComment');
		}

		// age validation
		if (USER_RENOMMEE_MAX_OBJECT_AGE != -1) {
			$age = floor((TIME_NOW - $this->time) / 86400);
			if ($age > USER_RENOMMEE_MAX_OBJECT_AGE) return false;
		}

		return (!$this->rated && $objectType->isActive());
	}

	/**
	 * @see RenommeeObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->commentID;
	}

	/**
	 * @see RenommeeObject::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

	 /**
	 * @see RenommeeObject::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @see RenommeeObject::getTitle()
	 */
	public function getTitle() {
		return $this->getExcerpt();
	}

	/**
	 * @see RenommeeObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLinkCommentList&linkID='.$this->linkID.'&commentID='.$this->commentID.'#comment'.$this->commentID;
	}

	/**
	 * @see RenommeeObject::getIcon()
	 */
	public function getIcon() {
		return 'linkListLinkComment';
	}
}
?>