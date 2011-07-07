<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

WCF::getCache()->addResource('linkListCategory', WCF_DIR.'cache/cache.linkListCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderLinkListCategory.class.php');

/**
 * Exports all linklist data.
 * 
 * @author 	Christoph H.
 * @copyright	2009 WBBLite2.de & Christoph H.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.chrihis.wcf.linkList.im-exporter
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework 
 */
class LinkListDataExportAction extends AbstractAction {
	/**
	 * list of all links
	 *
	 * @var array
	 */
	public $linkData = array();
	
	/**
	 * list of all categories
	 *
	 * @var array
	 */
	public $categoryData = array();
	
	/**
	 * list of category structure
	 *
	 * @var array
	 */
	public $categoryStructureData = array();
	
	/**
	 * list of all comments
	 *
	 * @var array
	 */
	public $commentData = array();
	
	public $xmlContent;
	public $xmlPath;
	public $fileName;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check permissions
		WCF::getUser()->checkPermission('admin.linkList.canImExPortLinkListData');
		
		// get all links of the database
		$sql = "SELECT * FROM wcf".WCF_N."_linkList_link";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->linkData[] = $row;
		}
		
		// get all categories of the database
		$sql = "SELECT * FROM wcf".WCF_N."_linkList_category";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->categoryData[] = $row;
		}
		
		// get category structure of all categories of the database
		$sql = "SELECT * FROM wcf".WCF_N."_linkList_category_structure";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->categoryStructureData[] = $row;
		}
		
		// get all comments of the database
		$sql = "SELECT * FROM wcf".WCF_N."_linkList_link_comment";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->commentData[] = $row;
		}
	}

	protected function generateXML() {
		// xml version tag and leading tag
		$string = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		$string .= "<data>\n";
		
		// category data
		if (count($this->categoryData)) {
			foreach($this->categoryData as $key => $category) {
				$string .= "\t<linkListCategory>\n";
				$string .= "\t\t<categoryID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['categoryID']) : $category['categoryID']))."</categoryID>\n";
				$string .= "\t\t<parentID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['parentID']) : $category['parentID']))."</parentID>\n";
				$string .= "\t\t<categoryType>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['categoryType']) : $category['categoryType']))."</categoryType>\n";
				$string .= "\t\t<title><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['title']) : $category['title']))."]]></title>\n";
				$string .= "\t\t<description><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['description']) : $category['description']))."]]></description>\n";
				$string .= "\t\t<allowDescriptionHtml>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['allowDescriptionHtml']) : $category['allowDescriptionHtml']))."</allowDescriptionHtml>\n";
				$string .= "\t\t<image><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $category['image']) : $category['image']))."]]></image>\n";
				$string .= "\t</linkListCategory>\n";
			}
		}
		
		// category structure data
		if (count($this->categoryStructureData)) {
			foreach($this->categoryStructureData as $key => $categoryStructure) {
				$string .= "\t<linkListCategoryStructure>\n";
				$string .= "\t\t<parentID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $categoryStructure['parentID']) : $categoryStructure['parentID']))."</parentID>\n";
				$string .= "\t\t<categoryID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $categoryStructure['categoryID']) : $categoryStructure['categoryID']))."</categoryID>\n";
				$string .= "\t\t<position>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $categoryStructure['position']) : $categoryStructure['position']))."</position>\n";
				$string .= "\t</linkListCategoryStructure>\n";
			}
		}
		
		// link data
		if (count($this->linkData)) {
			foreach($this->linkData as $key => $link) {
				$string .= "\t<linkListLink>\n";
				$string .= "\t\t<linkID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['linkID']) : $link['linkID']))."</linkID>\n";
				$string .= "\t\t<categoryID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['categoryID']) : $link['categoryID']))."</categoryID>\n";
				$string .= "\t\t<subject><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['subject']) : $link['subject']))."]]></subject>\n";
				$string .= "\t\t<shortDescription><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['shortDescription']) : $link['shortDescription']))."]]></shortDescription>\n";
				$string .= "\t\t<message><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['message']) : $link['message']))."]]></message>\n";
				$string .= "\t\t<isDisabled>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['isDisabled']) : $link['isDisabled']))."</isDisabled>\n";
				$string .= "\t\t<isDeleted>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['isDeleted']) : $link['isDeleted']))."</isDeleted>\n";
				$string .= "\t\t<isClosed>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['isClosed']) : $link['isClosed']))."</isClosed>\n";
				$string .= "\t\t<isSticky>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['isSticky']) : $link['isSticky']))."</isSticky>\n";
				$string .= "\t\t<userID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['userID']) : $link['userID']))."</userID>\n";
				$string .= "\t\t<username><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['username']) : $link['username']))."]]></username>\n";
				$string .= "\t\t<url><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['url']) : $link['url']))."]]></url>\n";
				$string .= "\t\t<time>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['time']) : $link['time']))."</time>\n";
				$string .= "\t\t<lastChangeTime>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['lastChangeTime']) : $link['lastChangeTime']))."</lastChangeTime>\n";
				$string .= "\t\t<visits>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['visits']) : $link['visits']))."</visits>\n";
				$string .= "\t\t<lastVisitorID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['lastVisitorID']) : $link['lastVisitorID']))."</lastVisitorID>\n";
				$string .= "\t\t<lastVisitorName><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['lastVisitorName']) : $link['lastVisitorName']))."]]></lastVisitorName>\n";
				$string .= "\t\t<lastVisitTime>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['lastVisitTime']) : $link['lastVisitTime']))."</lastVisitTime>\n";
				$string .= "\t\t<enableSmilies>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['enableSmilies']) : $link['enableSmilies']))."</enableSmilies>\n";
				$string .= "\t\t<enableHtml>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['enableHtml']) : $link['enableHtml']))."</enableHtml>\n";
				$string .= "\t\t<enableBBCodes>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['enableBBCodes']) : $link['enableBBCodes']))."</enableBBCodes>\n";
				$string .= "\t\t<ipAddress><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $link['ipAddress']) : $link['ipAddress']))."]]></ipAddress>\n";
				$string .= "\t</linkListLink>\n";
			}
		}
		
		// comment data
		if (count($this->commentData)) {
			foreach($this->commentData as $key => $comment) {
				$string .= "\t<linkListLinkComment>\n";
				$string .= "\t\t<commentID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['commentID']) : $comment['commentID']))."</commentID>\n";
				$string .= "\t\t<linkID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['linkID']) : $comment['linkID']))."</linkID>\n";
				$string .= "\t\t<categoryID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['categoryID']) : $comment['categoryID']))."</categoryID>\n";
				$string .= "\t\t<userID>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['userID']) : $comment['userID']))."</userID>\n";
				$string .= "\t\t<username><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['username']) : $comment['username']))."]]></username>\n";
				$string .= "\t\t<message><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['message']) : $comment['message']))."]]></message>\n";
				$string .= "\t\t<time>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['time']) : $comment['time']))."</time>\n";
				$string .= "\t\t<enableSmilies>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['enableSmilies']) : $comment['enableSmilies']))."</enableSmilies>\n";
				$string .= "\t\t<enableHtml>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['enableHtml']) : $comment['enableHtml']))."</enableHtml>\n";
				$string .= "\t\t<enableBBCodes>".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['enableBBCodes']) : $comment['enableBBCodes']))."</enableBBCodes>\n";
				$string .= "\t\t<ipAddress><![CDATA[".StringUtil::escapeCDATA((CHARSET != 'UTF-8' ? StringUtil::convertEncoding(CHARSET, 'UTF-8', $comment['ipAddress']) : $comment['ipAddress']))."]]></ipAddress>\n";
				$string .= "\t</linkListLinkComment>\n";
			}
		}
		// ending tag
		$string .= "</data>";
		
		$this->xmlContent = $string;
		
		// write the xml file with all this data
		require_once(WCF_DIR.'lib/system/io/File.class.php');
		$xml = new File(WCF_DIR.'lib/data/linkList/tmp/linkListData.xml');
		$xml->write($this->xmlContent);
		$xml->close();
	} 
	
	protected function getPath() {
		// get xml path
		if (count($this->categoryData)) {
			$xmlPath = WCF_DIR.'lib/data/linkList/tmp/linkListData.xml';
		}	
		$this->xmlPath = $xmlPath;
	}

	/**
	 * Generates g-zipped file of all data.
	 */
	protected function generateTar() {

		if (!count($this->categoryData)) {
			// forward to list page
			HeaderUtil::redirect('index.php?page=LinkListCategoryList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
		}
		
		$this->generateXML();
		$path = $this->getPath();
		$this->filename = WCF_DIR.'lib/data/linkList/tmp/linkListData-Export.'.StringUtil::getRandomID().'.gz';
		// generate gZipped export file 
		require_once(WCF_DIR.'lib/system/io/TarWriter.class.php');
		$tar = new TarWriter($this->filename, true);
		// add file
 		$tar->add($this->xmlPath, '', WCF_DIR.'lib/data/linkList/tmp/');
 		// create archive
 		$tar->create();
 		// unlink temp xml file
 		@unlink($this->xmlPath);
	} 
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// starts generating export file
		$this->generateTar();	
		$this->executed();
		// headers for downloading file
		header('Content-Type: application/x-gzip; charset='.CHARSET);
		header('Content-Disposition: attachment; filename="LinkListData-Export.gz"');
		readfile($this->filename);
		// delete temp file
		@unlink($this->filename);
	}
}
?>