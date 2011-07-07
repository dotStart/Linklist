<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
 
/**
 * Deletes a report of a linklist link..
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.report
 * @subpackage action
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkReportDeleteAction extends AbstractSecureAction {
	/**
	 * report id
	 *
	 * @var integer
	 */
	public $reportID = 0;
	
	/**
	 * link id
	 *
	 * @var integer
	 */
	public $linkID = 0;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get report id
		if (isset($_REQUEST['reportID'])) $this->reportID = intval($_REQUEST['reportID']);
		
		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// delete report
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link_report
			WHERE		reportID = ".$this->reportID;
		WCF::getDB()->sendQuery($sql);
		
		// update isReported in the link table
		$sql = "UPDATE	wcf".WCF_N."_linkList_link
			SET	isReported = 0
			WHERE	linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
		// call event
		$this->executed();
		
		exit;
	}
}
?>