<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows a detailed view of a linklist link and the comments of this link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkPage extends AbstractPage {
	// system
	public $templateName = 'linkListLink';
	
	// parameters
	public $markedLinks = 0;
	
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
	 * attachment list object
	 * 
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;
	
	/**
	 * list of attachments
	 * 
	 * @var	array<Attachment>
	 */
	public $attachments = array();
	
	/**
	 * list of the last visitors
	 *
	 * @var array<UserProfile>
	 */
	public $lastVisitors = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {		
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
		
		// call event
		parent::readParameters();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read attachments
		if (MODULE_ATTACHMENT == 1 && $this->link->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->linkID, 'linkListLink', '', WCF::getPackageID('de.chrihis.wcf.linkList'));
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments($this->category->getPermission('canViewAttachmentPreview'));
			
			// set embedded attachments
			if ($this->category->getPermission('canViewAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}
			
			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinks']);
		}
		
		if (LINKLIST_LINK_SHOW_LASTVISITORS) {
			$sql = "SELECT	avatar.*, user_table.*, link_visitor.*
					FROM		wcf".WCF_N."_linkList_link_last_visitor link_visitor
					LEFT JOIN 	wcf".WCF_N."_user user_table
					ON 		(user_table.userID = link_visitor.userID)
					LEFT JOIN 	wcf".WCF_N."_avatar avatar
					ON 		(avatar.avatarID = user_table.avatarID)
					WHERE		linkID = ".$this->linkID."
							AND user_table.userID IS NOT NULL
					ORDER BY	link_visitor.time DESC";
			$result = WCF::getDB()->sendQuery($sql, 5);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->lastVisitors[] = new UserProfile(null, $row);
			}
		}
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
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array()),
			'url' => 'index.php?page=LinkListLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED,
			'attachments' => $this->attachments,
			'markedLinks' => $this->markedLinks,
			'lastVisitors' => $this->lastVisitors,
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check module options		
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>