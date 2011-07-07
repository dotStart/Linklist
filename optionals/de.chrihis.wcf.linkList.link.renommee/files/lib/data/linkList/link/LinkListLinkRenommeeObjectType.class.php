<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/object/RenommeeObjectType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkRenommeeObject.class.php');

/**
 * General class for linklist link renommee object types.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.renommee
 * @subpackage data.linkList.link
 * @category 	Renommee System
 */
class LinkListLinkRenommeeObjectType implements RenommeeObjectType {
	/**
	 * @see RenommeeObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$links = array();
			$sql = "SELECT	linkList_link.*
				FROM 	wcf".WCF_N."_linkList_link linkList_link
				WHERE 	linkList_link.linkID IN(".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);			
			while ($row = WCF::getDB()->fetchArray($result)) {
				$links[$row['linkID']] = new LinkListLinkRenommeeObject(null, $row);
			}
			
			return (count($links) > 0 ? $links : null); 
		}
		else {
			// get object
			$link = new LinkListLinkRenommeeObject($objectID);
			
			if (!$link->linkID) return null;
			
			// check permissions			
			$link->access();
			
			// return object
			return $link;
		}
	}

	/**	 
	 * @see RenommeeObjectType::getObjectRenommeeByUser()
	 */
	public function getObjectRenommeeByUser(User $user) {
		if (!$this->isActive()) return 0;
		
		$sql = "SELECT	SUM(points) AS renommee
			FROM	wcf".WCF_N."_user_renommee
			WHERE	userID = ".$user->userID."
			AND	objectType = 'linkListLink'";
		$row = WCF::getDB()->getFirstRow($sql);
		
		return ($row['renommee'] ? $row['renommee'] : 0);
	}
	
	/**
	 * @see RenommeeObjectType::isActive()	 
	 */
	public function isActive() {
		return (USER_RENOMMEE_OBJECT_LINKLIST_LINK_ACTIVE && MODULE_LINKLIST) ? true : false;
	}
	
	/**
	 * @see RenommeeObjectType::getPackageID()
	 */
	public function getPackageID() {
		return WCF::getPackageID('de.chrihis.wcf.linkList');
	}
}
?>