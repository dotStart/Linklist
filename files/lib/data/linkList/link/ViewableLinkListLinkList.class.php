<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkList.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');

/**
 * Represents a viewable list of linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class ViewableLinkListLinkList extends LinkListLinkList {
	/**
	 * list of object ids
	 * 
	 * @var	array<integer>
	 */
	public $objectIDArray = array();
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		linkList_link.linkID
			FROM		wcf".WCF_N."_linkList_link linkList_link
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['linkID'];
		}
	}

	/**
	 * Gets the list of tags.
	 */
	protected function readTags() {
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link');
			$sql = "SELECT		tag_to_object.objectID AS linkID,
						tag.tagID, tag.name
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_tag tag
				ON		(tag.tagID = tag_to_object.tagID)
				WHERE		tag_to_object.taggableID = ".$taggable->getTaggableID()."
						AND tag_to_object.languageID IN (".implode(',', (count(WCF::getSession()->getVisibleLanguageIDArray()) ? WCF::getSession()->getVisibleLanguageIDArray() : array(0))).")
						AND tag_to_object.objectID IN (".implode(',', $this->objectIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($this->tags[$row['linkID']])) $this->tags[$row['linkID']] = array();
				$this->tags[$row['linkID']][] = new Tag(null, $row);
			}
		}
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// get ids
		$this->readObjectIDArray();
		
		// get links
		if (count($this->objectIDArray)) {
			$this->readTags();
			
			$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
						linkList_link.*
				FROM		wcf".WCF_N."_linkList_link linkList_link
				".$this->sqlJoins."
				WHERE 		linkList_link.linkID IN (".implode(',', $this->objectIDArray).")
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->links[] = new ViewableLinkListLink(null, $row);
			}
		}
	}
	
	/**
	 * Returns the list of tags.
	 * 
	 * @return	array
	 */
	public function getTags() {
		return $this->tags;
	}
}
?>