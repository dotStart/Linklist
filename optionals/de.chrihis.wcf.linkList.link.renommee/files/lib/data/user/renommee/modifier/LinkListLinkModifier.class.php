<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/renommee/modifier/AbstractModifier.class.php');

/**
 * Provides a modifier bonus for the user's linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.renommee
 * @subpackage data.user.renommee.modifier
 * @category 	Renommee System
 */
class LinkListLinkModifier extends AbstractModifier {
	/**
	 * @see Modifier::getPoints()
	 */
	public function getPoints(RenommeeUser $user) {
		if (!isset($this->points[$user->userID])) {
			$sql = "SELECT  IFNULL(COUNT(*), 0) AS links
				FROM    wcf".WCF_N."_linkList_link
				WHERE   userID = ".$user->userID;
			$row = WCF::getDB()->getFirstRow($sql);

			$this->points[$user->userID] = $row['links'];
		}

		return $this->points[$user->userID];
	}

	/**
	 * @see Modifier::getIcon()
	 */
	public function getIcon() {
		return 'linkListLink';
	}
}
?>