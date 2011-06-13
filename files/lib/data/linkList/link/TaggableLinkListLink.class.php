<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/TaggedLinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * An implementation of Taggable to support the tagging of linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class TaggableLinkListLink extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_linkList_link
			WHERE		linkID IN (" . implode(",", $objectIDs) . ")
				AND isDeleted = 0
				AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedLinkListLink(null, $row);
		}
		return $taggedObjects;
	}
	
	/**
	 * @see Taggable::countObjectsByTagID()
	 */
	public function countObjectsByTagID($tagID) {
		if (!WCF::getUser()->getPermission('user.linkList.canViewLink')) {
			return 0;
		}
		
		$accessibleCategoryIDArray = LinkListCategory::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) return 0;
		
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wcf".WCF_N."_linkList_link linkList_link
			ON		(linkList_link.linkID = tag_to_object.objectID)
			WHERE	tagID = ".$tagID."
				AND taggableID = ".$this->getTaggableID()."
				AND linkList_link.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
				AND linkList_link.isDeleted = 0
				AND linkList_link.isDisabled = 0";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see Taggable::getObjectsByTagID()
	 */
	public function getObjectsByTagID($tagID, $limit = 0, $offset = 0) {
		if (!WCF::getUser()->getPermission('user.linkList.canViewLink')) {
			return array();
		}
		
		$accessibleCategoryIDArray = LinkListCategory::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) return array();
		
		$links = array();
		$sql = "SELECT		linkList_link.*, user_table.username
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wcf".WCF_N."_linkList_link linkList_link
			ON		(linkList_link.linkID = tag_to_object.objectID)
			LEFT JOIN	wcf".WCF_N."_user user_table
			ON		(user_table.userID = linkList_link.userID)
			WHERE		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND linkList_link.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
					AND linkList_link.isDeleted = 0
					AND linkList_link.isDisabled = 0
			ORDER BY	linkList_link.lastChangeTime DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$links[] = new TaggedLinkListLink(null, $row);
		}
		return $links;
	}

	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'linkID';
	}
	
	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedLinkListLinks';
	}
	
	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkS.png');
	}

	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkM.png');
	}
	
	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkL.png');
	}
}
?>