<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

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
class LinkListCategoryPermissionsObjectsPage extends AbstractPage {
	/**
	 * query
	 * 
	 * @var	array
	 */
	public $query = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['query'])) {
			$queryString = $_REQUEST['query'];
			if (CHARSET != 'UTF-8') {
				$queryString = StringUtil::convertEncoding('UTF-8', CHARSET, $queryString);
			}
			$this->query = ArrayUtil::trim(explode(',', $queryString));
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();
				
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n<objects>";
		
		if (count($this->query)) {
			// get users and groups
			$names = implode("','", array_map('escapeString', $this->query));
			$sql = "SELECT		groupName, groupID
				FROM		wcf".WCF_N."_group
				WHERE		groupName IN ('".$names."')
				ORDER BY 	groupName";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				echo "<object>";
				echo "<name><![CDATA[".StringUtil::escapeCDATA($row['groupName'])."]]></name>";
				echo "<type>group</type>";
				echo "<id>".$row['groupID']."</id>";
				echo "</object>";
			}
		}
		echo '</objects>';
		exit;
	}
}
?>