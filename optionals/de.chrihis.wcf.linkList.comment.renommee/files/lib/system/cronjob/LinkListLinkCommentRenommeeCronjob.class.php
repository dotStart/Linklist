<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');
require_once(WCF_DIR.'lib/data/user/RenommeeUser.class.php');

/**
 * Cleans up old renommee data based on deleted linklist link comments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.comment.renommee
 * @subpackage system.cronjob
 * @category 	Renommee System
 */
class LinkListLinkCommentRenommeeCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		// delete renommee data of inexising comments
		$userIDs = '';
		$sql = "SELECT	userID
			FROM	wcf".WCF_N."_user_renommee
			WHERE	objectID NOT IN (SELECT commentID
						FROM	wcf".WCF_N."_linkList_link_comment)
				AND	objectType = 'linkListLinkComment'";
		WCF::getDB()->sendQuery($sql);
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$userIDs .= ','.$row['userID'];
		}

		if (!empty($userIDs)) {
			// get users
			$sql = "SELECT		user.*, user_option.*,
						GROUP_CONCAT(DISTINCT groups.groupID ORDER BY groups.groupID ASC SEPARATOR ',') AS groupIDs
				FROM		wcf".WCF_N."_user user
				LEFT JOIN	wcf".WCF_N."_user_option_value user_option
				ON		(user_option.userID = user.userID)
				LEFT JOIN	wcf".WCF_N."_user_to_groups groups
				ON		(groups.userID = user.userID)
				WHERE		user.userID IN(0".$userIDs.")
				GROUP BY	user.userID";
			$result = WCF::getDB()->sendQuery($sql);

			$sql = "DELETE FROM	wcf".WCF_N."_user_renommee
				WHERE		objectID NOT IN (SELECT commentID
								FROM	wcf".WCF_N."_linkList_link_comment)
						AND	objectType = 'linkListLinkComment'";
			WCF::getDB()->sendQuery($sql);

			while ($row = WCF::getDB()->fetchArray($result)) {
				$user = new RenommeeUser(null, $row);
				$user->updateRenommee();
				$user->updateModifier();
			}
		}

	}
}
?>