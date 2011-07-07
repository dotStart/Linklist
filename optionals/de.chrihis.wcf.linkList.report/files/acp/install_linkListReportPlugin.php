<?php
/**
 * Sets the group options.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.linkList.report
 * @category 	WoltLab Community Framework (WCF)
 */

// get package id
$packageID = $this->installation->getPackageID();

// user, mod and admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID IN (4,5,6)
		AND optionID IN (
	SELECT	optionID
		FROM	wcf".WCF_N."_group_option
		WHERE	optionName = 'mod.linkList.canAdministrateReportedLinks'
			AND packageID IN (
				SELECT	dependency
				FROM	wcf".WCF_N."_package_dependency
				WHERE	packageID = ".$packageID."
				)
		)
	AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);


?>