<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkList.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * Shows the newest x links on the linklist page.
 *
 * @author 	Christoph H.
 * @copyright	2011 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.newestLinks
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListNewestLinksListener implements EventListener {
	/**
	 * list of linklist links
	 *
	 * @var LinkListLinkList
	 */
	public $links = array();

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!LINKLIST_SHOW_NEWESTLINKS) return;
		$categoryIDs = LinkListCategory::getAccessibleCategoryIDArray();
				
		if (empty($categoryIDs)) {
			$categoryIDs = array(0);
		}

		$categoryIDs = implode(',', $categoryIDs);
		
		$sql = "SELECT linkList_link.linkID, linkList_link.subject, linkList_link.time, linkList_link.categoryID, linkList_link.userID
			FROM		wcf".WCF_N."_linkList_link linkList_link
			WHERE linkList_link.isDisabled = 0 AND linkList_link.isDeleted = 0 AND linkList_link.categoryID IN(".$categoryIDs.")
			ORDER BY linkList_link.time DESC";
		$result = WCF::getDB()->sendQuery($sql, LINKLIST_NEWESTLINKS_NUMBER);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->links[] = new ViewableLinkListLink(null, $row);
		}

		foreach ($this->links as $link) {
			$link->category = new LinkListCategory($link->categoryID);
		}
			
		$this->status = 1;
		if (WCF::getUser()->userID != 0) {
			$this->status = intval(WCF::getUser()->linkListShowNewestLinks);
		}
		else {
			if (WCF::getSession()->getVar('linkListShowNewestLinks') != false) {
				$this->status = WCF::getSession()->getVar('linkListShowNewestLinks');
			}
		}
		// assign variables
		WCF::getTPL()->assign(array(
			'links' => $this->links,
			'status' => $this->status
		));
		
		if (LINKLIST_NEWESTLINKS_TYPE == 1) {
			WCF::getTPL()->append(array(
				'additionalLinkListBoxes' => WCF::getTPL()->fetch('linkListNewestLinks'),
				'specialStyles' => '<style type="text/css">.newestLinkListLinks { list-style: none; margin-top: 10px; padding: 0; } .newestLinkListLinks li { min-height: 0; } .newestLinkListLinks .breadCrumbs { margin: 0; }</style>'

			));
		}
		else if (LINKLIST_NEWESTLINKS_TYPE == 2) {
			WCF::getTPL()->append('additionalMessages', WCF::getTPL()->fetch('linkListNewestLinks'));
		}
	}
}
?>
