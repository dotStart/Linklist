<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarFactory.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentList.class.php');

/**
 * Shows a list of all linklist link comments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentListPage extends MultipleLinkPage {
	// system
	public $templateName = 'linkListLinkCommentList';
	
	// parameters
	public $markedLinks = 0;
	
	/**
	 * comment list  object
	 * 
	 * @var	LinkListLinkCommentList
	 */
	public $commentList = null;
	
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * comment object
	 *
	 * @var ViewableLinkListLinkComment
	 */
	public $comment = null;
	
	/**
	 * sidebar factory object
	 * 
	 * @var	MessageSidebarFactory
	 */
	public $sidebarFactory = null;
	
	/**
	 * link id
	 *
	 * @var	integer
	 */
	public $linkID = 0;
	
	/**
	 * linklist link object
	 * 
	 * @var	ViewableLinkListLink
	 */
	public $link = null;
	
	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * linklist category object
	 * 
	 * @var	LinkListCategory
	 */
	public $category = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		// get link
		$this->link = new ViewableLinkListLink($this->linkID);
		
		// category id
		$this->categoryID = $this->link->categoryID;
		
		// get category
		$this->category = LinkListCategory::getCategory($this->categoryID);
		// enter category
		$this->category->enter();
		
		// enter link
		$this->link->enter($this->category);
		
		// init comment list
		$this->commentList = new LinkListLinkCommentList();
		$this->commentList->sqlConditions .= 'link_comment.linkID = '.$this->linkID;
		
		if (isset($_REQUEST['commentID'])) {
			$this->commentID = intval($_REQUEST['commentID']);
			$this->comment = new ViewableLinkListLinkComment($this->commentID);
			if (!$this->comment->commentID || $this->comment->linkID != $this->linkID) {
				throw new IllegalLinkException();
			}
			
			$sql = "SELECT	COUNT(*) AS links
				FROM 	wcf".WCF_N."_linkList_link_comment
				WHERE 	linkID = ".$this->linkID."
					AND time >= ".$this->link->time;
			$result = WCF::getDB()->getFirstRow($sql);
			$this->pageNo = intval(ceil($result['links'] / $this->itemsPerPage));
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read objects
		$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->commentList->sqlLimit = $this->itemsPerPage;
		$this->commentList->readObjects();
		
		// init sidebars
		$this->sidebarFactory = new MessageSidebarFactory($this);
		foreach ($this->commentList->getObjects() as $comment) {
			$this->sidebarFactory->create($comment);
		}
		$this->sidebarFactory->init();
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinks']);
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->commentList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'link' => $this->link,
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'comments' => $this->commentList->getObjects(),
			'sidebarFactory' => $this->sidebarFactory,
			'markedLinks' => $this->markedLinks,
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array()),
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permission
		$this->category->checkPermission('canViewComment');
		
		
		// check module options		
		if (!MODULE_LINKLIST && !LINKLIST_ENABLE_COMMENTS && !$this->category->allowComments) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>