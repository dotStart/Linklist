<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/object/RenommeeObject.class.php');
require_once(WCF_DIR.'lib/data/user/renommee/Renommee.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');

/**
 * A linklist link renommee object. Represents a link for the renommee system.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.renommee
 * @subpackage data.linkList.link
 * @category 	Renommee System
 */
class LinkListLinkRenommeeObject extends LinkListLink implements RenommeeObject {	
	/**
	 * Creates a new LinkListLinkRenommeeObject object.
	 *
	 * @see LinkListLink::__construct()
	 */
	public function __construct($linkID, $row = null, $databaseObject = null, $additionalData = array()) {
		if ($linkID !== null) {
			$sql = "SELECT		linkList_link.*,
						(SELECT         GROUP_CONCAT(evaluatorID SEPARATOR ',')
                                                FROM            wcf".WCF_N."_user_renommee
						WHERE           objectID = linkList_link.linkID
						AND             objectType = 'linkListLink') AS evaluatorIDs
				FROM 	wcf".WCF_N."_linkList_link linkList_link
				WHERE 	linkList_link.linkID = ".$linkID;
			$row = WCF::getDB()->getFirstRow($sql);
		}		
		if ($databaseObject !== null && $row === null) {
			// workaround for nasty PHP 5.0.x and PHP 5.1.x Bug
			// @see http://bugs.php.net/bug.php?id=37212
			if (version_compare(PHP_VERSION, '5.2.0') == -1) {
				$row = $additionalData;
				$row['linkID'] = $databaseObject->linkID;
				$row['userID'] = $databaseObject->userID;
				$row['username'] = $databaseObject->username;
				$row['subject'] = $databaseObject->subject;
				$row['renommee'] = $databaseObject->renommee;
				$row['rated'] = $databaseObject->rated;
			}
			else $row = array_merge($databaseObject->data, $additionalData);
		}		
		parent::__construct(null, $row);
	}
	
	/**
	 * @see RenommeeObject::access()
	 */
	public function access() {		
		// create a new LinkListLink instance
		$link = new LinkListLink($this->linkID, null);
		$link->enter();
	}
	
	/**	 
	 * @see RenommeeObject::isRateable
	 */
	public function isRateable(RenommeeObjectType $objectType = null) {
		if ($objectType === null) {
			$objectType = Renommee::getRenommeeObjectTypeObject('linkListLink');
		}

		// age validation
		if (USER_RENOMMEE_MAX_OBJECT_AGE != -1) {
			$age = floor((TIME_NOW - $this->time) / 86400);
			if ($age > USER_RENOMMEE_MAX_OBJECT_AGE) return false;
		}
		
		return (!$this->rated && $objectType->isActive());
	}

        /**
	 * @see RenommeeObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->linkID;
	}

        /**
	 * @see RenommeeObject::getUserID()
	 */
	public function getUserID() {
		return $this->userID;
	}

        /**
	 * @see RenommeeObject::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @see RenommeeObject::getTitle()
	 */
	public function getTitle() {		
		return $this->subject;
	}
	
	/**
	 * @see RenommeeObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLink&linkID='.$this->linkID;
	}
	
	/**
	 * @see RenommeeObject::getIcon()
	 */
	public function getIcon() {
		return 'linkListLink';
	}
}
?>