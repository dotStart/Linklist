<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the category permissions for a combination of user groups.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	system.cache
 * @category 	WoltLab Community Framework (WCF)
 */
class CacheBuilderLinkListCategoryPermissions implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $groupIDs) = explode('-', $cacheResource['cache']);
		$data = array();
		
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_linkList_category_to_group
			WHERE		groupID IN (".$groupIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['groupID']);
			
			foreach ($row as $permission => $value) {
				if ($value == -1) continue;
				
				if (!isset($data[$categoryID][$permission])) $data[$categoryID][$permission] = $value;
				else $data[$categoryID][$permission] = $value || $data[$categoryID][$permission];
			}
		}
		
		if (count($data)) {
			// inherits this permissions
			require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
			LinkListCategory::inheritPermissions(0, $data);
		}
		
		$data['groupIDs'] = $groupIDs;
		return $data;
	}
}
?>