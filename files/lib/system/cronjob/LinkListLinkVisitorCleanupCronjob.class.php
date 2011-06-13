<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Does a cleanup of the saved linklist link visitors.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.link.lastVisitors
 * @subpackage system.cronjob
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkVisitorCleanupCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		// delete old visitors
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link_last_visitor
			WHERE		time < ".(TIME_NOW - 86400 * 14);
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>