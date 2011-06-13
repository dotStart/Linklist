<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a linklist link.
*
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLink extends DatabaseObject {
	/**
	 * Creates a new LinkListLink object.
	 *
	 * @param	integer		$linkID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($linkID, $row = null) {
		if ($linkID !== null) {
			$sql = "SELECT	linkList_link.*
				FROM 		wcf".WCF_N."_linkList_link linkList_link
				WHERE 		linkList_link.linkID = ".$linkID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}
	
	/**
	 * Returns true, if this link is marked.
	 */
	public function isMarked() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			if (in_array($this->linkID, $sessionVars['markedLinkListLinks'])) return 1;
		}
		return 0;
	}
	
	/**
	 * Enters the active user to this link.
	 */
	public function enter($category = null) {
		if (!$this->linkID) {
			throw new IllegalLinkException();
		}
		
		if ($category == null || $category->categoryID != $this->categoryID) {
			// include LinkListCategory class
			require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
			// get category by category id
			$category = LinkListCategory::getCategory($this->categoryID);
		}
		
		$category->enter();
		
		// get permission
		$this->canEnterLink = ((!$this->isDisabled || WCF::getUser()->getPermission('mod.linkList.canEnableLink') || $this->isOwnLink()) && (!$this->isDeleted || WCF::getUser()->getPermission('mod.linkList.canDeleteLinkCompletely')));
		// check permission
		if (!$this->canEnterLink || !$category->getPermission('canViewLink')) {
			throw new PermissionDeniedException();
		}
			
		// save category
		$this->category = $category;
	}
	
	/**
	 * Returns true, if the active user added this link.
	 * 
	 * @return	boolean
	 */
	public function isOwnLink() {
		return ($this->userID == WCF::getUser()->userID && WCF::getUser()->userID);
	}
	
	/**
	 * Returns the tags of this link.
	 * 
	 * @return	array<Tag>
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WCF_DIR.'lib/data/linkList/link/TaggedLinkListLink.class.php');
		
		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedLinkListLink(null, array(
			'linkID' => $this->linkID,
			'taggable' => TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link')
		)), $languageIDArray);
	}	
	
	/**
	 * Returns true, if the active user can edit this link.
	 * 
	 * @param	LinkListCategory		$category
	 * @return	boolean
	 */
	public function isEditable($category) {
		// check permissions
		if (WCF::getUser()->getPermission('mod.linkList.canEditLink') || $this->isOwnLink() && $category->getPermission('canEditOwnLink')) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns true, if the active user can delete this link.
	 * 
	 * @param	LinkListCategory		$category
	 * @return	boolean
	 */
	public function isDeletable($category) {
		// check permissions
		if (WCF::getUser()->getPermission('mod.linkList.canDeleteLink') || $this->isOwnLink() && $category->getPermission('canDeleteOwnLink')) {
			return true;
		}
		
		return false;
	}
}
?>