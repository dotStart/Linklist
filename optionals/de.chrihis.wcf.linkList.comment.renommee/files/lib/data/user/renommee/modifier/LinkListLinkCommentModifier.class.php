<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/modifier/AbstractModifier.class.php');

/**
 * Provides a modifier bonus for the linklist link comments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.comment.renommee
 * @subpackage data.user.renommee.modifier
 * @category 	Renommee System
 */
class LinkListLinkCommentModifier extends AbstractModifier {
	/**
	 * @see Modifier::getPoints()
	 */
	public function getPoints(RenommeeUser $user) {
		if (!isset($this->points[$user->userID])) {
			$sql = "SELECT  IFNULL(COUNT(*), 0) AS comments
				FROM    wcf".WCF_N."_linkList_link_comment
				WHERE   userID = ".$user->userID;
			$row = WCF::getDB()->getFirstRow($sql);

			$this->points[$user->userID] = $row['comments'];
		}

		return $this->points[$user->userID];
	}

	/**
	 * @see Modifier::getIcon()
	 */
	public function getIcon() {
		return 'linkListLinkComment';
	}
}
?>