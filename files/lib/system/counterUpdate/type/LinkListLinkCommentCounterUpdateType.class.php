<?php
// wcf imports
require_once(WCF_DIR.'lib/system/counterUpdate/type/AbstractCounterUpdateType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentEditor.class.php');

/**
 * Updates the linklist comment counters.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.counterUpdate.type
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentCounterUpdateType extends AbstractCounterUpdateType {
	/**
	 * @see CounterUpdateType::getDefaultLimit()
	 */
	public function getDefaultLimit() {
		return 250;
	}
	
	/**
	 * @see CounterUpdateType::countItems()
	 */
	public function countItems() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_link_comment";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see CounterUpdateType::update()
	 */
	public function update($offset, $limit) {		
		// get comment ids
		$commentIDs = '';
		$sql = "SELECT		commentID
			FROM		wcf".WCF_N."_linkList_link_comment
			ORDER BY	commentID";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($commentIDs)) $commentIDs .= ',';
			$commentIDs .= $row['commentID'];
			
			// refresh comments
			LinkListLinkCommentEditor::refreshAll($commentIDs);
			
			// call event
			$this->updated();
		}
		
		// get link ids
		$linkIDs = '';
		$sql = "SELECT		linkID
			FROM		wcf".WCF_N."_linkList_link
			ORDER BY	linkID";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($linkIDs)) $linkIDs .= ',';
			$linkIDs .= $row['linkID'];
			
			// update comments count
			$sql = "UPDATE wcf".WCF_N."_linkList_link linkList_link
				SET	comments = (
						SELECT	COUNT(*)
						FROM	wcf".WCF_N."_linkList_link_comment
						WHERE	linkID = linkList_link.linkID
						)
				WHERE	linkID IN (".$linkIDs.")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
		if (empty($commentIDs)) {
			$this->finished = true;
			return;
		}
	}
}
?>