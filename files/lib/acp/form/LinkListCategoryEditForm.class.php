<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/LinkListCategoryAddForm.class.php');

/**
 * Shows the category edit form.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryEditForm extends LinkListCategoryAddForm {	
	/**
	 * category id
	 * 
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * existing category structure
	 * 
	 * @var	array
	 */
	public static $categoryStructure;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		
		// get category
		$this->category = new LinkListCategoryEditor($this->categoryID);
	}
	
	/**
	 * @see LinkListCategoryAddForm::validateParentID()
	 */
	protected function validateParentID() {
		parent::validateParentID();
		
		if ($this->parentID) {
			if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');
			if ($this->categoryID == $this->parentID || $this->searchChildren($this->categoryID, $this->parentID)) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}
	
	/**
	 * Searches for a category in the child tree of another category.
	 */
	protected function searchChildren($parentID, $searchedCategoryID) {
		if (isset(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				if ($categoryID == $searchedCategoryID) return true;
				if ($this->searchChildren($categoryID, $searchedCategoryID)) return true;
			}
		}
		return false;
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
			
		// update data
		$this->category->updateData(array(
			'parentID' => $this->parentID,
			'title' => $this->title,
			'description' => $this->description,
			'categoryType' => $this->categoryType,
			'image' => $this->image,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'allowComments' => $this->allowComments
		));
		
		// remove the old position
		$this->category->removePositions();
		
		// adds the new position
		$this->category->addPosition($this->parentID, ($this->position ? $this->position : null));

		// delete old permissions
		$this->category->deletePermissions();
		// save new permissions
		$this->category->savePermissions($this->permissions, $this->permissionSettings);
		
		// reset cache
		$this->category->resetCache();
		
		// call event
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {			
			// get values
			$this->categoryType = $this->category->categoryType;
			$this->parentID = $this->category->parentID;
			$this->title = $this->category->title;
			$this->description = $this->category->description;
			$this->image = $this->category->image;
			$this->allowDescriptionHtml = $this->category->allowDescriptionHtml;
			$this->allowComments = $this->category->allowComments;
			
			// get position
			$sql = "SELECT	position
				FROM	wcf".WCF_N."_linkList_category_structure
				WHERE	categoryID = ".$this->categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (isset($row['position'])) $this->position = $row['position'];
			
			// get permissions
			$sql = "	(SELECT		group_permission.*, usergroup.groupID AS id, 'group' AS type, usergroup.groupName AS name
						FROM		wcf".WCF_N."_linkList_category_to_group group_permission
						LEFT JOIN	wcf".WCF_N."_group usergroup
						ON		(usergroup.groupID = group_permission.groupID)
						WHERE		categoryID = ".$this->categoryID.")
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['id'])) continue;
				$permission = array('name' => $row['name'], 'type' => $row['type'], 'id' => $row['id']);
				unset($row['name'], $row['userID'], $row['groupID'], $row['categoryID'], $row['id'], $row['type']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->permissionSettings)) unset($row[$key]);
				}
				$permission['settings'] = $row;
				$this->permissions[] = $permission;
			}
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// check permission
		WCF::getUser()->checkPermission('admin.linkList.canEditCategory');
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'action' => 'edit',
			'categoryQuickJumpOptions' => LinkListCategory::getCategorySelect(array()),
			'categoryOptions' => LinkListCategory::getCategorySelect(array(), array($this->categoryID))
		));
	}
}
?>