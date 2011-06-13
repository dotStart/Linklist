<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/URLBBCode.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/URLParser.class.php');

/**
 * Parses URLs to linklist links.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.event.listener
 * @category 	WoltLab Community Framework (WCF)
 */
class URLParserLinkListLinkURLListener implements EventListener {
	protected $links = array();
	protected $linkURLPattern = 'index\.php\?page=LinkListLink&linkID=([0-9]+)';
	
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_LINKLIST || empty(URLParser::$text)) return;
		
		// reset data
		$this->links = array();
		$linkIDArray = array();
		
		// get page urls
		$pageURLs = URLBBCode::getPageURLs();
		$pageURLs = '(?:'.implode('|', array_map('preg_quote', $pageURLs)).')';
		
		// build search pattern
		$linkIDPattern = "!\[url\](".$pageURLs."/?".$this->linkURLPattern.".*?)\[/url\]!i";
		
		// find link ids
		if (preg_match_all($linkIDPattern, URLParser::$text, $matches)) {
			$linkIDArray = $matches[2];
		}
		
		// get links
		if (count($linkIDArray) > 0) {
			// remove duplicates
			$linkIDArray = array_unique($linkIDArray);
				
			$sql = "SELECT	linkID, subject
				FROM 	wcf".WCF_N."_linkList_link
				WHERE 	linkID IN (".implode(",", $linkIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->links[$row['linkID']] = $row['subject'];
			}
			
			if (count($this->links) > 0) {
				// insert links
				URLParser::$text = preg_replace_callback($linkIDPattern, array($this, 'buildLinkURLTagCallback'), URLParser::$text);
			}
		}
	}
	
	/**
	 * Builds the url bbcode tag.
	 * 
	 * @param	array		$matches
	 * @return	string
	 */
	private function buildLinkURLTagCallback($matches) {
		$url = $matches[1];
		$linkID = $matches[2];
		
		if ($linkID != 0 && isset($this->links[$linkID])) {
			return '[url=\''.$url.'\']'.$this->links[$linkID].'[/url]';
		}
		
		return '[url]'.$url.'[/url]';
	}
}
?>