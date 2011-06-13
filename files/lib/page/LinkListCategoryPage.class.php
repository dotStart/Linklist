<?php
// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows a list of all linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage page
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryPage extends SortablePage {
	// system
	public $templateName = 'linkListCategory';
	public $defaultSortField = LINKLIST_CATEGORY_DEFAULT_SORT_FIELD;
	public $defaultSortOrder = LINKLIST_CATEGORY_DEFAULT_SORT_ORDER;
	public $markedLinks = 0;
	public $itemsPerPage = LINKLIST_CATEGORY_LINKS_PER_PAGE;
	
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
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * show category
	 *
	 * @var Category
	 */
	public $category = null;
	
	/**
	 * list of linklist links
	 *
	 * @var LinkListLinkList
	 */
	public $linkList = null;
	
	/**
	 * tag list object
	 *
	 * @var TagList
	 */
	public $tagList = null;
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * tag id
	 *
	 * @var integer
	 */
	public $tagID = 0;
	
	/**
	 * tag object
	 *
	 * @var Tag
	 */
	public $tag = null;
	
	/**
	 * taggable object
	 *
	 * @var Taggable
	 */
	public $taggable = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
		// create a new category instance
		$this->category = LinkListCategory::getCategory($this->categoryID);		
		// check permission
		$this->category->enter();
		
		// get tag
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
		
		// init linklist
		if (MODULE_TAGGING && $this->tagID) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
			if ($this->tag === null) {
				throw new IllegalLinkException();
			}
			require_once(WCF_DIR.'lib/data/linkList/link/TaggedLinkListLinkList.class.php');
			$this->linkList = new TaggedLinkListLinkList($this->tagID);
		}
		else {
			require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLinkList.class.php');
			$this->linkList = new ViewableLinkListLinkList();
		}
		
		// init tag list
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagList.class.php');
			$this->tagList = new TagList(array('de.chrihis.wcf.linkList.link'), WCF::getSession()->getVisibleLanguageIDArray());
		}
		
		// sql conditions
		$this->linkList->sqlConditions = "categoryID = ".$this->categoryID;
		
		if (!WCF::getUser()->getPermission('mod.linkList.canEnableLink')) {
			$disabledLinks = 1;
			$this->linkList->sqlConditions .= ' AND (isDisabled = 0';
		}
		
		if (isset($disabledLinks)) {
			$this->linkList->sqlConditions .= " OR userID = ".WCF::getUser()->userID;
		}
		
		if (!WCF::getUser()->getPermission('mod.linkList.canEnableLink')) {
			$this->linkList->sqlConditions .= ')';
		}
		
		if (!WCF::getUser()->getPermission('mod.linkList.canDeleteLink')) {
			$this->linkList->sqlConditions .= ' AND isDeleted = 0';
		}
		
		// register cache
		WCF::getCache()->addResource('linkListCategory', WCF_DIR.'cache/cache.linkListCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategory.class.php');
		
		// get category structure from cache		
		$this->categoryStructure = WCF::getCache()->get('linkListCategory', 'categoryStructure');

		// get categories from cache
		$this->categories = WCF::getCache()->get('linkListCategory', 'categories');
		
		// make category list
		$this->makeCategoryList($this->categoryID, $this->categoryID);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read links
		$this->linkList->sqlLimit = $this->itemsPerPage;
		$this->linkList->sqlOrderBy = 'linkList_link.isSticky DESC, linkList_link.'.$this->sortField.' '.$this->sortOrder;
		$this->linkList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->linkList->readObjects();

		// get tags
		if (MODULE_TAGGING) {
			$this->readTags();
		}
		
		// get marked links
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			$this->markedLinks = count($sessionVars['markedLinkListLinks']);
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->linkList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'links' => $this->linkList->getObjects(),
			'markedLinks' => $this->markedLinks,
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'tags' => $this->linkList->getTags(),
			'availableTags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag,
			'taggableID' => ($this->taggable !== null ? $this->taggable->getTaggableID() : 0),
			'allowSpidersToIndexThisPage' => true,
			'categories' => $this->categoryList,
			'subCategories' => $this->subCategories
		));
	}
	
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'subject':
			case 'lastChangeTime':
			case 'visits':
			case 'comments':
			case 'time': 
			break;
			default: $this->sortField = $this->defaultSortField;
		}
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
	 * Gets the tags of this category.
	 */
	protected function readTags() {
		// get tags
		require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryTagCloud.class.php');
		$tagCloud = new LinkListCategoryTagCloud($this->categoryID, WCF::getSession()->getVisibleLanguageIDArray());
		$this->tags = $tagCloud->getTags();
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.linkList');
		
		// check module options
		if (!MODULE_LINKLIST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>