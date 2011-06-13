<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

// get cache
WCF::getCache()->addResource('linkListStatistics', WCF_DIR.'cache/cache.linkListStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListStatistics.class.php');

/**
 * Shows the linklist category list page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListPage extends AbstractPage {
	// system
	public $templateName = 'linkList';
	
	/**
	 * list of categories
	 * 
	 * @var	array
	 */
	public $categories = null;

	/**
	 * category structure
	 * 
	 * @var	array
	 */
	public $categoryStructure = null;

	/**
	 * structured list of categories
	 * 
	 * @var	array
	 */
	public $categoryList = array();
	
	/**
	 * list of sub categories
	 * 
	 * @var	array
	 */
	public $subCategories = array();
	
	/**
	 * statistics dara
	 * 
	 * @var	array
	 */
	public $statistics = array();
	
	/**
	 * count of disabled links
	 * 
	 * @var	integer
	 */
	public $disabledLinks = 0;
	
	/**
	 * count of marked links
	 * 
	 * @var	integer
	 */
	public $markedLinks = 0;
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// register cache
		WCF::getCache()->addResource('linkListCategory', WCF_DIR.'cache/cache.linkListCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategory.class.php');
		
		// get category structure from cache		
		$this->categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');
		// get categories from cache
		$this->categories = WCF::getCache()->get('linkListCategory', 'categories');
		// make category list
		$this->makeCategoryList();
		
		// get statistics variable
		$this->statistics = WCF::getCache()->get('linkListStatistics');

		// count disabled links
		if (WCF::getUser()->getPermission('mod.linkList.canEnableLink')) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_linkList_link
				WHERE	isDisabled = 1";
			$row = WCF::getDB()->getFirstRow($sql);
			$this->disabledLinks = $row['count'];
		}
		
		// count marked links
		if (WCF::getUser()->getPermission('mod.linkList.canEditLink')) {
			$this->markedLinks = (($markedLinks = WCF::getSession()->getVar('markedLinkListLinks')) ? count($markedLinks) : 0);
		}
	}
	
	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'subCategories' => $this->subCategories,
			'statistics' => $this->statistics,
			'allowSpidersToIndexThisPage' => true,
			'disabledLinks' => $this->disabledLinks,
			'markedLinks' => $this->markedLinks
		));
	}
	
	/**
	 * Renders one level of the linklist category structure.
	 */
	protected function makeCategoryList($parentID = 0, $subCategoriesFrom = 0, $depth = 1, $openParents = 0, $parentClosed = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;
		
		$i = 0;
		$count = count($this->categoryStructure[$parentID]);
		foreach ($this->categoryStructure[$parentID] as $categoryID) {
			$category = $this->categories[$categoryID];
			
			if ($category->getPermission('canViewCategory')) {
				// category list depth on index
				$updateNewPosts = 0;
				$childrenOpenParents = $openParents + 1;
				$newSubCategoriesFrom = $subCategoriesFrom;
				if ($parentClosed == 0 && ($depth <= 2) && $subCategoriesFrom == $parentID) {
					$open = ($depth + 1 <= 2);
					$hasChildren = isset($this->categoryStructure[$categoryID]) && $open;
					$last = ($i == ($count - 1));
					if ($hasChildren && !$last) $childrenOpenParents = 1;
					$this->categoryList[] = array('open' => $open, 'depth' => $depth, 'hasChildren' => $hasChildren, 'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)), 'category' => $category);
					$newSubCategoriesFrom = $categoryID;
				}
			
				// make next level of the category list
				$this->makeCategoryList($categoryID, $newSubCategoriesFrom, $depth + 1, $childrenOpenParents, $parentClosed);
			
				$i++;
			}
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check permission
		WCF::getUser()->checkPermission('user.linkList.canViewLinkList');
		
		// check module options
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>