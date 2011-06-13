<?php
// wcf imports
require_once(WCF_DIR.'lib/data/linkList/link/LinkListLink.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a viewable linklist link.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class ViewableLinkListLink extends LinkListLink {
	/**
	 * list of general information
	 * 
	 * @var	array
	 */
	public $generalDataFields = array();

	/**
	 * Returns formatted message.
	 *
	 * @return	string
	 */	
	public function getFormattedMessage() {
		// require message parser
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		
		// set output type
		MessageParser::getInstance()->setOutputType('text/html');
		
		// set attachments
		require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
		AttachmentBBCode::setMessageID($this->linkID);
		
		// return parsed message
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->messagePreview);
	}
	
	/**
	 * Returns the formatted short description.
	 * 
	 * @return	string
	 */
	public function getFormattedShortDescription() {
		// require simple message parser
		require_once(WCF_DIR.'lib/data/message/bbcode/SimpleMessageParser.class.php');
		
		if ($this->shortDescription != '') {
			// parse message and return this
			return SimpleMessageParser::getInstance()->parse($this->shortDescription);
		}
		else {
			// pre-format
			$message = StringUtil::trim(StringUtil::unifyNewlines($this->message));
		
			// find 1st paragraph
			$excerpt = preg_replace('/^(.*?)\n\n.*/s', '$1', $message);
			if (StringUtil::length($excerpt) != StringUtil::length($message)) {
				$this->data['hasMoreText'] = 1;
			}

			// parse excerpt and return this
			return SimpleMessageParser::getInstance()->parse($excerpt);
		}
	}

	/**
	 * Handles the given resultset.
	 *
	 * @param 	array 		$row		resultset with link data form database
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// user profile
		$this->author = new UserProfile($this->userID);
		
		// author
		$this->generalDataFields[] = array(
			'title' => WCF::getLanguage()->get('wcf.linkList.link.author'),
			'value' => $this->userID ? '<a href="index.php?page=User&amp;userID='.$this->userID.SID_ARG_2ND.'">'.$this->username.'</a>' : $this->username,
			'icon' => StyleManager::getStyle()->getIconPath('userM.png')
		);
		
		// last change time
		$this->generalDataFields[] = array(
			'title' => WCF::getLanguage()->get('wcf.linkList.link.lastChangeTime'),
			'value' => DateUtil::formatTime(null, $this->lastChangeTime, true),
			'icon' => StyleManager::getStyle()->getIconPath('cronjobsM.png')
		);
	
		// time
		$this->generalDataFields[] = array(
			'title' => WCF::getLanguage()->get('wcf.linkList.link.time'),
			'value' => DateUtil::formatTime(null, $this->time, false),
			'icon' => StyleManager::getStyle()->getIconPath('onlineM.png')
		);
		
		// visits
		$this->generalDataFields[] = array(
			'title' => WCF::getLanguage()->get('wcf.linkList.link.visits'),
			'value' => StringUtil::formatInteger($this->visits),
			'icon' => null
		);
		
	}
	
	/**
	 * Returns the filename of the link icon.
	 *
	 * @return	string		filename of the link icon
	 */
	public function getIconName() {
		// standard filename
		$icon = 'linkListLink';
		
		// deleted
		if ($this->isDeleted) return 'Trash';
		
		// isSticky
		if ($this->isSticky == 1) $icon .= 'Sticky';
		
		// isClosed
		if ($this->isClosed) $icon .= 'Closed';
		
		return $icon;
	}
	
	/**
	 * Gets the general data fields.
	 *
	 * @return	array		general data fields
	 */
	public function getGeneralDataFields() {		
		return $this->generalDataFields;
	}
	
	/**
	 * Returns the user object.
	 * 
	 * @return	UserProfile
	 */
	public function getAuthor() {
		return $this->author;
	}
}
?>