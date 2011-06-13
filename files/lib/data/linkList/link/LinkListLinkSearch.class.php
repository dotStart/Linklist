<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkSearchResult.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');
 
/**
 * An implementation of SearchableMessageType for searching linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkSearch extends AbstractSearchableMessageType {
	protected $messageCache = array();
	
	/**
	 * Caches the data of the messages with the given ids.
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get links
		$sql = "SELECT		linkList_link.*
			FROM		wcf".WCF_N."_linkList_link linkList_link
			WHERE		linkList_link.linkID IN (".$messageIDs.")
					AND linkList_link.isDisabled = 0
					AND linkList_link.isDeleted = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$link = new LinkListLinkSearchResult(null, $row);
			$this->messageCache[$row['linkID']] = array('type' => 'linkListLink', 'message' => $link);
		}
	}
	
	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData = null) {
		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];
		return null;
	}
	
	/**
	 * Returns the database table name for this search type.
	 */
	public function getTableName() {
		return 'wcf'.WCF_N.'_linkList_link';
	}
	
	/**
	 * Returns the condition name for this search type.
	 */
	public function getConditions($form = null) {
		return 'messageTable.isDeleted = 0 AND messageTable.isDisabled = 0';
	}
	
	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		return count(LinkListCategory::getCategorySelect(array('canEnterCategory', 'canViewLink'))) > 0;
	}
	
	/**
	 * Returns the message id field name for this search type.
	 */
	public function getIDFieldName() {
		return 'linkID';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultLinkListLink';
	}
}
?>