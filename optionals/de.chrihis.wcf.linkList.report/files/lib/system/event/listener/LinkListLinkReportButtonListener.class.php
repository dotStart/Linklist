<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows a button to report a link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.report
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkReportButtonListener implements EventListener {
	/**
	 * count of reported links
	 * 
	 * @var	integer
	 */
	public $reportedLinks = 0;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className == 'LinkListLinkPage' || $className == 'LinkListLinkCommentListPage') {
			WCF::getTPL()->assign(array(
				'link' => $eventObj->link,
				'linkID' => $eventObj->linkID
			));
			if ($eventObj->link->isReported != 1 && WCF::getUser()->getPermission('user.linkList.canReportLink') && LINKLIST_LINK_ENABLE_REPORT) {
				WCF::getTPL()->append('additionalSelection', WCF::getTPL()->fetch('linkListLinkReportButton'));
			}
		}
		else if ($className == 'LinkListPage') {
			if (WCF::getUser()->getPermission('mod.linkList.canAdministrateReportedLinks')) {
				// count reported links
				$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_linkList_link_report";
				$row = WCF::getDB()->getFirstRow($sql);
				$this->reportedLinks = $row['count'];
			
				if ($this->reportedLinks != 0) {
					// assign variables
					WCF::getTPL()->assign(array(
						'reportedLinks' => $this->reportedLinks
					));
					WCF::getTPL()->append('additionalMessages', WCF::getTPL()->fetch('linkListReportOutputs'));
				}
			}
		}			
		else if ($className == 'LinkListModerationPage') {
			if ($eventName == 'readData') {
				// count reported links
				$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_linkList_link_report";
				$row = WCF::getDB()->getFirstRow($sql);
				$this->reportedLinks = $row['count'];
			}
			else {
				if (WCF::getUser()->getPermission('mod.linkList.canAdministrateReportedLinks')) {
					// assign variables
					WCF::getTPL()->assign(array(
						'reportedLinks' => $this->reportedLinks
					));
		
					WCF::getTPL()->append(array(
						'additionalModerationItems' => WCF::getTPL()->fetch('linkListModerationReportedLinks'),
						'additionalTabMenuItems' => '<li><a href="index.php?page=LinkListModerationReportedLinks'.SID_ARG_2ND_NOT_ENCODED.'"><span>'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks').'</span></a></li>'
					));
				}
			}
		}
		else {
			if (WCF::getUser()->getPermission('mod.linkList.canAdministrateReportedLinks')) {
				WCF::getTPL()->append(array(
					'additionalTabMenuItems' => '<li><a href="index.php?page=LinkListModerationReportedLinks'.SID_ARG_2ND_NOT_ENCODED.'"><span>'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks').'</span></a></li>'
				));
			}
		}
	}
}
?>
