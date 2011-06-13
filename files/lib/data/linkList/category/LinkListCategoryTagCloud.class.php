<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');
 
/**
 * Gets the tags of links in a linklist category.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList.category
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryTagCloud extends TagCloud {
	/**
	 * Contructs a new LinkListCategoryTagCloud.
	 *
	 * @param	integer		$categoryID
	 * @param	array<integer>	$languageIDArray
	 */
	public function __construct($categoryID, $languageIDArray = array()) {
		$this->categoryID = $categoryID;
		$this->languageIDArray = $languageIDArray;
		if (!count($this->languageIDArray)) $this->languageIDArray = array(0);
		
		// init cache
		$this->cacheName = 'linkListCategoryTagCloud-'.$this->categoryID.'-'.implode(',', $this->languageIDArray);
		$this->loadCache();
	}
	
	/**
	 * Loads the tag cloud cache.
	 */
	public function loadCache() {
		if ($this->tags !== null) return;

		// get cache
		WCF::getCache()->addResource($this->cacheName, WCF_DIR.'cache/cache.linkListCategoryTagCloud-'.$this->categoryID.'-'.StringUtil::getHash(implode(',', $this->languageIDArray)).'.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategoryTagCloud.class.php', 0, 86400);
		$this->tags = WCF::getCache()->get($this->cacheName);
	}
}
?>