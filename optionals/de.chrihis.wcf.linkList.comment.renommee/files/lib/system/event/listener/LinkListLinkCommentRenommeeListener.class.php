<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/user/RenommeeUser.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkCommentRenommeeObject.class.php');

/**
 * Displays the rating form and linklist link comment renommee on the comment list page.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.comment.renommee
 * @subpackage system.event.listener
 * @category 	Renommee System
 */
class LinkListLinkCommentRenommeeListener implements EventListener {
	/**
	 * Current linklist link comment
	 *
	 * @var LinkListLinkCommentRenommeeObject
	 */
	protected $comment = null;
			 
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_USER_RENOMMEE || !MODULE_LINKLIST || !WCF::getUser()->getPermission('user.linkList.canViewComment')) return;
		if ($eventName == 'readData') {
			if (USER_RENOMMEE_SHOW_RENOMMEE) {
				if (!empty($eventObj->commentList->sqlSelects)) $eventObj->commentList->sqlSelects .= ',';
				$eventObj->commentList->sqlSelects .= " (	SELECT	SUM(points)
										FROM	wcf".WCF_N."_user_renommee
										WHERE	objectID = link_comment.commentID
										AND	objectType = 'linkListLinkComment') AS objectRenommee";
			}
			if (WCF::getUser()->userID && WCF::getUser()->getPermission('user.renommee.canAddRenommee')) {
				if (!empty($eventObj->commentList->sqlSelects)) $eventObj->commentList->sqlSelects .= ',';
				$eventObj->commentList->sqlSelects .= " user_renommee.evaluatorID AS rated";
				$eventObj->commentList->sqlJoins .= "	LEFT JOIN	wcf".WCF_N."_user_renommee user_renommee
										ON		(user_renommee.evaluatorID = ".WCF::getUser()->userID."
										AND		user_renommee.objectID = link_comment.commentID
										AND		user_renommee.objectType = 'linkListLinkComment')";
			}
		}
		else if ($eventName == 'assignVariables') {
			$currentUser = new RenommeeUser(WCF::getUser()->userID);
			WCF::getTPL()->assign(array(
				'objectType' => 'linkListLinkComment',
				'currentUser' => $currentUser,
				'languageVariables' => array(
					'wcf.user.renommee.linkListLinkComment.state' => WCF::getLanguage()->get('wcf.user.renommee.linkListLinkComment.state'),
					'wcf.user.renommee.linkListLinkComment.state.positive' => WCF::getLanguage()->get('wcf.user.renommee.linkListLinkComment.state.positive'),
					'wcf.user.renommee.linkListLinkComment.state.negative' => WCF::getLanguage()->get('wcf.user.renommee.linkListLinkComment.state.negative'),
					'wcf.user.renommee.linkListLinkComment.renommee' => WCF::getLanguage()->get('wcf.user.renommee.linkListLinkComment.renommee')
				)
			));
			
			$comments = array();
			$additionalSmallButtons = array();
			foreach ($eventObj->commentList->comments as &$comment) {
				if ($comment->userID) {
					$renommeeObject = new LinkListLinkCommentRenommeeObject(null, null, $comment);
					WCF::getTPL()->assign('object', $renommeeObject);
					$additionalSmallButtons[$renommeeObject->getObjectID()] = WCF::getTPL()->fetch('renommeeSmallButtons');
					$comments[] = $renommeeObject;
				}
			}
			
			WCF::getTPL()->assign(array(
				'objects' => $comments
			));
			
			WCF::getTPL()->append(array(
				'userMessages' => WCF::getTPL()->fetch('renommeeDialogHeader'),
				'additionalSmallButtons' => $additionalSmallButtons,
				'specialStyles' => '<style type="text/css">.inlineObjectRenommee { margin-top: 40px; }</style>'
			));
		}
	}
}
?>