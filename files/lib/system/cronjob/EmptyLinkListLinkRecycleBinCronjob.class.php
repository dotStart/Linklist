<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Cronjob empties the recycle bin for links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class EmptyLinkListLinkRecycleBinCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		if (LINKLIST_LINK_ENABLE_RECYCLE_BIN && LINKLIST_LINK_EMPTY_RECYCLE_BIN_CYCLE > 0) {
			$sql = "SELECT	linkID
				FROM	wcf".WCF_N."_linkList_link
				WHERE	isDeleted = 1
					AND deleteTime < ".(TIME_NOW - LINKLIST_LINK_EMPTY_RECYCLE_BIN_CYCLE * 86400);
			$result = WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows($result) > 0) {
				require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkEditor.class.php');
				$linkIDs = '';
				while ($row = WCF::getDB()->fetchArray($result)) {
					if (!empty($linkIDs)) $linkIDs .= ',';
					$linkIDs .= $row['linkID'];
				}
				
				// delete links completely
				LinkListLinkEditor::deleteAllCompletely($linkIDs);
			}
		}
	}
}
?>