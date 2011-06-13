<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');

/**
 * Provides functions to manage the links in the linklist.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkEditor extends LinkListLink {
	/**
	 * Creates a new linklist link.
	 * 
	 * @param	integer				$categoryID
	 * @param	string				$subject
	 * @param	string				$shortDescription
	 * @param	string				$message
	 * @param	string				$username
	 * @param	string				$url
	 * @param	integer				$isSticky
	 * @param	integer				$isDisabled
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @return	LinkListLinkEditor
	 */
	public static function create($categoryID, $subject, $shortDescription, $message, $username, $url, $isSticky, $isDisabled = 0, $options = array(), $attachmentList = null, $additionalFields = array()) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments()) : 0);
		
		// save category data
		$categoryID = self::insert($subject, array_merge($additionalFields, array(
			'categoryID' => $categoryID,
			'shortDescription' => $shortDescription,
			'message' => $message,
			'url' => $url,
			'isSticky' => $isSticky,
			'userID' => WCF::getUser()->userID,
			'username' => $username,
			'isDisabled' => $isDisabled,
			'everEnabled' => ($isDisabled ? 0 : 1),
			'time' => TIME_NOW,
			'lastChangeTime' => TIME_NOW,
			'attachments' => $attachmentsAmount,
			'enableSmilies' => (isset($options['enableSmilies']) ? $options['enableSmilies'] : 1),
			'enableHtml' => (isset($options['enableHtml']) ? $options['enableHtml'] : 1),
			'enableBBCodes' => (isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1),
			'ipAddress' => WCF::getSession()->ipAddress
		)));
		
		// get link id
		$linkID = WCF::getDB()->getInsertID("wcf".WCF_N."_linkList_link", 'linkID');
		
		// get link
		$link = new LinkListLinkEditor($linkID, null);
		
		// update attachments
		if ($attachmentList !== null) {
			$attachmentList->updateContainerID($linkID);
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// return link
		return $link;
	}
	
	/**
	 * Creates a preview of a link.
	 *
	 * @param 	string		$subject
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($subject, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'linkID' => 0,
			'subject' => $subject,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');
		$link = new ViewableLinkListLink(null, $row);
		return $link->getFormattedMessage();
	}
	
	/**
	 * Marks this link.
	 */
	public function mark() {
		$markedLinkListLinks = self::getMarkedLinks();
		if ($markedLinkListLinks == null || !is_array($markedLinkListLinks)) { 
			$markedLinkListLinks = array($this->linkID);
			WCF::getSession()->register('markedLinkListLinks', $markedLinkListLinks);
		}
		else {
			if (!in_array($this->linkID, $markedLinkListLinks)) {
				array_push($markedLinkListLinks, $this->linkID);
				WCF::getSession()->register('markedLinkListLinks', $markedLinkListLinks);
			}
		}
	}
	
	/**
	 * Unmarks this link.
	 */
	public function unmark() {
		$markedLinkListLinks = self::getMarkedLinks();
		if (is_array($markedLinkListLinks) && in_array($this->linkID, $markedLinkListLinks)) {
			$key = array_search($this->linkID, $markedLinkListLinks);
			
			unset($markedLinkListLinks[$key]);
			if (count($markedLinkListLinks) == 0) {
				self::unmarkAll();
			} 
			else {
				WCF::getSession()->register('markedLinkListLinks', $markedLinkListLinks);
			}
		}
	}
	
	/**
	 * Unmarks all marked links.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedLinkListLinks');
	}
	
	/**
	 * Closes the links with given ids.
	 * 
	 * @param	string		$linkIDs
	 */
	public static function closeAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isClosed = 1
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Opens the links with given ids.
	 * 
	 * @param	string		$linkIDs
	 */
	public static function openAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isClosed = 0
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Disables the links with the given link ids.
	 */
	public static function disableAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// disable the links
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isDeleted = 0,
				isDisabled = 1
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
		
	}
	
	/**
	 * Enables the links with the given link ids.
	 */
	public static function enableAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// enable the links
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isDisabled = 0,
				everEnabled = 1
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
		
	}
	
	/**
	 * Sticks the links with the given link ids.
	 */
	public static function stickAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// stick the links
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isSticky = 1
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Unsticks the links with the given link ids.
	 */
	public static function unstickAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// unstick the links
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isSticky = 0
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Restores the links with the given link ids.
	 */
	public static function restoreAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// restore link
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isDeleted = 0,
				deletedBy = '',
				deleteTime = 0,
				deletedByID = 0
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns the categories of the linkss with the given link ids
	 * 
	 * @param	string		$linkIDs
	 * @return	array
	 */
	public static function getCategories($linkIDs) {
		if (empty($linkIDs)) return array(array(), '', 'categories' => array(), 'categoryIDs' => '');
		
		$categories = array();
		$categoryIDs = '';
		$sql = "SELECT 	DISTINCT categoryID
			FROM 	wcf".WCF_N."_linkList_link
			WHERE 	linkID IN (".$linkIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
			$categories[$row['categoryID']] = new LinkListCategoryEditor($row['categoryID']);
		}
		
		return array($categories, $categoryIDs, 'categories' => $categories, 'categoryIDs' => $categoryIDs);
	}
	
	/**
	 * Moves all links with the given ids into the category with the given category id.
	 *
	 * @param	string		$linkIDs
	 * @param integer		$newCategoryID
	 */
	public static function moveAll($linkIDs, $newCategoryID) {
		if (empty($linkIDs)) return;
		
		// move links
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	categoryID = ".$newCategoryID."
			WHERE 	linkID IN (".$linkIDs.")
				AND categoryID <> ".$newCategoryID;
		WCF::getDB()->sendQuery($sql);
		
		// edit category id of link-comments
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link_comment
			SET	categoryID = ".$newCategoryID."
			WHERE 	linkID IN (".$linkIDs.")
				AND categoryID <> ".$newCategoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes the links with the given link ids.
	 *
	 * @param	string		$linkIDs
	 */
	public static function deleteAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		$trashIDs = '';
		$deleteIDs = '';
		if (LINKLIST_LINK_ENABLE_RECYCLE_BIN) {
			// recylce bin enabled
			// first of all we check which links are already in recylce bin
			$sql = "SELECT 	linkID, isDeleted
				FROM 	wcf".WCF_N."_linkList_link
				WHERE 	linkID IN (".$linkIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted']) {
					// link in recylce bin
					// delete completely
					if (!empty($deleteIDs)) $deleteIDs .= ',';
					$deleteIDs .= $row['linkID'];
				}
				else {
					// move link to recylce bin
					if (!empty($trashIDs)) $trashIDs .= ',';
					$trashIDs .= $row['linkID'];
				}
			}
		}
		else {
			// no recylce bin
			// delete all links completely
			$deleteIDs = $linkIDs;
		}
		
		self::trashAll($trashIDs);
		self::deleteAllCompletely($deleteIDs);
	}
	
	/**
	 * Deletes the links with the given link ids completely.
	 *
	 * @param	string		$linkIDs
	 */
	public static function deleteAllCompletely($linkIDs) {
		if (empty($linkIDs)) return;
		
		// delete links
		self::deleteData($linkIDs);
	}
	
	/**
	 * Deletes the sql data of the links with the given link ids.
	 *
	 * @param	string		$linkIDs
	 */
	protected static function deleteData($linkIDs) {
		// delete link
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link
			WHERE 		linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete tags
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link');
			
			$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE 		taggableID = ".$taggable->getTaggableID()."
						AND objectID IN (".$linkIDs.")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
		
		// delete comments
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link_comment
			WHERE 		linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
			
		// delete attachments
		require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
		$attachment = new MessageAttachmentListEditor(explode(',', $linkIDs));
		$attachment->deleteAll();
	}
	
	/**
	 * Moves the links with the given link ids into the recycle bin.
	 *
	 * @param	string		$linkIDs
	 */
	public static function trashAll($linkIDs) {
		if (empty($linkIDs)) return;
		
		// trash link
		$sql = "UPDATE 	wcf".WCF_N."_linkList_link
			SET	isDeleted = 1,
				deleteTime = ".TIME_NOW.",
				deletedBy = '".escapeString(WCF::getUser()->username)."',
				deletedByID = ".WCF::getUser()->userID.",
				isDisabled = 0
			WHERE 	linkID IN (".$linkIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Creates the link row in database table.
	 *
	 * @param 	array		$additionalFields
	 * @return	integer		new link id
	 */
	public static function insert($subject, $additionalFields = array()) { 
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}
		
		$sql = "INSERT INTO	wcf".WCF_N."_linkList_link
					(subject
						 ".$keys.")
			VALUES	('".escapeString($subject)."'
						".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
	
	/**
	 * Updates a linklist link.
	 * 
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$shortDescription
	 * @param	string				$url
	 * @param	integer				$isSticky
	 * @param	integer				$isDisabled
	 * @param	integer				$enableSmilies
	 * @param	integer				$enableHtml
	 * @param	integer				$enableBBCodes
	 * @param	array				$attachments
	 */
	public function update($subject, $message, $shortDescription, $url, $isSticky, $isDisabled, $enableSmilies, $enableHtml, $enableBBCodes, $attachments) {
		$this->updateData(array(
			'subject' => $subject,
			'message' => $message,
			'shortDescription' => $shortDescription,
			'url' => $url,
			'isSticky' => $isSticky,
			'isDisabled' => $isDisabled,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'lastChangeTime' => TIME_NOW,
			'attachments' => $attachments
		));
	}
	
	/**
	 * Updates the data of a link.
	 *
	 * @param 	array		$fields
	 */
	public function updateData($fields = array()) { 
		$updates = '';
		foreach ($fields as $key => $value) {
			if (!empty($updates)) $updates .= ',';
			$updates .= $key.'=';
			if (is_int($value)) $updates .= $value;
			else $updates .= "'".escapeString($value)."'";
		}
		
		if (!empty($updates)) {
			$sql = "UPDATE	wcf".WCF_N."_linkList_link
				SET	".$updates."
				WHERE	linkID = ".$this->linkID;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Updates the tags of this link.
	 * 
	 * @param	array<string>		$tagArray
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WCF_DIR.'lib/data/linkList/link/TaggedLinkListLink.class.php');
		
		// save tags
		$tagged = new TaggedLinkListLink(null, array(
			'linkID' => $this->linkID,
			'taggable' => TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link')
		));

		$languageID = 0;
		if (count(Language::getAvailableContentLanguages()) > 0) {
			$languageID = WCF::getLanguage()->getLanguageID();
		}
		
		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($languageID));
		
		// save new tags
		if (count($tagArray) > 0) {
			TagEngine::getInstance()->addTags($tagArray, $tagged, $languageID);
		}
	}
	
	/**
	 * Returns the marked links.
	 */
	public static function getMarkedLinks() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedLinkListLinks'])) {
			return $sessionVars['markedLinkListLinks'];
		}
		return null;
	}
}
?>