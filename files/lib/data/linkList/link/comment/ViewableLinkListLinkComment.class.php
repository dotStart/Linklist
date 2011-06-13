<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/comment/LinkListLinkComment.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarObject.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a viewable linklist link comment.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link.comment
 * @category 	WoltLab Community Framework (WCF)
 */
class ViewableLinkListLinkComment extends LinkListLinkComment implements MessageSidebarObject {
	/**
	 * user object
	 *
	 * @var UserProfile
	 */
	protected $user = null;
	protected $signature = null;
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// create a new UserProfile instance
		$this->user = new UserProfile($this->userID);
	}
	
	/**
	 * Returns an excerpt of the message.
	 * 
	 * @return	string
	 */
	public function getExcerpt() {
		$message = self::getFormattedMessage();
		
		// remove html codes
		$message = StringUtil::stripHTML($message);
		
		// decode html
		$message = StringUtil::decodeHTML($message);
		
		// get abstract
		if (StringUtil::length($message) > 100) {
			$message = StringUtil::substring($message, 0, 97) . '...';
		}
		
		// trim message
		$message = StringUtil::trim($message);
		
		// encode html
		if (!empty($message)) {
			$message = StringUtil::encodeHTML($message);
		}
		else {
			$message = '#'.$this->commentID;
		}
		
		return $message;
	}

	/**
	 * Returns the formatted message.
	 * 
	 * @return	string
	 */
	public function getFormattedMessage() {
		// include MessageParser
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		// set output type
		MessageParser::getInstance()->setOutputType('text/html');
		
		// parse the comment message and return this
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, false);
	}
	
	/**
	 * Returns the signature of this comment author
	 * 
	 * @return	string
	 */
	public function getSignature() {
		if ($this->signature === null) {
			$this->signature = '';
			
			if ($this->showSignature && (!WCF::getUser()->userID || WCF::getUser()->showSignature) && !$this->user->disableSignature) {
				if ($this->user->signatureCache) $this->signature = $this->user->signatureCache;
				else if ($this->user->signature) {
					$parser = MessageParser::getInstance();
					$parser->setOutputType('text/html');
					$this->signature = $parser->parse($this->user->signature, $this->user->enableSignatureSmilies, $this->user->enableSignatureHtml, $this->user->enableSignatureBBCodes, false);
				}
			}
		}
		
		return $this->signature;
	}
	
	/**
	 * @see MessageSidebarObject::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageID()
	 */
	public function getMessageID() {
		return $this->commentID;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageType()
	 */
	public function getMessageType() {
		return 'linkListLinkComment';
	}
}
?>