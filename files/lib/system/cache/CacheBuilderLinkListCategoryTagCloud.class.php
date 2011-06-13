<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilderTagCloud.class.php');
 
/**
 * Caches the tag cloud of a linklist category.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	system.cache
 * @category 	WoltLab Community Framework (WCF)
 */
class CacheBuilderLinkListCategoryTagCloud extends CacheBuilderTagCloud {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $categoryID, $languageIDs) = explode('-', $cacheResource['cache']);
		$data = array();

		// get taggable
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link');
		
		// get tag ids
		$tagIDArray = array();
		$sql = "SELECT		COUNT(*) AS counter, object.tagID
			FROM 		wcf".WCF_N."_linkList_link linkList_link,
					wcf".WCF_N."_tag_to_object object
			WHERE 		linkList_link.categoryID = ".$categoryID."
					AND object.taggableID = ".$taggable->getTaggableID()."
					AND object.languageID IN (".$languageIDs.")
					AND object.objectID = linkList_link.linkID
					AND linkList_link.isDisabled = 0
					AND linkList_link.isDeleted = 0
			GROUP BY 	object.tagID
			ORDER BY 	counter DESC";
		$result = WCF::getDB()->sendQuery($sql, 500);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$tagIDArray[$row['tagID']] = $row['counter'];
		}
			
		// get tags
		if (count($tagIDArray)) {
			$sql = "SELECT		name, tagID
				FROM		wcf".WCF_N."_tag
				WHERE		tagID IN (".implode(',', array_keys($tagIDArray)).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$row['counter'] = $tagIDArray[$row['tagID']];
				$this->tags[StringUtil::toLowerCase($row['name'])] = new Tag(null, $row);
			}

			// sort by counter
			uasort($this->tags, array('self', 'compareTags'));
						
			$data = $this->tags;
		}
		
		return $data;
	}
}
?>