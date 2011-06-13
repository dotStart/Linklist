<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');

/**
 * Represents a linklist link comment.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link.comment
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkComment extends Message {
	/**
	 * Creates a new LinkListLinkComment object.
	 *
	 * @param	integer		$linkID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if ($commentID !== null) {
			$sql = "SELECT	*
				FROM 	wcf".WCF_N."_linkList_link_comment
				WHERE 	commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}
	
	/**
	 * Returns true, if the active user can edit this comment.
	 *
	 * @param	array		$category		LinkListCategory
	 * @param	array		$link		LinkListLink
	 * @return boolean
	 */
	public function isEditable($category, $link) {
		return ((WCF::getUser()->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnComment') && !$link->isClosed) || (WCF::getUser()->getPermission('mod.linkList.canEditComment')));
	}
	
	/**
	 * Returns true, if the active user can delete this comment.
	 *
	 * @param	array	$category		LinkListCategory
	 * @param	array		$link		LinkListLink
	 * @return boolean
	 */
	public function isDeletable($category, $link) {
		return ((WCF::getUser()->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnComment') && !$link->isClosed) || (WCF::getUser()->getPermission('mod.linkList.canDeleteComment')));
	}
}
?>