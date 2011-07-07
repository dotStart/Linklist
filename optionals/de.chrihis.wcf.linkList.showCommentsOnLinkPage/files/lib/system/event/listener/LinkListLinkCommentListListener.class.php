<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentList.class.php');

/**
 * Shows the comment list on the link page and remove the commentlist page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.showCommentsOnLinkPage
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentListListener extends MultipleLinkPage implements EventListener {
	/**
	 * link id
	 *
	 * @var	integer
	 */
	public $linkID = 0;
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
	public $itemsPerPage = 10;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!WCF::getUser()->getPermission('user.linkList.canViewComment')) return;
		switch ($className) {
			case 'LinkListLinkPage':
				switch ($eventName) {
					// fire events
					case 'readParameters':
						// init comment list
						$this->commentList = new LinkListLinkCommentList();
						$this->commentList->sqlConditions .= 'link_comment.linkID = '.$this->linkID;
		
						if (isset($_REQUEST['commentID'])) {
							$this->commentID = intval($_REQUEST['commentID']);
							$this->comment = new ViewableLinkListLinkComment($this->commentID);
							if (!$this->comment->commentID || $this->comment->linkID != $eventObj->linkID) {
								throw new IllegalLinkException();
							}
			
							$sql = "SELECT	COUNT(*) AS links
								FROM 	wcf".WCF_N."_linkList_link_comment
								WHERE 	linkID = ".$eventObj->linkID."
									AND time >= ".$eventObj->link->time;
							$result = WCF::getDB()->getFirstRow($sql);
							$this->pageNo = intval(ceil($result['links'] / $this->itemsPerPage));
						}
						break;
				
					case 'readData':
						// read objects
						$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
						$this->commentList->sqlLimit = $this->itemsPerPage;
						$this->commentList->readObjects();
						break;
			
					case 'assignVariables':
						// assign variables
						WCF::getTPL()->assign(array(
							'link' => $eventObj->link,
							'linkID' => $eventObj->linkID,
							'category' => $eventObj->category,
							'comments' => $this->commentList->getObjects()
						));
				
						WCF::getTPL()->append(array(
							'additionalFirstColumnContent' => WCF::getTPL()->fetch('linkListLinkAlternativeCommentList')
						));
						break;
				}
				break;
			case 'LinkListLinkCommentAddForm':
				// forward
				HeaderUtil::redirect('index.php?page=LinkListLink&linkID='.$eventObj->linkID.'&commentID='.$eventObj->comment->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$eventObj->comment->commentID);
				exit;
				break;
			case 'LinkListLinkCommentEditForm':
				// forward
				HeaderUtil::redirect('index.php?page=LinkListLink&linkID='.$eventObj->linkID.'&commentID='.$eventObj->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$eventObj->commentID);
				exit;
				break;
			case 'LinkListLinkCommentListPage':
				throw new IllegalLinkException();
				break;
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);

		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_link_comment
			WHERE linkID = ".$this->linkID;
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}	
}
?>
