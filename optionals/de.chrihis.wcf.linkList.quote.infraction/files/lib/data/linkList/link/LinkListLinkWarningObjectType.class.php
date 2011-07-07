<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObjectType.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLinkWarningObject.class.php');
require_once(WCF_DIR.'lib/data/linkList/category/LinkListCategory.class.php');

/**
 * An implementation of WarningObjectType to support the usage of a linklist link as a warning object.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkWarningObjectType implements WarningObjectType {
	/**
	 * @see WarningObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$links = array();
			$sql = "SELECT		*
				FROM 		wcf".WCF_N."_linkList_link
				WHERE 		linkID IN (".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$links[$row['linkID']] = new LinkListLinkWarningObject(null, $row);
			}
			
			return (count($links) > 0 ? $links : null); 
		}
		else {
			// get object
			$link = new LinkListLinkWarningObject($objectID);
			if (!$link->linkID) return null;
			
			// check permissions
			$category = LinkListCategory::getCategory($link->categoryID);
			if (!$category->getPermission('canViewCategory') || !$category->getPermission('canEnterCategory')) return null;
			
			// return object
			return $link;
		}
	}
}
?>