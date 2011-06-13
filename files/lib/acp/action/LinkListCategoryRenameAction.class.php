<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');

/**
 * Changes the title of a category.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryRenameAction extends AbstractAction {
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
	 * new title
	 *
	 * @var string
	 */
	public $title = '';
	
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
		
		// get title
		if (isset($_POST['title'])) {
			$this->title = $_POST['title'];
			if (CHARSET != 'UTF-8') $this->title = StringUtil::convertEncoding('UTF-8', CHARSET, $this->title);
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.linkList.canAddCategory');
				
		// change title
		$this->category->updateData(array(
			'title' => $this->title
		));
		
		// reset cache
		WCF::getCache()->clearResource('linkListCategory');
		
		// call event
		$this->executed();
	}
}
?>