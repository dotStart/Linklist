<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/Tagged.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');

/**
 * An implementation of Tagged to support the tagging of linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage	data.linkList
 * @category 	WoltLab Community Framework (WCF)
 */
class TaggedLinkListLink extends ViewableLinkListLink implements Tagged {
	/**
	 * user object
	 * 
	 * @var	User
	 */
	protected $user = null;

	/**
	 * @see ViewableLinkListLink::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// get user
		$this->user = new User(null, array('userID' => $this->userID, 'username' => $this->username));
	}

	/**
	 * @see Tagged::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see Tagged::getObjectID()
	 */
	public function getObjectID() {
		return $this->linkID;
	}

	/**
	 * @see Tagged::getTaggable()
	 */
	public function getTaggable() {
		return $this->taggable;
	}
	
	/**
	 * @see Tagged::getDescription()
	 */
	public function getDescription() {
		// parse message
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		return $parser->parse($this->messagePreview, true, false, true, false);
	}

	/**
	 * @see Tagged::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkS.png');
	}

	/**
	 * @see Tagged::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkM.png');
	}

	/**
	 * @see Tagged::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('linkListLinkL.png');
	}

	/**
	 * @see Tagged::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getDate() {
		return $this->time;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getURL() {
		return 'index.php?page=LinkListLink&linkID='.$this->linkID;
	}
}
?>