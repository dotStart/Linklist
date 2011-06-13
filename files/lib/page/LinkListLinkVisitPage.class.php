<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the link-visit page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkVisitPage extends AbstractPage {
	/**
	 * link id
	 * 
	 * @var integer
	 */
	public $linkID = 0;
	
	/**
	 * link instance
	 * 
	 * @var ViewableLinkListLink
	 */
	public $link = null;
	
	/**
	 * category id
	 * 
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * linklist category instance
	 * 
	 * @var LinkListCategory
	 */
	public $category = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		// create new link instance
		$this->link = new ViewableLinkListLink($this->linkID);
		
		// create a new category instance
		$this->category = LinkListCategory::getCategory($this->link->categoryID);
		// enter category
		$this->category->enter();
		
		// enter link
		$this->link->enter($this->category);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// count redirects
		$sql = "UPDATE	wcf".WCF_N."_linkList_link
			SET	visits = visits + 1,
				lastVisitorID = ".WCF::getUser()->userID.",
				lastVisitorName = '".escapeString(WCF::getUser()->username)."',
				lastVisitTime = ".TIME_NOW."
			WHERE	linkID = ".$this->linkID;
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// save visitor
		if (LINKLIST_LINK_SHOW_LASTVISITORS) {
			if (WCF::getUser()->userID && !WCF::getUser()->invisible) {
				$sql = "INSERT INTO			wcf".WCF_N."_linkList_link_last_visitor
									(linkID, userID, time)
					VALUES				(".$this->linkID.", ".WCF::getUser()->userID.", ".TIME_NOW.")
					ON DUPLICATE KEY UPDATE		time = VALUES(time)";
				WCF::getDB()->registerShutdownUpdate($sql);
			}
		}
			
		// do redirect
		HeaderUtil::redirect($this->link->url, false);
		exit;
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList.links');
		
		// check permission
		$this->category->checkPermission('canVisitLink');
		
		// check module options
		if (MODULE_LINKLIST != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
}
?>