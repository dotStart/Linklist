<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows a small moderation for linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListModerationPage extends AbstractPage {
	// system
	public $templateName = 'linkListModeration';
	
	/**
	 * count of deleted links
	 * 
	 * @var	integer
	 */
	public $deletedLinks = 0;
	
	/**
	 * count of disabled links
	 * 
	 * @var	integer
	 */
	public $disabledLinks = 0;
	
	/**
	 * count of marked links
	 * 
	 * @var	integer
	 */
	public $markedLinks = 0;
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// count deleted links
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_link
			WHERE	isDeleted = 1";
		$row = WCF::getDB()->getFirstRow($sql);
		$this->deletedLinks = $row['count'];
		
		// count disabled links
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_link
			WHERE	isDisabled = 1";
		$row = WCF::getDB()->getFirstRow($sql);
		$this->disabledLinks = $row['count'];
		
		// count marked links
		$this->markedLinks = (($markedLinks = WCF::getSession()->getVar('markedLinkListLinks')) ? count($markedLinks) : 0);
		
	}
	
	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'deletedLinks' => $this->deletedLinks,
			'disabledLinks' => $this->disabledLinks,
			'markedLinks' => $this->markedLinks
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permissions
		if (!WCF::getUser()->getPermission('mod.linkList.canEditLink') && !WCF::getUser()->getPermission('mod.linkList.canEnableLink') && !WCF::getUser()->getPermission('mod.linkList.canDeleteLinkCompletely')) {
			throw new PermissionDeniedException();
		}
		
		// check module options
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>