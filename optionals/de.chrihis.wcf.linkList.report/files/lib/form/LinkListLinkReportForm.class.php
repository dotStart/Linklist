<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the form to report a link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.report
 * @subpackage form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkReportForm extends MessageForm {
	// system
	public $templateName = 'linkListLinkReport';
	public $useCaptcha = 1;
	
	// parameters
	public $markedLinks = 0;
	
	/**
	 * link id
	 * 
	 * @var	integer
	 */
	public $linkID = 0;
	
	/**
	 * viewable linklist link object
	 * 
	 * @var	ViewableLinkListLink
	 */
	public $link = null;
	
	/**
	 * linklist category object
	 * 
	 * @var	LinkListCategory
	 */
	public $category = null;
	
	/**
	 * report id
	 * 
	 * @var	integer
	 */
	public $reportID = 0;
	
	/**
	 * username
	 * 
	 * @var	string
	 */
	public $username = '';
	
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
		
		// check, if this link was already reported.
		$sql = "SELECT 	linkID
			FROM	wcf".WCF_N."_linkList_link_report
			WHERE	linkID = ".$this->linkID;
		$row = WCF::getDB()->getFirstRow($sql);
		if (isset($row['linkID'])) {
			throw new NamedUserException(WCF::getLanguage()->get('wcf.linkList.link.report.alreadyReported'));
		}
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinks']);
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// username
		$this->validateUsername();
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() {}
	
	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		// only for guests
		if (WCF::getUser()->userID == 0) {
			// username
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}
			
			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save report in database
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_linkList_link_report
						(linkID, userID, username, report, reportTime)
			VALUES 			(".$this->linkID.", ".WCF::getUser()->userID.", '".escapeString($this->username)."',
						'".escapeString($this->text)."', ".TIME_NOW.")";
		WCF::getDB()->sendQuery($sql);
		// get report id
		$this->reportID = WCF::getDB()->getInsertID();
		
		// update linkList_link table
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isReported = 1
			WHERE 	linkID = ".$this->linkID;
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// call event
		$this->saved();
		
		HeaderUtil::redirect('index.php?page=LinkListLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
	}
	
	/**
	 * @see Form::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'link' => $this->link,
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'username' => $this->username,
			'markedLinks' => $this->markedLinks,
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array()),
		));
		
		WCF::getTPL()->append('additionalSelection', WCF::getTPL()->fetch('linkListLinkReportButton'));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// check permissions
		WCF::getUser()->checkPermission('user.linkList.canReportLink');
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check module options		
		if (!MODULE_LINKLIST && !LINKLIST_LINK_ENABLE_REPORT) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>