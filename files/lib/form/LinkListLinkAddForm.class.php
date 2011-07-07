<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkEditor.class.php');
require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the form for adding new links to the linklist.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkAddForm extends MessageForm {
	// system
	public $templateName = 'linkListLinkAdd';
	public $showPoll = false;
	public $showSignatureSetting = false;
	public $preview, $send;
	public $useCaptcha = 1;
	
	/**
	 * category id
	 * 
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * category editor object
	 * 
	 * @var	LinkListCategory
	 */
	public $category = null;
	
	/**
	 * link id
	 * 
	 * @var	integer
	 */
	public $linkID = 0;
	
	// parameters
	public $isSticky = 0;
	public $url = '';
	public $shortDescription = '';
	public $tags = '';
	public $username = '';
	
	/**
	 * attachment list editor
	 * 
	 * @var	AttachmentListEditor
	 */
	public $attachmentListEditor = null;	
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		// get a new LinkListCategory instance
		$this->category = new LinkListCategoryEditor($this->categoryID);
		// enter category
		$this->category->enter();
		
		// check permissions
		if (!$this->category->getPermission('canAddLink') && $this->category->isCategory) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// parameters
		if (isset($_POST['isSticky'])) $this->isSticky = (boolean) $_POST['isSticky'];
		if (isset($_POST['url'])) $this->url = StringUtil::trim($_POST['url']);
		if (isset($_POST['shortDescription'])) $this->shortDescription = StringUtil::trim($_POST['shortDescription']);
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// attachment handling
			if ($this->showAttachments) {
				$this->attachmentListEditor->handleRequest();
			}
				
			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', LinkListLinkEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
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
		
		// validate username
		$this->validateUsername();
		
		// validate url
		if (!FileUtil::isURL($this->url)) {
			throw new UserInputException('url', 'illegalURL');
		}
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
		
		// save link
		$this->link = LinkListLinkEditor::create($this->categoryID, $this->subject, $this->shortDescription, $this->text, $this->username, $this->url, $this->isSticky, !$this->category->getPermission('canAddLinkWithoutModeration'), $this->getOptions(), $this->attachmentListEditor);
		
		// save tags
		if (MODULE_TAGGING) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $this->link->updateTags($tagArray);
		}
		
		// call event
		$this->saved();
		
		if ($this->category->getPermission('canAddLinkWithoutModeration')) {
			// refresh category
			LinkListCategoryEditor::refreshAll($this->categoryID);
			
			// clear cache
			LinkListCategory::resetCache();
			
			if (WCF::getUser()->userID) {
				// add activity points
				if (LINKLIST_ACTIVITY_POINTS_PER_LINK) {
					require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
					UserRank::updateActivityPoints(LINKLIST_ACTIVITY_POINTS_PER_LINK);
				}
			}
			
			// forward
			HeaderUtil::redirect('index.php?page=LinkListLink&linkID='.$this->link->linkID.SID_ARG_2ND_NOT_ENCODED);
		}
		else {
			// redirect to url
			WCF::getTPL()->assign(array(
				'url' => 'index.php?page=LinkListCategory&categoryID='.$this->categoryID.SID_ARG_2ND_NOT_ENCODED,
				'message' => WCF::getLanguage()->get('wcf.linkList.link.add.success.moderation'),
				'wait' => 5
			));
			WCF::getTPL()->display('redirect');
		}		
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'linkID' => $this->linkID,
			'action' => 'add',
			'isSticky' => $this->isSticky,
			'url' => $this->url,
			'shortDescription' => $this->shortDescription,
			'tags' => $this->tags,
			'username' => $this->username
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
		
		// check upload permission
		if (MODULE_ATTACHMENT != 1 || !$this->category->getPermission('canUploadAttachment')) {
			$this->showAttachments = false;
		}
		
		// get attachments editor
		if ($this->attachmentListEditor == null) {
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'linkListLink', WCF::getPackageID('de.chrihis.wcf.linkList'), WCF::getUser()->getPermission('user.linkList.maxAttachmentSize'), WCF::getUser()->getPermission('user.linkList.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.linkList.maxAttachmentCount'));
		}
		
		parent::show();
	}
}
?>