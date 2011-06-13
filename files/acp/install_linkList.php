<?php
/**
 * Sets group options and write style files.
 * 
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.chrihis.wcf.linkList
 * @category 	WoltLab Community Framework 
 */
$packageID = $this->installation->getPackageID();
// refresh style files
require_once(WCF_DIR.'lib/data/style/StyleEditor.class.php');
$sql = "SELECT 	*
		FROM 	wcf".WCF_N."_style";
$result = WCF::getDB()->sendQuery($sql);
while ($row = WCF::getDB()->fetchArray($result)) {
	$style = new StyleEditor(null, $row);
	$style->writeStyleFile();
}

// user, mod and admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
		SET	optionValue = 1
		WHERE	groupID IN (4,5,6)
			AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	optionName LIKE 'user.linklist.%'
					OR optionName LIKE 'mod.linklist.%'
					OR optionName LIKE 'admin.linklist.%'
						AND packageID IN (
					SELECT	dependency
					FROM	wcf".WCF_N."_package_dependency
					WHERE	packageID = ".$packageID."
				)
			)
		AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);
?>