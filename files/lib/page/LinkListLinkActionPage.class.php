<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkEditor.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkAction.class.php');

/**
 * Chechs the actions an execute they.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkActionPage extends AbstractPage {
	public $categoryID = 0;
	public $linkID = 0;
	public $subject = '';
	public $url = '';
	public $category, $link;
	public static $validFunctions = array('stick', 'unstick', 'changeSubject', 'mark', 'unmark', 'trash', 'delete', 'recover', 'disable', 'enable', 'close', 'closeAll', 'open', 'openAll', 'unmarkAll', 'deleteAll', 'recoverAll', 'move');
	public $reason = '';
	public $action = '';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);

		// get link ids
		if (isset($_REQUEST['linkID'])) {
			$this->linkID = ArrayUtil::toIntegerArray($_REQUEST['linkID']);
		}
		else if (isset($_REQUEST['linkListLinkID'])) {
			$this->linkID = ArrayUtil::toIntegerArray($_REQUEST['linkListLinkID']);
		}
		
		// new subject
		if (isset($_REQUEST['subject'])) {
			$this->subject = StringUtil::trim($_REQUEST['subject']);
			if (CHARSET != 'UTF-8') $this->subject = StringUtil::convertEncoding('UTF-8', CHARSET, $this->subject);
		}
		
		// get url
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
		
		// get a new LinkListLinkEditor instance
		if (!is_array($this->linkID) && $this->linkID != 0) {
			$this->link = new LinkListLinkEditor($this->linkID);
			$this->categoryID = $this->link->categoryID;
			$this->link->enter();
		}
		
		// get a new LinkListCategoryEditor instance
		if ($this->categoryID != 0) {
			$this->category = new LinkListCategoryEditor($this->categoryID);
			if ($this->link == null) {
				$this->category->enter();
			}
		}
		
		if (isset($_POST['action'])) $this->action = $_POST['action'];
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();
		
		// execute action
		if (in_array($this->action, self::$validFunctions)) {
			$linkAction = new LinkListLinkAction($this->category, $this->link, $this->linkID, $this->subject, $this->url);
			$linkAction->{$this->action}();
		}
	}
}
?>