<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');
require_once(WCF_DIR.'lib/data/linkList/link/ViewableLinkListLink.class.php');

/**
 * This class extends the viewable linklist link by functions for a search result output.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage data.linkList.link
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListLinkSearchResult extends ViewableLinkListLink {
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->data['messagePreview'] = true;
	}

	/**
	 * @see ViewableLinkListLink::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::parse(parent::getFormattedMessage());
	}
}
?>