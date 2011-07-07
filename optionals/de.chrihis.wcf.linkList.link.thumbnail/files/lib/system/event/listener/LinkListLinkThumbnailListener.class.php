<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows a thumbnail of a link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.thumbnail
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkThumbnailListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!LINKLIST_LINK_SHOW_THUMBNAIL) return;
		if ($className == 'LinkListLinkPage') {
			if (LINKLIST_LINK_THUMBNAIL_TYPE == 1) {
				if (LINKLIST_LINK_THUMBNAIL_VIEW == 1) {
					WCF::getTPL()->append(array(
						'additionalMessageBodyContents' => '<img class="linkListLinkContentThumbnail" src="http://fadeout.de/thumbshot-pro/?url='.$eventObj->link->url.'&scale=4" alt="" title="'.WCF::getLanguage()->get('wcf.linkList.link.thumbnailShow').'" />',
						'specialStyles' => '<style type="text/css">.linkListLinkContentThumbnail { float: right; padding: 5px; } .linkListLinkContentThumbnail:after { clear: right; }</style>'
					));
				}
				else {
					WCF::getTPL()->assign(array(								
						'url' => 'http://fadeout.de/thumbshot-pro/?url='.$eventObj->link->url.'&scale=3',
						'subject' => $eventObj->link->subject,
						'service' => 'fadeout',
						'linkID' => $eventObj->link->linkID
					));
					WCF::getTPL()->append('additionalBoxes', WCF::getTPL()->fetch('linkListLinkThumbnail'));
				}
				WCF::getTPL()->append('additionalLinkFooterContent', '<p class="smallFont" style="text-align: center">'.WCF::getLanguage()->get('wcf.linkList.link.thumbnail.fadeout.coypright').'</p>');
			}
			else {
				if (LINKLIST_LINK_THUMBNAIL_VIEW == 1) {
					WCF::getTPL()->append(array(
						'additionalMessageBodyContents' => '<img class="linkListLinkContentThumbnail" src="http://images.websnapr.com/?url='.$eventObj->link->url.'&size=s" alt="" title="'.WCF::getLanguage()->get('wcf.linkList.link.thumbnailShow').'" />',
						'specialStyles' => '<style type="text/css">.linkListLinkContentThumbnail { float: right; padding: 5px; } .linkListLinkContentThumbnail:after { clear: right; }</style>',
					));
				}
				else {
					WCF::getTPL()->assign(array(								
						'url' => 'http://cligs.websnapr.com/?url='.$eventObj->link->url.'&size=s',
						'subject' => $eventObj->link->subject,
						'service' => 'websnapr',
						'linkID' => $eventObj->link->linkID
					));
					WCF::getTPL()->append('additionalBoxes', WCF::getTPL()->fetch('linkListLinkThumbnail'));
				}
			}
		}
		else {
			if (LINKLIST_LINK_SHOW_THUMBNAIL_IN_CATEGORY) {
				$additionalLeftContents = array();
				if (LINKLIST_LINK_THUMBNAIL_TYPE == 1) {
					foreach ($eventObj->linkList->links as &$link) {
						$additionalLeftContents[$link->linkID] = '<div class="linkListLinkListThumbnail"><a href="index.php?page=LinkListLink&amp;linkID='.$link->linkID.SID_ARG_2ND.'"><img src="http://fadeout.de/thumbshot-pro/?url='.$link->url.'&scale=4" alt="" /></a></div>';
					}
				}
				else {
					foreach ($eventObj->linkList->links as &$link) {
						$additionalLeftContents[$link->linkID] = '<div class="linkListLinkListThumbnail"><a href="index.php?page=LinkListLink&amp;linkID='.$link->linkID.SID_ARG_2ND.'"><img src="http://images.websnapr.com/?url='.$link->url.'&size=s" alt="" /></a></div>';
					}
				}
				
				WCF::getTPL()->append(array(
					'additionalLeftContents' => $additionalLeftContents,
					'specialStyles' => '<style type="text/css">.linkListLinkListThumbnail { text-align: center; float: left; width: 160px; } .linkListLinkListThumbnail img { width: auto; }</style>',
				));
			}
		}
	}
}
?>
