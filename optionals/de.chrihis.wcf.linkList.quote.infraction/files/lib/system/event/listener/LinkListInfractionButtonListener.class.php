<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows an infraction button on the linklist link and linklist link comment list page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListInfractionButtonListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className == 'LinkListLinkPage') {
			// add infraction button to linklist link
			if (MODULE_USER_INFRACTION && $eventObj->link->userID && WCF::getUser()->getPermission('admin.user.infraction.canWarnUser')) {
				WCF::getTPL()->append('additionalSmallButtons', '<li><a href="index.php?form=UserWarn&amp;userID='.$eventObj->link->userID.'&amp;objectType=linkListLink&amp;objectID='.$eventObj->link->linkID.SID_ARG_2ND.'" title="'.WCF::getLanguage()->get('wcf.user.infraction.button.warn').'"><img src="'.StyleManager::getStyle()->getIconPath('infractionWarningS.png').'" alt="" /> <span>'.WCF::getLanguage()->get('wcf.user.infraction.button.warn').'</span></a></li>');
			}
		}
		else {
			if (MODULE_USER_INFRACTION && WCF::getUser()->getPermission('admin.user.infraction.canWarnUser')) {
				// add infraction buttons to comments on the linklist link comment list page
				$additionalSmallButtons = array();
				foreach ($eventObj->commentList->comments as &$comment) {
					if ($comment->userID) {
						$additionalSmallButtons[$comment->commentID] = '<li><a href="index.php?form=UserWarn&amp;userID='.$comment->userID.'&amp;objectType=linkListLinkComment&amp;objectID='.$comment->commentID.SID_ARG_2ND.'" title="'.WCF::getLanguage()->get('wcf.user.infraction.button.warn').'"><img src="'.StyleManager::getStyle()->getIconPath('infractionWarningS.png').'" alt="" /> <span>'.WCF::getLanguage()->get('wcf.user.infraction.button.warn').'</span></a></li>';
					}
				}
			
				WCF::getTPL()->append(array(
					'additionalSmallButtons' => $additionalSmallButtons,
				));
			}
		}
	}
}
?>