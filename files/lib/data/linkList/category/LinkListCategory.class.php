<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

// register cache
// linklist category cache
WCF::getCache()->addResource('linkListCategory', WCF_DIR.'cache/cache.linkListCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategory.class.php');
// linklist statistics cache
WCF::getCache()->addResource('linkListStatistics', WCF_DIR.'cache/cache.linkListStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListStatistics.class.php');

/**
 * Represents a category in the linklist.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategory extends DatabaseObject {
	protected static $categories = null;
	protected static $categoryStructure = null;
	protected static $categorySelect;
	
	public static $categoryPermissions = null;

	/**
	 * Defines a normal category.
	 */
	const TYPE_CATEGORY = 0;
	
	/**
	 * Defines that a main category
	 */
	const TYPE_MAIN_CATEGORY = 1;
	
	/**
	 * Creates a new LinkListCategory object.
	 * 
	 * @param 	integer			$categoryID	id of a category
	 * @param 	array			$row			resultset with category data form database
	 * @param 	LinkListCategory	$cacheObject	object with linklist category data form database
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null) {
		if ($categoryID !== null) $cacheObject = self::getCategory($categoryID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}
	
	/**
	 * Returns true if this is a normal category
	 *
	 * @return	boolean
	 */
	public function isCategory() {
		return $this->categoryType == self::TYPE_CATEGORY;
	}
	
	/**
	 * Returns true if this is a main category.
	 *
	 * @return	boolean
	 */
	public function isMainCategory() {
		return $this->categoryType == self::TYPE_MAIN_CATEGORY;
	}
	
	/**
	 * Returns a list of the parent categories of this category.
	 * 
	 * @return	array
	 */
	public function getParentCategories() {
		if ($this->parentCategories === null) {
			$this->parentCategories = array();
			$categories = WCF::getCache()->get('linkListCategory', 'categories');
			
			$parentCategory = $this;
			while ($parentCategory->parentID != 0) {
				$parentCategory = $categories[$parentCategory->parentID];
				array_unshift($this->parentCategories, $parentCategory);
			}
		}
		
		return $this->parentCategories;
	}
	
	/**
	 * Enters the active user to this category.
	 */
	public function enter() {
		// check permissions
		$this->checkPermission(array('canViewCategory', 'canEnterCategory'));
		
	}
	
	/**
	 * Returns the name of the category icon.
	 *
	 * @return	string		filename of the category icon
	 */
	public function getIconName() {
		$icon = 'linkListCategory';
		
		// todo: eigene kategorie icons bereits hier hinzufügen.
		
		return $icon;
	}
	
	/**
	 * Checks the given permissions for this user in this category.
	 * 
	 * @see			LinkListCategory::getPermission()
	 * @param	mixed	$permissions
	 */
	public function checkPermission($permissions = 'canViewCategory') {
		if (!is_array($permissions)) $permissions = array($permissions);
		
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Checks whether the active user has the permission with the given name on this category.
	 * 
	 * @param		string		$permission	name of the permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canViewCategory') {
		return (boolean) $this->getCategoryPermission($permission, $this->categoryID);
	}
	
	/**
	 * Checks whether this user has the permission with the given name on the category with the given category id.
	 * 
	 * @param		string		$permission	name of the requested permission
	 * @param		integer		$categoryID
	 * @return	mixed					value of the permission
	 */
	public static function getCategoryPermission($permission, $categoryID) {
		if (self::$categoryPermissions === null) self::getGroupData();
		if (isset(self::$categoryPermissions[$categoryID][$permission])) {
			return self::$categoryPermissions[$categoryID][$permission];
		}
		return WCF::getUser()->getPermission('user.linkList.'.$permission);
	}
	
	/**
	 * @see LinkListCategory::getGroupData()
	 */
	public static function getGroupData() {		
		// get group permissions from cache
		$groups = implode(",", WCF::getUser()->getGroupIDs());
		$groupsFileName = StringUtil::getHash(implode("-", WCF::getUser()->getGroupIDs()));
		
		// register cache resource
		WCF::getCache()->addResource('linkListCategoryPermissions-'.$groups, WCF_DIR.'cache/cache.linkListCategoryPermissions-'.$groupsFileName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategoryPermissions.class.php');
		
		// get group data from cache
		self::$categoryPermissions = WCF::getCache()->get('linkListCategoryPermissions-'.$groups);
		if (isset(self::$categoryPermissions['groupIDs']) && self::$categoryPermissions['groupIDs'] != $groups) {
			self::$categoryPermissions = array();
		}
	}
	
	/**
	 * Gets the category with the given category id from cache.
	 * 
	 * @param 	integer			$categoryID
	 * @return	LinkListCategory
	 */
	public static function getCategory($categoryID) {
		if (self::$categories === null) {
			self::$categories = WCF::getCache()->get('linkListCategory', 'categories');
		}
		
		// check if category exists
		if (!isset(self::$categories[$categoryID])) {
			throw new IllegalLinkException();
		}
		
		return self::$categories[$categoryID];
	}
	
	/**
	 * Creates the category select list.
	 * 
	 * @param	array		$permissions		filters categories by given permissions
	 * @param	array		$ignored			list of category ids to ignore in result
	 * @return array
	 */
	public static function getCategorySelect($permissions = array('canViewCategory'), $ignored = array()) {
		self::$categorySelect = array();
		
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');
		if (self::$categories === null) self::$categories = WCF::getCache()->get('linkListCategory', 'categories');
		
		self::makeCategorySelect(0, 0, $permissions, $ignored);
		
		return self::$categorySelect;
	}
	
	/**
	 * Generates the category select list.
	 * 
	 * @param	integer		$parentID			id of the parent category
	 * @param	integer		$depth 			current list depth
	 * @param	array		$permissions		filters categories by given permissions
	 * @param	array		$ignore			list of category ids to ignore in result
	 */
	protected static function makeCategorySelect($parentID = 0, $depth = 0, $permissions = array('canViewCategory'), $ignore = array()) {
		if (!isset(self::$categoryStructure[$parentID])) return;
		
		foreach (self::$categoryStructure[$parentID] as $categoryID) {
			if (!empty($ignore) && in_array($categoryID, $ignore)) continue;
			
			$category = self::$categories[$categoryID];
			
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			if (!$result) continue;
			
			// we must encode html here because the htmloptions plugin doesn't do it
			$title = WCF::getLanguage()->get(StringUtil::encodeHTML($category->title));
			if ($depth > 0) $title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth). ' ' . $title;
			
			self::$categorySelect[$categoryID] = $title;
			self::makeCategorySelect($categoryID, $depth + 1, $permissions, $ignore);
		}
	}
	
	/**
	 * Returns a list of accessible categories.
	 * 
	 * @param		array		$permissions		filters categories by given permissions
	 * @return	array<integer>					comma separated category ids
	 */
	public static function getAccessibleCategoryIDArray($permissions = array('canViewCategory', 'canEnterCategory')) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('linkListCategory', 'categories');
		
		$categoryIDArray = array();
		foreach (self::$categories as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			if ($result) {
				$categoryIDArray[] = $category->categoryID;
			}
		}
		
		return $categoryIDArray;
	}
	
	/**
	 * Returns a list of accessible categories.
	 * 
	 * @param		array		$permissions	filters categories by given permissions
	 * @return	string					comma separated category ids
	 */
	public static function getAccessibleCategories($permissions = array('canViewCategory', 'canEnterCategory')) {
		return implode(',', self::getAccessibleCategoryIDArray($permissions));
	}
	
	/** 
	 * Inherits category permissions.
	 *
	 * @param 	integer		$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');
		if (self::$categories === null) self::$categories = WCF::getCache()->get('linkListCategory', 'categories');
		
		if (isset(self::$categoryStructure[$parentID]) && is_array(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				$category = self::$categories[$categoryID];
					
				// inherit permissions from parent category
				if ($category->parentID) {
					if (isset($permissions[$category->parentID]) && !isset($permissions[$categoryID])) {
						$permissions[$categoryID] = $permissions[$category->parentID];
					}
				}
				
				self::inheritPermissions($categoryID, $permissions);
			}
		}
	}
	
	/**
	 * Resets the cache after changes.
	 */
	public static function resetCache() {
		// reset category cache
		WCF::getCache()->clearResource('linkListCategory');
		// reset statistics cache
		WCF::getCache()->clearResource('linkListStatistics');
		// reset permissions cache
		WCF::getCache()->clear(WCF_DIR . 'cache/', 'cache.linkListCategoryPermissions-*', true);
		
		self::$categories = self::$categoryStructure = self::$categorySelect = null;
	}
	
	/**
	 * Returns a list of subcategories.
	 * 
	 * @param	mixed		$categoryID
	 * @return	array<integer>
	 */
	public static function getSubCategoryIDArray($categoryID) {
		$categoryIDArray = (is_array($categoryID) ? $categoryID : array($categoryID));
		$subCategoryIDArray = array();
		
		// load cache
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');
		foreach ($categoryIDArray as $categoryID) {
			$subCategoryIDArray = array_merge($subCategoryIDArray, self::makeSubCategoryIDArray($categoryID));
		}
		
		$subCategoryIDArray = array_unique($subCategoryIDArray);
		return $subCategoryIDArray;
	}
	
	/**
	 * Make a list of subcategories.
	 * 
	 * @param	integer		$parentCategoryID
	 * @return	array<integer>
	 */
	public static function makeSubCategoryIDArray($parentCategoryID) {
		if (!isset(self::$categoryStructure[$parentCategoryID])) {
			return array();
		}
		
		$subCategoryIDArray = array();
		foreach (self::$categoryStructure[$parentCategoryID] as $categoryID) {
			$subCategoryIDArray = self::makeSubCategoryIDArray($categoryID);
			$subCategoryIDArray[] = $categoryID;
		}
		
		return $subCategoryIDArray;
	}
}
?>