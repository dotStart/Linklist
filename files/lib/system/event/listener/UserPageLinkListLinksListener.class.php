<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows the lastest linklist links in the user profile.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class UserPageLinkListLinksListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (MODULE_LINKLIST == 1 && PROFILE_SHOW_LAST_LINKLIST_LINKS == 1&& WCF::getUser()->getPermission('user.linkList.canViewLink')) {
			// get accessible categories
			require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
			$categoryIDs = LinkListCategory::getAccessibleCategoryIDArray();
				
			if (empty($categoryIDs)) {
				$categoryIDs = array(0);
			}

			$categoryIDs = implode(',', $categoryIDs);

			// get links
			require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkList.class.php');
			$linkList = new LinkListLinkList();
			$linkList->sqlConditions .= 'userID = '.$eventObj->frame->getUserID().' AND isDisabled = 0 AND categoryID IN('.$categoryIDs.')';
			$count = $linkList->countObjects();
			if ($count > 0) {
				$linkList->sqlLimit = 5;
				$linkList->readObjects();
				WCF::getTPL()->assign(array(
					'user' => $eventObj->frame->getUser(),
					'links' => $linkList->getObjects(),
					'linkCount' => $count
				));
				WCF::getTPL()->append('additionalContent3', WCF::getTPL()->fetch('userProfileLinkListLinks'));
			}
		}
	}
}
?>