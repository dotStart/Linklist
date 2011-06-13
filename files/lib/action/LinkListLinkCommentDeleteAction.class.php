<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentEditor.class.php');
 
/**
 * Deletes a linklist link comment.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentDeleteAction extends AbstractSecureAction {
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * linklist comment editor object
	 *
	 * @var LinkListCommentEditor
	 */
	public $comment = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
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
		$this->link = new LinkListLink($this->linkID);
		
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
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// delete comment
		$this->comment->delete($this->linkID);
		
		// refresh comments counter in category
		LinkListCategoryEditor::refreshAll($this->categoryID);
		LinkListCategory::resetCache();
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=LinkListLinkCommentList&linkID='.$this->comment->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>