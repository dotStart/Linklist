<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Adds thumbnails to the linklist
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.evil-co.wcf.linkList.link.thumbnail
 */
class LinkListLinkThumbnailListener implements EventListener {
	public $additionalLeftContents = array();
	public $eventObj = null;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// thumbnails disabled?
		if (!LINKLIST_LINK_SHOW_THUMBNAIL) return;
		
		if ($className == 'LinkListLinkPage') {
			if (LINKLIST_LINK_THUMBNAIL_TYPE == 1) {
				// fadeout
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
					WCF::getTPL()->append('additionalLinkBoxes', WCF::getTPL()->fetch('linkListLinkThumbnail'));
				}
			}
			else {
				// websnapr
				WCF::getTPL()->append('specialStyles', '<script type="text/javascript" src="http://www.websnapr.com/js/websnapr.js"></script>');

				if (LINKLIST_LINK_THUMBNAIL_VIEW == 1) {
					WCF::getTPL()->append(array(
						'additionalMessageBodyContents' => '<div class="linkListLinkContentThumbnail"><script type="text/javascript">wsr_snapshot(\''.$this->encodejs($eventObj->link->url).'\', \''.LINKLIST_LINK_THUMBNAIL_WEBSNAPR_KEY.'\');</script></div>',
						'specialStyles' => '<style type="text/css">.linkListLinkContentThumbnail { float: right; padding: 5px; } .linkListLinkContentThumbnail:after { clear: right; }</style>',
					));
				} else {
					WCF::getTPL()->assign(array(								
						'url' => $eventObj->link->url,
						'subject' => $eventObj->link->subject,
						'service' => 'websnapr',
						'linkID' => $eventObj->link->linkID
					));
					WCF::getTPL()->append('additionalLinkBoxes', WCF::getTPL()->fetch('linkListLinkThumbnail'));
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
				} else {
					WCF::getTPL()->append('specialStyles', '<script type="text/javascript" src="http://www.websnapr.com/js/websnapr.js"></script>');
					
					foreach ($eventObj->linkList->links as &$link) {
						$additionalLeftContents[$link->linkID] = '<div class="linkListLinkListThumbnail"><script type="text/javascript">wsr_snapshot(\''.$this->encodejs($link->url).'\', \''.LINKLIST_LINK_THUMBNAIL_WEBSNAPR_KEY.'\');</script></div>';
					}
				}
				
				WCF::getTPL()->append(array(
					'additionalLeftContents' => $additionalLeftContents,
					'specialStyles' => '<style type="text/css">.linkListLinkListThumbnail { text-align: center; float: left; width: 160px; } .linkListLinkListThumbnail img { width: auto; }</style>',
				));
			}
		}
	}
	
	/**
	 * Encodes strings for use in js variables
	 * @param	string			$string
	 */
	protected function encodejs($string) {
		// escape backslash
		$string = StringUtil::replace("\\", "\\\\", $string);
		
		// escape singe quote
		$string = StringUtil::replace("'", "\'", $string);
		
		// escape new lines
		$string = StringUtil::replace("\n", '\n', $string);
		
		// escape slashes
		return StringUtil::replace("/", '\/', $string);
	}
}
?>
