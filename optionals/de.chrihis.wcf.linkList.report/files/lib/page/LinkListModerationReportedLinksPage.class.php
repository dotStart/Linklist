<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');+require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');

/**
 * Shows the reported links in the linklist moderation.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.report
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListModerationReportedLinksPage extends MultipleLinkPage {
	// system
	public $templateName = 'linkListModerationLinks';
	public $defaultSortField = LINKLIST_CATEGORY_DEFAULT_SORT_FIELD;
	public $defaultSortOrder = LINKLIST_CATEGORY_DEFAULT_SORT_ORDER;
	public $itemsPerPage = LINKLIST_CATEGORY_LINKS_PER_PAGE;

	// parameters
	public $markedLinks = 0;

	/**
	 * list of linklist links
	 *
	 * @var	LinkListLinkList
	 */
	public $linkList = null;
	
	/**
	 * reported links
	 * 
	 * @var	array
	 */
	public $reportedLinks = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get reported links
		$sql = "SELECT	*, link_report.userID AS reportUserID, link_report.username AS reportUsername
			FROM		wcf".WCF_N."_linkList_link_report link_report
			LEFT JOIN wcf".WCF_N."_linkList_link linkList_link
			ON (linkList_link.linkID = link_report.linkID)
			ORDER BY link_report.reportTime DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->reportedLinks[] = new ViewableLinkListLink(null, $row);
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinkListLinks']);
		}
		
		// set output type
		MessageParser::getInstance()->setOutputType('text/html');

		$additionalData = array();
		$additionalSmallButtons = array();
		foreach ($this->reportedLinks as &$link) {
			$reportReason = MessageParser::getInstance()->parse($link->report, 0, 0, 0);
			$additionalData[$link->linkID] = '<div class="signature smallFont light">'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks.reportBy',
				array('$user' => '<a href="index.php?page=User&userID='.$link->reportUserID.SID_ARG_2ND_NOT_ENCODED.'">'.$link->reportUsername.'</a>',
					'$reportTime' => DateUtil::formatTime(null, $link->reportTime, true)
				)).'<br />'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks.reason', array('$reason' => $reportReason)).'</div>';
			$additionalSmallButtons[$link->linkID] = '<li><a href="index.php?action=LinkListLinkReportDelete&reportID='.$link->reportID.'&linkID='.$link->linkID.'&t='.SECURITY_TOKEN.SID_ARG_2ND_NOT_ENCODED.'" class="deleteButton"><img src="'.StyleManager::getStyle()->getIconPath('deleteS.png').'" alt="" longdesc="'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks.delete.sure').'" /> <span>'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks.delete').'</span></a></li>';
		}
		
		WCF::getTPL()->append(array(
			'additionalData' => $additionalData,
			'additionalSmallButtons' => $additionalSmallButtons,
			'additionalTabMenuItems' => '<li class="activeTabMenu"><a href="index.php?page=LinkListModerationReportedLinks'.SID_ARG_2ND_NOT_ENCODED.'"><span>'.WCF::getLanguage()->get('wcf.linkList.moderation.reportedLinks').'</span></a></li>'
		));
		
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_link_report";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'links' => $this->reportedLinks,
			'markedLinks' => $this->markedLinks,
			'url' => 'index.php?page=LinkListModerationReportedLinks'.'&pageNo='.$this->pageNo.SID_ARG_2ND_NOT_ENCODED,
			'action' => 'reportedLinks'
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permission
		WCF::getUser()->checkPermission('mod.linkList.canAdministrateReportedLinks');
		
		// check module options
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>