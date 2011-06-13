<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches a many of linklist data.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 * @subpackage system.cache
 * @category 	WoltLab Community Framework (WCF)
 */
class CacheBuilderLinkListStatistics implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		// number of categories
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	wcf".WCF_N."_linkList_category";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['categories'] = $result['amount'];
		
		// number of links
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	wcf".WCF_N."_linkList_link";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['links'] = $result['amount'];
		
		// number of visits
		$sql = "SELECT	SUM(visits) AS amount
				FROM	wcf".WCF_N."_linkList_link";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['visits'] = $row['amount'];
		
		// number of comments
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	wcf".WCF_N."_linkList_link_comment";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['comments'] = $result['amount'];
		
		// get days
		$days = ceil((TIME_NOW - INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		// links per day
		$data['linksPerDay'] = $data['links'] / $days;
		// comments per day
		$data['commentsPerDay'] = $data['comments'] / $days;
		
		// return all numbers
		return $data;
	}
}
?>