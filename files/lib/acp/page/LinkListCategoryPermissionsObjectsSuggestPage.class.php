<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/page/UserSuggestPage.class.php');

/**
 * Outputs an XML document with a list of permissions objects (user or user groups).
 *
 * @author	Sebastian Oettl (edited by Christoph H.)
 * @copyright	2009-2010 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	acp.page
 * @category	WoltLab Community Framework (WCF)
 */
class LinkListCategoryPermissionsObjectsSuggestPage extends UserSuggestPage {
	/**
	 * @see Page::show()
	 */
	public function show() {
		AbstractPage::show();
				
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n<suggestions>\n";
		
		if (!empty($this->query)) {
			// get suggestions
			$sql = "SELECT		groupName
				FROM		wcf".WCF_N."_group
				WHERE		groupName LIKE '".escapeString($this->query)."%'
				ORDER BY	groupName";
			$result = WCF::getDB()->sendQuery($sql, 10);
			while ($row = WCF::getDB()->fetchArray($result)) {
				echo "<group><![CDATA[".StringUtil::escapeCDATA($row['groupName'])."]]></group>\n";
			}
		}
		echo '</suggestions>';
		exit;
	}
}
?>