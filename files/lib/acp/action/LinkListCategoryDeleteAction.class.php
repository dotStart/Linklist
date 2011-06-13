<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');

/**
 * Deletes a category and the data of this.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryDeleteAction extends AbstractAction {
	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * category editor object
	 *
	 * @var LinkListCategoryEditor
	 */
	public $category = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		// new linklist category editor instance
		$this->category = new LinkListCategoryEditor($this->categoryID);
		if (!$this->category->categoryID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.linkList.canDeleteCategory');
				
		// delete category
		$this->category->delete();
		
		// reset cache
		$this->category->resetCache();
		
		// call event
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=LinkListCategoryList&successfulDeleting&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>