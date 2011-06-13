<?php
// wcf imports
require_once(WCF_DIR.'lib/form/LinkListLinkCommentAddForm.class.php');

/**
 * Shows the form for editing a linklist link comment.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentEditForm extends LinkListLinkCommentAddForm {
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * comment editor object
	 *
	 * @var LinkListLinkCommentEditor
	 */
	public $comment = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		// get comment id
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
		// create a new LinkListLinkCommentEditor instance
		$this->comment = new LinkListLinkCommentEditor($this->commentID);
		
		// check if comment exists
		if (!$this->comment->commentID) {
			throw new IllegalLinkException();
		}
		
		// link id
		$this->linkID = $this->comment->linkID;
		
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
		
		// check permissions
		if (!$this->comment->isEditable($this->category, $this->link)) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// save comment
		$this->comment->update($this->text, $this->getOptions());
		LinkListCategoryEditor::refreshAll($this->categoryID);
		
		// call event
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=LinkListLinkCommentList&linkID='.$this->linkID.'&commentID='.$this->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->commentID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// default values
		if (!count($_POST)) {
			$this->text = $this->comment->message;
			$this->enableSmilies =  $this->comment->enableSmilies;
			$this->enableHtml = $this->comment->enableHtml;
			$this->enableBBCodes = $this->comment->enableBBCodes;
			$this->showSignature = $this->comment->enableBBCodes;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'commentID' => $this->commentID
		));
	}
}
?>