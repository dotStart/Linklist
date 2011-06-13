<?php
// wcf imports
require_once(WCF_DIR.'lib/system/counterUpdate/type/AbstractCounterUpdateType.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');

/**
 * Updates the linklist category counters.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.counterUpdate.type
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryCounterUpdateType extends AbstractCounterUpdateType {
	/**
	 * @see CounterUpdateType::getDefaultLimit()
	 */
	public function getDefaultLimit() {
		return 50;
	}
	
	/**
	 * @see CounterUpdateType::countItems()
	 */
	public function countItems() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_linkList_category";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see CounterUpdateType::update()
	 */
	public function update($offset, $limit) {		
		// get category ids
		$categoryIDs = '';
		$sql = "SELECT		categoryID
			FROM		wcf".WCF_N."_linkList_category
			ORDER BY	categoryID";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
			
			// update last entry
			$category = new LinkListCategoryEditor($row['categoryID']);
			// refresh categories
			LinkListCategoryEditor::refreshAll($categoryIDs);
			LinkListCategory::resetCache();
			
			// call event
			$this->updated();
		}
		if (empty($categoryIDs)) {
			$this->finished = true;
			return;
		}
	}
}
?>