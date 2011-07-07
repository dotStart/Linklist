<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObjectType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentWarningObject.class.php');

/**
 * An implementation of WarningObjectType to support the usage of a linklist link comments as a warning object.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage data.linkList.link.comment
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentWarningObjectType implements WarningObjectType {
	/**
	 * @see WarningObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$comments = array();
			$sql = "SELECT		*
				FROM 		wcf".WCF_N."_linkList_link_comment
				WHERE 		commentID IN (".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$comments[$row['commentID']] = new LinkListLinkCommentWarningObject(null, $row);
			}
			
			return (count($comments) > 0 ? $comments : null); 
		}
		else {
			// get object
			$comment = new LinkListLinkCommentWarningObject($objectID);
			if (!$comment->commentID) return null;
			
			// return object
			return $comment;
		}
	}
}
?>