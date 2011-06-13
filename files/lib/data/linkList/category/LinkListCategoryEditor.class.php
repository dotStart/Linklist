<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * LinkListCategoryEditor provides functions to edit the data of a category.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryEditor extends LinkListCategory {	
	/**
	 * Creates a new LinkListCategoryEditor object.
	 * @see LinkListCategory::__construct()
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($categoryID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_linkList_category
				WHERE	categoryID = ".$categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Updates the link count of this category.
	 * 
	 * @param	integer		$links
	 */
	public function updateLinks($links = 1, $categoryID = 1) {
		// get link id
		if ($this->categoryID) {
			$categoryID = $this->categoryID;
		}
		
		// update link count
		$sql = "UPDATE	wcf".WCF_N."_linkList_category
			SET	links = links + ".$links."
			WHERE 	categoryID = ".$categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates the link counter for this category.
	 */
	public function refresh() {
		$this->refreshAll($this->categoryID);
	}
	
	/**
	 * Updates the link counter and comment counter for the given links
	 * 
	 * @param	string		$linkIDs
	 */
	public static function refreshAll($categoryIDs) {
		if (empty($categoryIDs)) return;
		
		$sql = "UPDATE wcf".WCF_N."_linkList_category linkList_category
			SET	links = (
					SELECT	COUNT(*)
					FROM	wcf".WCF_N."_linkList_link
					WHERE	categoryID = linkList_category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
					),
				comments = (
					SELECT	COUNT(*)
					FROM	wcf".WCF_N."_linkList_link_comment
					WHERE	categoryID = linkList_category.categoryID
					),
				visits = (
					SELECT	IFNULL(SUM(visits), 0)
					FROM	wcf".WCF_N."_linkList_link
					WHERE	categoryID = linkList_category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
					)
			WHERE	categoryID IN (".$categoryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Deletes the data of categories
	 */
	public static function deleteAll($categoryIDs) {
		// delete group permissions		
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category_to_group
			WHERE		categoryID IN (".$categoryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete category
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category
			WHERE		categoryID IN (".$categoryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this category
	 */
	public function delete() {
		// get all link ids
		$linkIDs = '';
		$sql = "SELECT	linkID
			FROM	wcf".WCF_N."_linkList_link
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($linkIDs)) $linkIDs .= ',';
			$linkIDs .= $row['linkID'];
		}
		if (!empty($linkIDs)) {
			// delete links of this category
			require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkEditor.class.php');
			LinkListLinkEditor::deleteAllCompletely($linkIDs);
		}
		
		$this->removePositions();
		
		// update sub categories
		$sql = "UPDATE	wcf".WCF_N."_linkList_category
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "UPDATE	wcf".WCF_N."_linkList_category_structure
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category
		self::deleteAll($this->categoryID);
	}
	
	/**
	 * Removes a category from all positions in category tree.
	 */
	public function removePositions() {
		// unshift categories
		$sql = "SELECT 	parentID, position
			FROM	wcf".WCF_N."_linkList_category_structure
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "UPDATE	wcf".WCF_N."_linkList_category_structure
				SET	position = position - 1
				WHERE 	parentID = ".$row['parentID']."
					AND position > ".$row['position'];
			WCF::getDB()->sendQuery($sql);
		}
		
		// delete category structure
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category_structure
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds a category to a specific position in the category tree.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public function addPosition($parentID, $position = null) {
		// shift categories
		if ($position !== null) {
			$sql = "UPDATE	wcf".WCF_N."_linkList_category_structure
				SET	position = position + 1
				WHERE 	parentID = ".$parentID."
					AND position >= ".$position;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get final position
		$sql = "SELECT 	IFNULL(MAX(position), 0) + 1 AS position
			FROM	wcf".WCF_N."_linkList_category_structure
			WHERE	parentID = ".$parentID."
				".($position ? "AND position <= ".$position : '');
		$row = WCF::getDB()->getFirstRow($sql);
		$position = $row['position'];
		
		// save position
		$sql = "INSERT INTO	wcf".WCF_N."_linkList_category_structure
					(parentID, categoryID, position)
			VALUES		(".$parentID.", ".$this->categoryID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the data of a category.
	 *
	 * @param 	array		$fields
	 */
	public function updateData($fields = array()) { 
		$updates = '';
		foreach ($fields as $key => $value) {
			if (!empty($updates)) $updates .= ',';
			$updates .= $key.'=';
			if (is_int($value)) $updates .= $value;
			else $updates .= "'".escapeString($value)."'";
		}
		
		if (!empty($updates)) {
			$sql = "UPDATE	wcf".WCF_N."_linkList_category
				SET	".$updates."
				WHERE	categoryID = ".$this->categoryID;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Creates a new category.
	 * 
	 * @return	LinkListCategoryEditor
	 */
	public static function create($parentID, $position, $title, $description = '', $categoryType = 0, $image = '', $allowDescriptionHtml = 0, $allowComments = -1, $additionalFields = array()) {
		// save category data
		$categoryID = self::insert($title, array_merge($additionalFields, array(
			'parentID' => $parentID,
			'description' => $description,
			'categoryType' => $categoryType,
			'image' => $image,
			'time' => TIME_NOW,
			'allowDescriptionHtml' => $allowDescriptionHtml,
			'allowComments' => $allowComments
		)));
		
		// get category
		$category = new LinkListCategoryEditor($categoryID, null, null, false);
		
		// save position
		$category->addPosition($parentID, $position);
		
		// return category
		return $category;
	}
	
	/**
	 * Creates the category row in database table.
	 *
	 * @param 	string 		$title
	 * @param 	array		$additionalFields
	 * @return	integer		new category id
	 */
	public static function insert($title, $additionalFields = array()) { 
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}
		
		$sql = "INSERT INTO	wcf".WCF_N."_linkList_category
					(title
					".$keys.")
			VALUES		('".escapeString($title)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
	
	/**
	 * Saves the user and group permissions.
	 *
	 * @param 	array 		$permissions
	 * @param 	array		$permissionSettings
	 */
	public function savePermissions($permissions, $permissionSettings) {
		// create inserts
		$groupInserts = '';
		foreach ($permissions as $key => $permission) {
			// skip default values
			$noDefaultValue = false;
			foreach ($permission['settings'] as $value) {
				if ($value != -1) $noDefaultValue = true;
			}
			if (!$noDefaultValue) {
				unset($permissions[$key]);
				continue;
			}

			if (!empty($groupInserts)) $groupInserts .= ',';
			$groupInserts .= '('.$this->categoryID.',
					 '.intval($permission['id']).',
					 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
		}
		
		if (!empty($groupInserts)) {
			$sql = "INSERT INTO	wcf".WCF_N."_linkList_category_to_group
						(categoryID, groupID, ".implode(', ', $permissionSettings).")
				VALUES		".$groupInserts;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Deletes the old permissions.
	 */
	public function deletePermissions() {
		// delete group permissions
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category_to_group
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the position of a category directly.
	 *
	 * @param	integer		$categoryID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($categoryID, $parentID, $position) {
		$sql = "UPDATE	wcf".WCF_N."_linkList_category
			SET	parentID = ".$parentID."
			WHERE 	categoryID = ".$categoryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "REPLACE INTO	wcf".WCF_N."_linkList_category_structure
					(categoryID, parentID, position)
			VALUES		(".$categoryID.", ".$parentID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
}
?>