<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObject.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/ViewableLinkListLinkComment.class.php');

/**
 * An implementation of WarningObject to support the usage of a linklist link comment as a warning object.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage data.linkList.link.comment
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentWarningObject extends ViewableLinkListLinkComment implements WarningObject {
	/**
	 * @see WarningObject::getTitle()
	 */
	public function getTitle() {
		return $this->getExcerpt();
	}
	
	/**
	 * @see WarningObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLinkCommentList&linkID='.$this->linkID.'&commentID='.$this->commentID.'#comment'.$this->commentID;
	}
}
?>