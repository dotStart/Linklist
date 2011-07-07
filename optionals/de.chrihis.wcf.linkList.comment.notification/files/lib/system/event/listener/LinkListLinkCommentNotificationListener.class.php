<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/user/notification/NotificationHandler.class.php');

/**
 * Handles the notification system regarding the link list link comment.
 *
 * @author	Christoph H.
 * @copyright	2011 Christoph H. (Chrihis)
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.chrihis.wcf.linkList.omment.notification
 * @subpackage	system.event.listener
 * @category 	WoltLab Community Framework (commercial)
 */
class LinkListLinkCommentNotificationListener implements EventListener {

		/**
		 * @see EventListener::execute()
		 */
		public function execute($eventObj, $className, $eventName) {
				if (!MODULE_USER_NOTIFICATION) return;
				switch ($className) {
					// fire events
					case 'LinkListLinkCommentAddForm':
					if (WCF::getUser()->userID != $eventObj->link->userID) {
						NotificationHandler::fireEvent('newLinkComment', 'linkListLink', $eventObj->comment->commentID, $eventObj->link->userID);
					}
					break;
					// revoke events
					case 'LinkListLinkCommentDeleteAction':
						NotificationHandler::revokeEvent(array('newLinkComment'), 'linkListLink', $eventObj->comment);
					break;
					// confirm notifications
					case 'LinkListLinkCommentListPage':
						if (WCF::getUser()->userID) {
							// determine users which might be affected by confirmations
							$userIDScope = array($eventObj->link->userID);
							$objectIDScope = array();
							foreach ($eventObj->commentList->getObjects() as $comment) {
									$objectIDScope[] = $comment->commentID;
							}
							if (count($objectIDScope) && in_array(WCF::getUser()->userID, $userIDScope)) {
								$user = new NotificationUser(null, WCF::getUser(), false);
								$objectTypeObject = NotificationHandler::getNotificationObjectTypeObject('linkListLink');
								if (isset($user->notificationFlags[$objectTypeObject->getPackageID()]) && $user->notificationFlags[$objectTypeObject->getPackageID()] > 0) {
									$count = NotificationEditor::markConfirmedByObjectVisit($user->userID, array('newLinkComment'), 'linkListLink', $objectIDScope);
									$user->removeOutstandingNotification($objectTypeObject->getPackageID(), $count);
								}
							}
						}
						break;
				}
		}
}
?>