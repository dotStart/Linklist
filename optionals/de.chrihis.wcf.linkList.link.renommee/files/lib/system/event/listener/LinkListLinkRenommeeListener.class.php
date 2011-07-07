<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/user/RenommeeUser.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkRenommeeObject.class.php');

/**
 * Displays the rating form and linklist link renommee on the link and link list page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.renommee
 * @subpackage data.linkList.link
 * @category 	Renommee System
 */
class LinkListLinkRenommeeListener implements EventListener {
	/**
	 * Current linklist link
	 *
	 * @var LinkListLinkRenommeeObject
	 */
        protected $link = null;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_USER_RENOMMEE || !MODULE_LINKLIST || !WCF::getUser()->getPermission('user.linkList.canViewLink')) return;
 		if ($className == 'LinkListLinkPage') {
			if ($eventName == 'readData') {
				$sql = "SELECT		linkList_link.*, user_renommee.evaluatorID AS rated,
							".(USER_RENOMMEE_SHOW_RENOMMEE ? 
						"(SELECT	SUM(points)
						FROM		wcf".WCF_N."_user_renommee
						WHERE		objectID = linkList_link.linkID
							AND		objectType = 'linkListlink') AS objectRenommee" : "")."
					FROM 		wcf".WCF_N."_linkList_link linkList_link
					LEFT JOIN	wcf".WCF_N."_user_renommee user_renommee
					ON		(user_renommee.evaluatorID = ".WCF::getUser()->userID."
						AND user_renommee.objectID = linkList_link.linkID
						AND user_renommee.objectType = 'linkListLink')
					WHERE 	linkList_link.linkID = ".$eventObj->link->linkID;
				$row = WCF::getDB()->getFirstRow($sql);
				$this->link = new LinkListLinkRenommeeObject(null, $row);
			}
			else if ($eventName == 'assignVariables') {
				$currentUser = new RenommeeUser(WCF::getUser()->userID);
				WCF::getTPL()->assign(array(
					'objectType' => 'linkListLink',
					'object' => $this->link,
					'objects' => array($this->link),
					'currentUser' => $currentUser,
					'languageVariables' => array(
						'wcf.user.renommee.linkListLink.state' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state'),
						'wcf.user.renommee.linkListLink.state.positive' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state.positive'),
						'wcf.user.renommee.linkListLink.state.negative' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state.negative'),
						'wcf.user.renommee.linkListLink.renommee' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.renommee')
					)
				));
				
				$additionalSmallButtons = '';
				
				WCF::getTPL()->append(array(
					'userMessages' => WCF::getTPL()->fetch('renommeeDialogHeader'),
					'additionalSmallButtons' => WCF::getTPL()->fetch('renommeeSmallButtons')
				));
			}
		}
		else {
			if ($eventName == 'readData') {
				if (USER_RENOMMEE_SHOW_RENOMMEE) {
					if (!empty($eventObj->linkList->sqlSelects)) $eventObj->linkList->sqlSelects .= ',';
					$eventObj->linkList->sqlSelects .= " (SELECT	SUM(points)
								FROM	wcf".WCF_N."_user_renommee
								WHERE	objectID = linkList_link.linkID
									AND	objectType = 'linkListLink') AS objectRenommee";
				}
				if (WCF::getUser()->userID && WCF::getUser()->getPermission('user.renommee.canAddRenommee')) {
					if (!empty($eventObj->linkList->sqlSelects)) $eventObj->linkList->sqlSelects .= ',';
					$eventObj->linkList->sqlSelects .= " user_renommee.evaluatorID AS rated";
					$eventObj->linkList->sqlJoins .= "	LEFT JOIN	wcf".WCF_N."_user_renommee user_renommee
									ON		(user_renommee.evaluatorID = ".WCF::getUser()->userID."
									AND		user_renommee.objectID = linkList_link.linkID
									AND		user_renommee.objectType = 'linkListLink')";
				}
			}
			else if ($eventName == 'assignVariables') {
				$currentUser = new RenommeeUser(WCF::getUser()->userID);
				WCF::getTPL()->assign(array(
					'objectType' => 'linkListLink',
					'currentUser' => $currentUser,
					'languageVariables' => array(
						'wcf.user.renommee.linkListLink.state' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state'),
						'wcf.user.renommee.linkListLink.state.positive' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state.positive'),
						'wcf.user.renommee.linkListLink.state.negative' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.state.negative'),
						'wcf.user.renommee.linkListLink.renommee' => WCF::getLanguage()->get('wcf.user.renommee.linkListLink.renommee')
					)
				));
				
				$links = array();
				$additionalSmallButtons = array();
				foreach ($eventObj->linkList->links as &$link) {
					if ($link->userID) {
						$renommeeObject = new LinkListLinkRenommeeObject(null, null, $link);
						WCF::getTPL()->assign('object', $renommeeObject);
						$additionalSmallButtons[$renommeeObject->getObjectID()] = WCF::getTPL()->fetch('renommeeSmallButtons');
						$links[] = $renommeeObject;
					}
				}

				WCF::getTPL()->assign(array(
					'objects' => $links
				));
				
				WCF::getTPL()->append(array(
					'userMessages' => WCF::getTPL()->fetch('renommeeDialogHeader'),
					'additionalSmallButtons' => $additionalSmallButtons
				));
			}
		}
	}
}
?>