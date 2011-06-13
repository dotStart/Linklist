<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentEditor.class.php');

/**
 * Shows the form for adding a new linklist link comment.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentAddForm extends MessageForm {
	// system
	public $templateName = 'linkListLinkCommentAdd';
	public $showAttachments = false;
	public $showPoll = false;
	public $preview, $send;
	public $useCaptcha = 1;
	
	// form parameters
	public $username = '';
	
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
		
		if ($this->link->isClosed && !$this->user->getPermission('mod.linkList.canCloseLink')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// preview
			if ($this->preview) {
				WCF::getTPL()->assign('preview', LinkListLinkCommentEditor::createPreview($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}
			// save message
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
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
		
		// save new comment
		$this->comment = LinkListLinkCommentEditor::create($this->linkID, $this->categoryID, $this->username, $this->text, $this->getOptions());
		
		// refresh comments counter in category
		LinkListCategoryEditor::refreshAll($this->categoryID);
		LinkListCategory::resetCache();
		
		if (WCF::getUser()->userID) {
			// add activity points
			if (LINKLIST_ACTIVITY_POINTS_PER_LINK) {
				require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
				UserRank::updateActivityPoints(LINKLIST_ACTIVITY_POINTS_PER_COMMENT);
			}
		}

		// call event
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=LinkListLinkCommentList&linkID='.$this->linkID.'&commentID='.$this->comment->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->comment->commentID);
		exit;
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
			'action' => 'add',
			'username' => $this->username
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permission
		$this->category->checkPermission('canAddComment');
		
		// check module options		
		if (!MODULE_LINKLIST && !LINKLIST_ENABLE_COMMENTS && !$this->category->allowComments) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() {}
}
?>