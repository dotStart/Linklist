<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');

/**
 * Sorts the structure of all categories.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategorySortAction extends AbstractAction {
	/**
	 * new positions
	 *
	 * @var array
	 */
	public $positions = array();
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_POST['categoryListPositions']) && is_array($_POST['categoryListPositions'])) $this->positions = ArrayUtil::toIntegerArray($_POST['categoryListPositions']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.linkList.canAddCategory');

		// delete old positions
		$sql = "TRUNCATE wcf".WCF_N."_linkList_category_structure";
		WCF::getDB()->sendQuery($sql);
		
		// update postions
		foreach ($this->positions as $categoryID => $data) {
			foreach ($data as $parentID => $position) {
				LinkListCategoryEditor::updatePosition(intval($categoryID), intval($parentID), $position);
			}
		}
		
		// insert default values
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_linkList_category_structure
						(parentID, categoryID)
			SELECT			parentID, categoryID
			FROM			wcf".WCF_N."_linkList_category";
		WCF::getDB()->sendQuery($sql);
		
		// reset cache
		WCF::getCache()->clearResource('linkListCategory');
		
		// call event
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=LinkListCategoryList&successfulSorting=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>