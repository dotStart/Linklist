<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLinkList.class.php');
require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');

/**
 * Represents a list of tagged linklist links.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class TaggedLinkListLinkList extends ViewableLinkListLinkList {
	/**
	 * tag id
	 * 
	 * @var	integer
	 */
	public $tagID = 0;
	
	/**
	 * taggable object
	 * 
	 * @var	Taggable
	 */
	public $taggable = null;

	/**
	 * Creates a new TaggedLinkListLinkList object.
	 */
	public function __construct($tagID) {
		$this->tagID = $tagID;
		$this->taggable = TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link');
	}
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		if (!empty($this->sqlConditions)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object tag_to_object,
					wcf".WCF_N."_linkList_link linkList_link
				WHERE	tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND linkList_link.linkID = tag_to_object.objectID
					AND ".$this->sqlConditions;
		}
		else {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object
				WHERE	tagID = ".$this->tagID."
					AND taggableID = ".$this->taggable->getTaggableID();
		}
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		linkList_link.linkID, linkList_link.attachments
			FROM		wcf".WCF_N."_tag_to_object tag_to_object,
					wcf".WCF_N."_linkList_link linkList_link
			WHERE		tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND linkList_link.linkID = tag_to_object.objectID
					".(!empty($this->sqlConditions) ? "AND ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['linkID'];
			if ($row['attachments']) $this->attachmentLinkIDArray[] = $row['linkID'];
		}
	}
}
?>