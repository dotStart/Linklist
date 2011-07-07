<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/message/multiQuote/MultiQuoteManager.class.php');

/**
 * Shows a quote button on the linklist link and linklist link comment list page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.quote.infraction
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListQuoteButtonListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className == 'LinkListLinkPage') {
			// add multi quote to linklist link
			$isQuoted = MultiQuoteManager::getQuoteCount($eventObj->link->linkID, 'linkListLink');
			WCF::getTPL()->assign(array(
				'link' => $eventObj->link,
				'isQuoted' => $isQuoted
			));
			WCF::getTPL()->append('userMessages', WCF::getTPL()->fetch('linkListLinkMultiQuote'));
		}
		else {
			// add multi quote to linklist link comment list
			WCF::getTPL()->assign(array(
				'linkID' => $eventObj->linkID,
			));
			WCF::getTPL()->append('userMessages', WCF::getTPL()->fetch('multiQuote'));

			foreach ($eventObj->commentList->comments as $comment) {
				WCF::getTPL()->append('userMessages', '<script type="text/javascript">
													//<![CDATA[
													quoteData.set(\'linkListLinkComment-'.$comment->commentID.'\', {
													objectID: '.$comment->commentID.',
													objectType: \'linkListLinkComment\',
													quotes: '.MultiQuoteManager::getQuoteCount($comment->commentID, 'linkListLinkComment').'
													});
													//]]>
												</script>'
				);
			}
		}
	}
}
?>