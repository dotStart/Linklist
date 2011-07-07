<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObject.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');

/**
 * An implementation of WarningObject to support the usage of a linklist link as a warning object.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkWarningObject extends LinkListLink implements WarningObject {
	/**
	 * @see WarningObject::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}
	
	/**
	 * @see WarningObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLink&linkID='.$this->linkID;
	}
}
?>