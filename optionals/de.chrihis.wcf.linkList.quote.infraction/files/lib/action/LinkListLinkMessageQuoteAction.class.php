<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractMessageQuoteAction.class.php');
require_once(WCF_DIR.'lib/data/message/multiQuote/MultiQuoteManager.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * Saves quotes of a linklist link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.quote.infraction
 * @subpackage action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkMessageQuoteAction extends AbstractMessageQuoteAction {
	/**
	 * linklist link object
	 *
	 * @var	LinkListLink
	 */
	public $link = null;
	
	/**
	 * linklist category object
	 *
	 * @var	LinkListCategory
	 */
	public $category = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// create a new LinkListLink instance
		$this->link = new LinkListLink($this->objectID);
		if (!$this->link->linkID) {
			throw new IllegalLinkException();
		}
		// create a new LinkListCategory instance
		$this->category = new LinkListCategory($this->link->categoryID);
		$this->category->enter();
		
		// enter link
		$this->link->enter($this->category);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		if ((!is_array($this->text) && $this->text == '') || (is_array($this->text) && !count($this->text))) {
			$this->text = $this->link->message;
		}
		if (!is_array($this->text)) {
			$this->text = array($this->text);
		}
		
		// store quotes
		foreach ($this->text as $key => $string) {
			MultiQuoteManager::storeQuote($this->objectID, 'linkListLink', $string, $this->link->username, 'index.php?page=LinkListLink&linkID='.$this->objectID, $this->category->categoryID, ((strlen($key) == 40 && preg_match('/^[a-f0-9]+$/', $key)) ? $key : ''));
		}
		MultiQuoteManager::saveStorage();
		
		// call event
		$this->executed();
	}
}
?>