<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentList.class.php');

/**
 * Shows the newest x comments on the linklist page.
 *
 * @author 	Christoph H.
 * @copyright	2011 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.newestComments
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListNewestCommentsListener implements EventListener {
	/**
	 * list of linklist link comments
	 *
	 * @var LinkListLinkCommentList
	 */
	public $comments = array();

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!LINKLIST_SHOW_NEWESTCOMMENTS) return;
		$categoryIDs = LinkListCategory::getAccessibleCategoryIDArray();
				
		if (empty($categoryIDs)) {
			$categoryIDs = array(0);
		}

		$categoryIDs = implode(',', $categoryIDs);

		$sql = "SELECT link_comment.commentID, link_comment.message, link_comment.time, link_comment.linkID, link_comment.userID
			FROM		wcf".WCF_N."_linkList_link_comment link_comment
			LEFT JOIN wcf".WCF_N."_linkList_link linkList_link ON 		(linkList_link.linkID = link_comment.linkID)
			WHERE linkList_link.isDisabled = 0 AND linkList_link.isDeleted = 0 AND linkList_link.categoryID IN(".$categoryIDs.")
			ORDER BY link_comment.time DESC";
		$result = WCF::getDB()->sendQuery($sql, LINKLIST_NEWESTCOMMENTS_NUMBER);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->comments[] = new ViewableLinkListLinkComment(null, $row);
		}

		foreach ($this->comments as $comment) {
			$comment->link = new LinkListLink($comment->linkID);
		}
			
		$this->status = 1;
		if (WCF::getUser()->userID != 0) {
			$this->status = intval(WCF::getUser()->linkListShowNewestComments);
		}
		else {
			if (WCF::getSession()->getVar('linkListShowNewestComments') != false) {
				$this->status = WCF::getSession()->getVar('linkListShowNewestComments');
			}
		}
		// assign variables
		WCF::getTPL()->assign(array(
			'comments' => $this->comments,
			'status' => $this->status
		));
		
		if (LINKLIST_NEWESTCOMMENTS_TYPE == 1) {
			WCF::getTPL()->append(array(
				'additionalLinkListBoxes' => WCF::getTPL()->fetch('linkListNewestComments'),
				'specialStyles' => '<style type="text/css">.newestLinkListComments { list-style: none; margin-top: 10px; padding: 0; } .newestLinkListComments li { min-height: 0; } .newestLinkListComments .breadCrumbs { margin: 0; }</style>'
			));
		}
		else if (LINKLIST_NEWESTCOMMENTS_TYPE == 2) {
			WCF::getTPL()->append('additionalMessages', WCF::getTPL()->fetch('linkListNewestComments'));
		}
	}
}
?>
