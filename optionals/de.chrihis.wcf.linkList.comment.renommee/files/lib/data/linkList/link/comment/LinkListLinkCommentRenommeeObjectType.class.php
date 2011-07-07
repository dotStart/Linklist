<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/object/RenommeeObjectType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentRenommeeObject.class.php');

/**
 * General class for linklist link comment renommee object types.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.comment.renommee
 * @subpackage data.linkList.link.comment
 * @category 	Renommee System
 */
class LinkListLinkCommentRenommeeObjectType implements RenommeeObjectType {
	/**
	 * @see RenommeeObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$comments = array();
			$sql = "SELECT		link_comment.*
				FROM		wcf".WCF_N."_linkList_link_comment link_comment
				WHERE 		commentID IN (".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);			
			while ($row = WCF::getDB()->fetchArray($result)) {
				$comments[$row['commentID']] = new LinkListLinkCommentRenommeeObject(null, $row);
			}
			
			return (count($comments) > 0 ? $comments : null); 
		}
		else {
			// get object
			$comment = new LinkListLinkCommentRenommeeObject($objectID);
			if (!$comment->commentID) return null;
			
			// check permissions			
			$comment->access();
			
			// return object
			return $comment;
		}
	}
		
	/**	 
	 * @see RenommeeObjectType::getObjectRenommeeByUser()
	 */
	public function getObjectRenommeeByUser(User $user) {
		if (!$this->isActive()) return 0;
		
		$sql = "SELECT	SUM(points) AS renommee
			FROM	wcf".WCF_N."_user_renommee
			WHERE	userID = ".$user->userID."
			AND	objectType = 'linkListLinkComment'";
		$row = WCF::getDB()->getFirstRow($sql);
		
		return ($row['renommee'] ? $row['renommee'] : 0);
	}
	
	/**
	 * @see RenommeeObjectType::isActive()	 
	 */
	public function isActive() {
		return (USER_RENOMMEE_OBJECT_LINKLIST_LINK_COMMENT_ACTIVE && MODULE_LINKLIST) ? true : false;
	}
	
	/**
	 * @see RenommeeObjectType::getPackageID()
	 */
	public function getPackageID() {
		return WCF::getPackageID('de.chrihis.wcf.linkList');
	}
}
?>