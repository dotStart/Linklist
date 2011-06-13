<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkEditor.class.php');

/**
 * Executes moderation actions on links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkAction {
	/**
	 * linklist category editor object
	 * 
	 * @var LinkListCategoryEditor
	 */
	protected $category = null;
	
	/**
	 * linklist link editor object
	 * 
	 * @var LinkListLinkEditor
	 */
	protected $link = null;
	
	
	protected $categoryID = 0;
	protected $linkID = 0;
	protected $subject = '';
	protected $url = '';
	protected $linkIDs = null;
	protected $postIDs = null;
	
	/**
	 * Creates a new LinkListLinkAction object.
	 * 
	 * @param	LinkListCategoryEditor	$category
	 * @param	LinkListLinkEditor	$link
	 */
	public function __construct($category = null, $link = null, $linkID = 0, $subject = '', $url = '') {
		$this->category = $category;
		$this->link = $link;
		if ($linkID != 0) $this->linkID = $linkID;
		else if ($link) $this->linkID = $link->linkID;
		if ($category) $this->categoryID = $category->categoryID;
		$this->subject = $subject;
		$this->url = $url;
		
		// get marked links from session
		$this->getMarkedLinks();
	}
	
	/**
	 * Get marked links from session.
	 */
	public function getMarkedLinks() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			$this->linkIDs = implode(',', $sessionVars['markedLinkListLinks']);	
		}
	}
	
	/**
	 * Changes the subject of a link.
	 */
	public function changeSubject() {
		if (!WCF::getUser()->getPermission('mod.linkList.canEditLink')) {
			return;
		}
		
		if (!empty($this->link) && $this->link != null) {
			$this->link->updateData(array(
				'subject' => $this->subject
			));
		}
	}
	
	/**
	 * Marks a link.
	 */
	public function mark() {
		if ($this->link != null) {
			$this->link->mark();
		}
		else if (is_array($this->linkID)) {
			foreach ($this->linkID as $linkID) {
				$link = new LinkListLinkEditor($linkID);
				$link->enter();
				$link->mark();
			}
		}
	}
	
	/**
	 * Unmarks a link.
	 */
	public function unmark() {
		if ($this->link != null) {
			$this->link->unmark();
		}
		else if (is_array($this->linkID)) {
			foreach ($this->linkID as $linkID) {
				$link = new LinkListLinkEditor($linkID);
				$link->enter();
				$link->unmark();
			}
		}
	}
	
	/**
	 * Trashes the selected link.
	 */
	public function trash() {
		if (!LINKLIST_LINK_ENABLE_RECYCLE_BIN || !$this->link->isDeletable($this->category)) {
			return;
		}
		
		if ($this->link != null && !$this->link->isDeleted) {
			LinkListLinkEditor::trashAll($this->linkID);
			LinkListCategoryEditor::refreshAll($this->categoryID);
			LinkListCategoryEditor::resetCache();
		}
		
		if (strpos($this->url, 'page=LinkListLink') !== false) HeaderUtil::redirect('index.php?page=LinkListCategory&categoryID='.$this->category->categoryID.SID_ARG_2ND_NOT_ENCODED);
		else HeaderUtil::redirect($this->url);
		exit;
	}
	
	/**
	 * Deletes the selected link.
	 */
	public function delete() {
		if ($this->link == null) {
			throw new IllegalLinkException();
		}
		
		WCF::getUser()->checkPermission('mod.linkList.canDeleteLinkCompletely');
		
		// unmark link
		$this->link->unmark();
		// delete link
		LinkListLinkEditor::deleteAllCompletely($this->linkID);
		if (!$this->link->isDeleted || !LINKLIST_LINK_ENABLE_RECYCLE_BIN) {
			self::resetCache();
		
			// refresh category
			$this->category->refresh();
		}
		// redirect 
		HeaderUtil::redirect('index.php?page=LinkListCategory&categoryID='.$this->category->categoryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * Recovers the selected link.
	 */
	public function recover() {
		if (!WCF::getUser()->getPermission('mod.linkList.canDeleteLinkCompletely')) {
			return;
		}
		
		if ($this->link != null && $this->link->isDeleted) {
			// removes the link from the trash.
			LinkListLinkEditor::restoreAll($this->linkID);
			// refresh category
			LinkListCategoryEditor::refreshAll($this->categoryID);
			LinkListCategoryEditor::resetCache();
		}
	}
	
	/**
	 * Disables the selected link.
	 */
	public function disable() {
		if (!WCF::getUser()->getPermission('mod.linkList.canEnableLink')) {
			return;
		}
		
		if ($this->link != null && !$this->link->isDisabled) {
			if (!$this->link->everEnabled) {
				if (WCF::getUser()->userID) {
					// add activity points
					if (LINKLIST_ACTIVITY_POINTS_PER_LINK) {
						require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
						UserRank::updateActivityPoints(LINKLIST_ACTIVITY_POINTS_PER_LINK);
					}
				}
			}
			// disable link
			LinkListLinkEditor::disableAll($this->linkID);
			// refresh category
			LinkListCategoryEditor::refreshAll($this->categoryID);
			LinkListCategoryEditor::resetCache();
		}
	}
	
	/**
	 * Enables the selected link.
	 */
	public function enable() {
		if (!WCF::getUser()->getPermission('mod.linkList.canEnableLink')) {
			return;
		}

		if ($this->link != null && $this->link->isDisabled) {
			// enable link
			LinkListLinkEditor::enableAll($this->linkID);
			// refresh category
			LinkListCategoryEditor::refreshAll($this->categoryID);
			LinkListCategoryEditor::resetCache();
		}
	}
	
	/**
	 * Closes the selected link.
	 */
	public function close() {
		if (!WCF::getUser()->getPermission('mod.linkList.canCloseLink')) {
			return;
		}
		
		if ($this->link != null && !$this->link->isClosed) {
			LinkListLinkEditor::closeAll($this->linkID);
		}
	}
	
	/**
	 * Sticks a link.
	 */
	public function stick() {
		// check permission
		if (!WCF::getUser()->getPermission('mod.linkList.canPinLink')) {
			return;
		}
		
		if ($this->link != null && !$this->link->isSticky) {
			// stich link
			LinkListLinkEditor::stickAll($this->linkID);
		}
	}
	
	/**
	 * Unsticks a link.
	 */
	public function unstick() {
		// check permission
		if (!WCF::getUser()->getPermission('mod.linkList.canPinLink')) {
			return;
		}
		
		if ($this->link != null && $this->link->isSticky) {
			// unstick link
			LinkListLinkEditor::unstickAll($this->linkID);
		}
	}
	
	/**
	 * Closes all marked links.
	 */
	public function closeAll() {
		// check permissions
		WCF::getUser()->checkPermission('mod.linkList.canCloseLink');
		
		// close all links
		LinkListLinkEditor::closeAll($this->linkIDs);
		LinkListLinkEditor::unmarkAll();
		
		HeaderUtil::redirect($this->url);
		exit;
	}
	
	/**
	 * Opens all marked links.
	 */
	public function openAll() {
		// check permissions
		WCF::getUser()->checkPermission('mod.linkList.canCloseLink');
		
		// close all links
		LinkListLinkEditor::openAll($this->linkIDs);
		LinkListLinkEditor::unmarkAll();
		
		HeaderUtil::redirect($this->url);
		exit;
	}
	
	/**
	 * Opens the selected links.
	 */
	public function open() {
		if (!WCF::getUser()->getPermission('mod.linkList.canCloseLink')) {
			return;
		}
		
		if ($this->link != null && $this->link->isClosed) {
			LinkListLinkEditor::openAll($this->linkID);
		}
	}
	
	/**
	 * Deletes all marked links
	 */
	public function deleteAll() {
		if (!empty($this->linkIDs)) {
			WCF::getUser()->checkPermission('mod.linkList.canDeleteLink');
			list($categories, $categoryIDs) = LinkListLinkEditor::getCategories($this->linkIDs);
			
			LinkListLinkEditor::deleteAll($this->linkIDs);
			LinkListLinkEditor::unmarkAll();
			
			// refresh counts
			LinkListCategoryEditor::refreshAll($categoryIDs);
			LinkListCategoryEditor::resetCache();
		}
		HeaderUtil::redirect($this->url);
		exit;
	}
	
	/**
	 * Recovers all marked links.
	 */
	public function recoverAll() {
		if (!empty($this->linkIDs)) {
			list($categories, $categoryIDs) = LinkListLinkEditor::getCategories($this->linkIDs);
			
			// revocer all marked links
			LinkListLinkEditor::restoreAll($this->linkIDs);
			// unmark all marked links.
			LinkListLinkEditor::unmarkAll();
			
			// refresh category
			LinkListCategoryEditor::refreshAll($this->categoryID);
			LinkListCategoryEditor::resetCache();
		}
		HeaderUtil::redirect($this->url);
		exit;
	}
	
	/**
	 * Moves the marked links.
	 */
	public function move() {
		if ($this->category == null) {
			throw new IllegalLinkException();
		}
		
		WCF::getUser()->checkPermission('mod.linkList.canMoveLink');
		
		list($categories, $categoryIDs) = LinkListLinkEditor::getCategories($this->linkIDs);
		
		// move all links
		LinkListLinkEditor::moveAll($this->linkIDs, $this->categoryID);
		// unmark moved links
		LinkListLinkEditor::unmarkAll();
		
		// refresh counts
		LinkListCategoryEditor::refreshAll($categoryIDs.','.$this->category->categoryID);
		LinkListCategoryEditor::resetCache();

		HeaderUtil::redirect($this->url);
		exit;
	}

	/**
	 * Unmarks all marked links.
	 */
	public static function unmarkAll() {
		LinkListLinkEditor::unmarkAll();
	}
}
?>