<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkComment.class.php');

/**
 * Provides functions to add, edit or delete linklist link comments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link.comment
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkCommentEditor extends LinkListLinkComment {	
	/**
	 * Creates a new linklist link comment.
	 *
	 * @param 	integer		$linkID
	 * @param 	integer		$categoryID
	 * @param 	string		$username
	 * @param 	string		$message
	 * @param 	array<integer>	$options
	 * @return	LinkListLinkCommentEditor
	 */
	public static function create($linkID, $categoryID, $username, $message, $options) {
		// save new comment
		$sql = "INSERT INTO	wcf".WCF_N."_linkList_link_comment
					(linkID, categoryID, userID, username, message, time, enableSmilies, enableHtml, enableBBCodes, showSignature, ipAddress)
			VALUES		(".$linkID.", ".$categoryID.", ".WCF::getUser()->userID.", '".escapeString($username)."', '".escapeString($message)."', ".TIME_NOW.", ".$options['enableSmilies'].", ".$options['enableHtml'].", ".$options['enableBBCodes'].", ".$options['showSignature'].", '".escapeString(WCF::getSession()->ipAddress)."')";
		WCF::getDB()->sendQuery($sql);

		// get new commentID
		$commentID = WCF::getDB()->getInsertID("wcf".WCF_N."_linkList_link_comment", 'commentID');
		
		// update comments count
		$sql = "UPDATE	wcf".WCF_N."_linkList_link
			SET	comments = comments + 1
			WHERE	linkID = ".$linkID;
		WCF::getDB()->sendQuery($sql);
		
		// return a new instance of LinkListLinkCommentEditor
		return new LinkListLinkCommentEditor($commentID);
	}
	
	/**
	 * Creates a preview of a new linklist link comment.
	 *
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'linkID' => 0,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes
		);

		// include ViewableLinkListLinkComment
		require_once(WCF_DIR.'lib/data/linkList/link/comment/ViewableLinkListLinkComment.class.php');
		// create a new instance of ViewableLinkListLinkComment
		$comment = new ViewableLinkListLinkComment(null, $row);
		
		// return parsed comment message
		return $comment->getFormattedMessage();
	}
	
	/**
	 * Updates the category ids of a comment.
	 * 
	 * @param	string		$commentIDs
	 */
	public static function refreshAll($commentIDs) {
		if (empty($commentIDs)) return;
		
		$sql = "UPDATE wcf".WCF_N."_linkList_link_comment link_comment
			SET	categoryID = (
					SELECT categoryID
					FROM	wcf".WCF_N."_linkList_link
					WHERE	linkID = link_comment.linkID
					)
			WHERE	commentID IN (".$commentIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates a linklist link comment.
	 *
	 * @param 	string		$message
	 * @param 	array<integer>	$options
	 */
	public function update($message, $options) {
		$sql = "UPDATE	wcf".WCF_N."_linkList_link_comment
			SET	message = '".escapeString($message)."',
				lastChangeTime = ".TIME_NOW.",
				enableSmilies = ".$options['enableSmilies'].",
				enableHtml = ".$options['enableHtml'].",
				enableBBCodes = ".$options['enableBBCodes']."
			WHERE	commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this linklist link comment.
	 */
	public function delete($linkID) {
		// delete comment
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link_comment
			WHERE		commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
		
		// update comments count
		$sql = "UPDATE	wcf".WCF_N."_linkList_link
			SET	comments = comments - 1
			WHERE	linkID = ".$linkID;
		WCF::getDB()->sendQuery($sql);
	}
}
?>