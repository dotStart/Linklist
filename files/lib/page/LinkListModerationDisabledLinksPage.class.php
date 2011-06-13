<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkList.class.php');

/**
 * Shows the disabled links in the linklist moderation.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListModerationDisabledLinksPage extends MultipleLinkPage {
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
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link list
		$this->linkList = new LinkListLinkList();
		$this->linkList->sqlConditions .= 'isDisabled = 1';
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read links
		$this->linkList->sqlLimit = $this->itemsPerPage;
		$this->linkList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->linkList->readObjects();
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinkListLinks']);
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->linkList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'links' => $this->linkList->getObjects(),
			'markedLinks' => $this->markedLinks,
			'url' => 'index.php?page=LinkListModerationDisabledLinks'.'&pageNo='.$this->pageNo.SID_ARG_2ND_NOT_ENCODED,
			'action' => 'disabledLinks'
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permission
		WCF::getUser()->checkPermission('mod.linkList.canEnableLink');
		
		// check module options
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>