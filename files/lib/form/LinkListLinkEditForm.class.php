<?php
// wcf imports
require_once(WCF_DIR.'lib/form/LinkListLinkAddForm.class.php');

/**
 * Shows form for edit links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkEditForm extends LinkListLinkAddForm {	
	/**
	 * link id
	 *
	 * @var integer
	 */
	public $linkID = 0;
	
	/**
	 * linklist link editor object
	 *
	 * @var LinkListLinkEditor
	 */
	public $link = null;
	
	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * linklist category editor object
	 *
	 * @var LinkListCategoryEditor
	 */
	public $category = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();

		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		// get a new LinkListLinkEditor instance
		$this->link = new LinkListLinkEditor($this->linkID);
		
		// get a new LinkListCategory instance
		$this->category = new LinkListCategoryEditor($this->link->categoryID);
		// enter category
		$this->category->enter();
		
		// enter link
		$this->link->enter($this->category);
		
		// check permissions
		if (!$this->category->getPermission('canEditOwnLink') && $this->category->isCategory) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// get number of attachments
		$attachmentsAmount = ($this->attachmentListEditor !== null ? count($this->attachmentListEditor->getAttachments($this->linkID)) : 0);
		
		// update link
		$this->link->update($this->subject, $this->text, $this->shortDescription, $this->url, $this->isSticky, intval(!$this->category->getPermission('canAddLinkWithoutModeration')), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, $attachmentsAmount);
		
		// update attachments
		if ($this->attachmentListEditor != null) {
			$this->attachmentListEditor->findEmbeddedAttachments($this->text);
		}
		
		// save tags
		if (MODULE_TAGGING) {
			$this->link->updateTags(TaggingUtil::splitString($this->tags));
		}
		
		// call event
		$this->saved();
		
		// forward to entry
		HeaderUtil::redirect('index.php?page=LinkListLink&linkID='.$this->link->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->subject = $this->link->subject;
			$this->shortDescription = $this->link->shortDescription;
			$this->text = $this->link->message;
			$this->url = $this->link->url;
			$this->isSticky = $this->link->isSticky;
			$this->attachments = $this->link->attachments;
			$this->enableSmilies =  $this->link->enableSmilies;
			$this->enableHtml = $this->link->enableHtml;
			$this->enableBBCodes = $this->link->enableBBCodes;
			
			// tags
			if (MODULE_TAGGING) {
				$this->tags = TaggingUtil::buildString($this->link->getTags(array((count(Language::getAvailableContentLanguages()) > 0 ? WCF::getLanguage()->getLanguageID() : 0))));
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
			'action' => 'edit',
			'link' => $this->link,
			'linkID' => $this->linkID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// get attachments
		require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->linkID), 'linkListLink', WCF::getPackageID('de.chrihis.wcf.linkList'), WCF::getUser()->getPermission('user.linkList.maxAttachmentSize'), WCF::getUser()->getPermission('user.linkList.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.linkList.maxAttachmentCount'));
		
		parent::show();
	}
}
?>