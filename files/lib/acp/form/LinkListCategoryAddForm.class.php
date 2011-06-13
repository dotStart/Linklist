<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');

/**
 * Shows the category add form.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryAddForm extends ACPForm {
	// system
	public $templateName = 'linkListCategoryAdd';
	public $activeMenuItem = 'wcf.acp.menu.link.content.linkList.category.add';
	public $neededPermissions = 'admin.linkList.canAddCategory';
	public $activeTabMenuItem = 'data';
	public $categoryID = 0;
	
	/**
	 * linklist category editor object
	 * 
	 * @var	LinkListCategoryEditor
	 */
	public $category;
	
	/**
	 * list of available permisions
	 * 
	 * @var	array
	 */
	public $permissionSettings = array();
	
	/**
	 * list of available parent categories
	 * 
	 * @var	array
	 */
	public $categoryOptions = array();
	
	/**
	 * list of additional fields
	 * 
	 * @var	array
	 */
	public $additionalFields = array();
	
	// parameters
	public $categoryType = 0;
	public $parentID = 0;
	public $position = '';
	public $title = '';
	public $description = '';
	public $allowDescriptionHtml = 0;
	public $image = '';
	public $permissions = array();
	public $allowComments = -1;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get parent id
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		
		// permission settings
		$this->getPermissionSettings();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// parameters
		if (isset($_POST['categoryType'])) $this->categoryType = intval($_POST['categoryType']);
		if (!empty($_POST['position'])) $this->position = intval($_POST['position']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['allowDescriptionHtml'])) $this->allowDescriptionHtml = intval($_POST['allowDescriptionHtml']);
		if (isset($_POST['image'])) $this->image = StringUtil::trim($_POST['image']);
		if (isset($_POST['activeTabMenuItem'])) $this->activeTabMenuItem = $_POST['activeTabMenuItem'];
		if (isset($_POST['allowComments'])) $this->allowComments = intval($_POST['allowComments']);

		// permissions
		if (isset($_POST['permission']) && is_array($_POST['permission'])) $this->permissions = $_POST['permission'];
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {		
		parent::validate();
		
		// category type
		if ($this->categoryType < 0 || $this->categoryType > 1) {
			throw new UserInputException('categoryType', 'invalid');
		}
		
		// validate permissions
		$this->validatePermissions($this->permissions, array_flip($this->permissionSettings));
		
		// parent id
		$this->validateParentID();
		
		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		
	}
	
	/**
	 * Validates the given permissions.
	 */
	public function validatePermissions($permissions, $validSettings) {
		foreach ($permissions as $permission) {
			// type
			if (!isset($permission['type']) || $permission['type'] != 'group') {
				throw new UserInputException();
			}
			
			// id
			if (!isset($permission['id'])) {
				throw new UserInputException();
			}
			
			$group = new Group(intval($permission['id']));
			if (!$group->groupID) throw new UserInputException();
			
			// settings
			if (!isset($permission['settings']) || !is_array($permission['settings'])) {
				throw new UserInputException();
			}
			// find invalid settings
			foreach ($permission['settings'] as $key => $value) {
				if (!isset($validSettings[$key]) || ($value != -1 && $value != 0 && $value =! 1)) {
					throw new UserInputException();
				}
			}
			// find missing settings
			foreach ($validSettings as $key => $value) {
				if (!isset($permission['settings'][$key])) {
					throw new UserInputException();
				}
			}
		}
	}
	
	/**
	 * Validates the given parent id.
	 */
	protected function validateParentID() {
		if ($this->parentID) {
			try {
				LinkListCategory::getCategory($this->parentID);
			}
			catch (IllegalLinkException $e) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save category
		$this->category = LinkListCategoryEditor::create($this->parentID, ($this->position ? $this->position : null), $this->title, $this->description, $this->categoryType, $this->image, $this->allowDescriptionHtml, $this->allowComments, $this->additionalFields);
		
		// save permissions
		$this->category->savePermissions($this->permissions, $this->permissionSettings);
		
		// reset cache
		$this->category->resetCache();
		
		// call event
		$this->saved();
		
		// reset values
		$this->categoryType = $this->parentID = $this->allowDescriptionHtml = 0;
		$this->position = $this->title = $this->description = $this->image = '';
		$this->permissions = array();
		$this->allowComments = -1;
		
		// show success message
		WCF::getTPL()->assign(array(
			'category' => $this->category,
			'success' => true
		));
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'categoryType' => $this->categoryType,
			'parentID' => $this->parentID,
			'position' => $this->position,
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'image' => $this->image,
			'categoryOptions' => LinkListCategory::getCategorySelect(array()),
			'permissions' => $this->permissions,
			'permissionSettings' => $this->permissionSettings,
			'action' => 'add',
			'activeTabMenuItem' => $this->activeTabMenuItem,
			'allowComments' => $this->allowComments
		));
	}
	
	/**
	 * Gets available permission settings.
	 */
	protected function getPermissionSettings() {
		$sql = "SHOW COLUMNS FROM wcf".WCF_N."_linkList_category_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'categoryID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canUploadAttachment':
					case 'canDownloadAttachment':
					case 'canViewAttachmentPreview': 
						if (!MODULE_ATTACHMENT) continue 2;
						break;
				}
				
				$this->permissionSettings[] = $row['Field'];
			}
		}
	}
}
?>