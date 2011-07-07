<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows a message, if the import was successful
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.im-exporter
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListCategoryListSuccessfulImportListener implements EventListener {
	/**
	 * If the import was successfully
	 * @var boolean
	 */
	public $successfulImport = false;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (isset($_REQUEST['successfulImport'])) $this->successfulImport = true;

		if ($this->successfulImport) {
			WCF::getTPL()->append(array(
				'adminMessages' => '<div class="success">'.WCF::getLanguage()->get('wcf.acp.linkList.import.successful',
				array(
					'$packageID' => PACKAGE_ID,
					'$sidArg2nd' => SID_ARG_2ND_NOT_ENCODED
				)).'</div>',
			));
		}
	}
}
?>
