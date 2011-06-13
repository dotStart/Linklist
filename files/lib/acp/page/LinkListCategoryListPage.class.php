<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * Shows a list of all categories in the acp.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryListPage extends AbstractPage {
	// system
	public $templateName = 'linkListCategoryList';
	
	/**
	 * category structure
	 * 
	 * @var	array
	 */
	public $categoryStructure = null;
	
	/**
	 * list of categories
	 * 
	 * @var	array
	 */
	public $categories = null;
	
	/**
	 * structured list of categories
	 * 
	 * @var	array
	 */
	public $categoryList = array();
		
	/**
	 * sorting change successful
	 *
	 * @var boolean
	 */
	public $successfulSorting = false;
	
	/**
	 * deleting category successful
	 *
	 * @var boolean
	 */
	public $successfulDeleting = false;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
		if (isset($_REQUEST['successfulSDeleting'])) $this->successfulDeleting = true;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get category structure from cache		
		$this->categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');

		// get categories from cache
		$this->categories = WCF::getCache()->get('linkListCategory', 'categories');
		
		// make category list
		$this->makeCategoryList();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.linkList.category.list');
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'successfulSorting' => $this->successfulSorting,
			'successfulDeleting' => $this->successfulDeleting
		));
	}
	
	/**
	 * Renders one level of the category structure.
	 *
	 * @param	integer		parentID		render the subcategories of the category with the given id
	 * @param	integer		depth			the depth of the current level
	 * @param	integer		openParents		helping variable for rendering the html list in the categorylist template
	 */
	protected function makeCategoryList($parentID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;
		
		$i = 0; $children = count($this->categoryStructure[$parentID]);
		foreach ($this->categoryStructure[$parentID] as $categoryID) {
			$category = $this->categories[$categoryID];
			
			// categorylist depth on index
			$childrenOpenParents = $openParents + 1;
			$hasChildren = isset($this->categoryStructure[$categoryID]);
			$last = $i == count($this->categoryStructure[$parentID]) - 1;
			if ($hasChildren && !$last) $childrenOpenParents = 1;
			$this->categoryList[] = array('depth' => $depth, 'hasChildren' => $hasChildren, 'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)), 'category' => $category, 'parentID' => $parentID, 'position' => $i+1, 'maxPosition' => $children);
			
			// make next level of the category list
			$this->makeCategoryList($categoryID, $depth + 1, $childrenOpenParents);
			
			$i++;
		}
	}
}
?>