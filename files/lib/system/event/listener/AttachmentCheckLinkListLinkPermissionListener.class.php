<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Checks the download permission for linklist link attachments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class AttachmentCheckLinkListLinkPermissionListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$attachment = $eventObj->attachment;
		
		if ($attachment['containerID'] && $attachment['containerType'] == 'linkListLink') {
			// get linklist link
			require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');
			$link = new LinkListLink($attachment['containerID'], null);
			
			// enter link
			$link->enter();
			
			// get linklist category
			require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
			$category = LinkListCategory::getCategory($link->categoryID);
			
			// check download permission
			if (!$category->getPermission('canDownloadAttachment') && (!$eventObj->thumbnail || !$category->getPermission('canViewAttachmentPreview'))) {
				throw new PermissionDeniedException();
			}
		}
	}
}
?>