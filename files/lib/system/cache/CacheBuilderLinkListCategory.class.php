<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');;

/**
 * Caches all categories and the structure of the categories.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	system.cache
 * @category 	WoltLab Community Framework (WCF)
 */
class CacheBuilderLinkListCategory implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('categories' => array(), 'categoryStructure' => array());
		
		// get categories
		$sql = "SELECT	*
			FROM 	wcf".WCF_N."_linkList_category";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categories'][$row['categoryID']] = new LinkListCategory(null, $row);
		}
		
		// get category structure
		$sql = "SELECT		*
			FROM 		wcf".WCF_N."_linkList_category_structure
			ORDER BY 	parentID ASC, position ASC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categoryStructure'][$row['parentID']][] = $row['categoryID'];
		}
		
		return $data;
	}
}
?>