<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');

/**
 * Shows a form to import categories, links and comments.
 *
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList.im-exporter
 * @subpackage	acp.form
 * @category 	WoltLab Community Framework (WCF)
 */
class LinkListDataImportForm extends ACPForm {
	// system
	public $templateName = 'linkListDataImport';
	public $activeMenuItem = 'wcf.acp.menu.link.content.linkList.importAll';
	
	public $fileUpload = '';
	public $data;
	public $fileData = array();
	public $filename = '';
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// check permissions
		WCF::getUser()->checkPermission('admin.linkList.canImExPortLinkListData');
		
		if (isset($_POST['filename'])) $this->filename = StringUtil::trim($_POST['filename']);
		if (isset($_FILES['fileUpload'])) $this->fileUpload = $_FILES['fileUpload'];
	}
	
	
	/**
	 * Reads the data from the importXML file.
	 */
	protected function readLinkListData($tar) {
		// check archive for required XML file
		$xml = 'linkListData.xml';
		$i = $tar->getIndexByFilename($xml);
		if ($i === false) {
			throw new SystemException("Unable to find required file '".$xml."' in the import archive");
		}
		// open xml
		$dataXML = new XML();
		$dataXML->loadString($tar->extractToString($i));
		$xmlContent = $dataXML->getElementTree('linkListData-Export');
		$data = array();
		$i = 0;
		$data['categoryData'] = array();
		$data['categoryStructureData'] = array();
		$data['linkData'] = array();
		$data['commentData'] = array();
		
		
		foreach ($xmlContent['children'] as $child) {
			switch ($child['name']) {
				case 'linkListCategory':
					foreach ($child['children'] as $category) {												 						
						switch ($category['name']) {
							case 'categoryID':
								$data['categoryData'][$i]['categoryID'] = intval($category['cdata']);
								break;
							case 'parentID':
								$data['categoryData'][$i]['parentID'] = intval($category['cdata']);
								break;
							case 'title':
								$data['categoryData'][$i]['title'] = $category['cdata'];
								break;
							case 'description':
								$data['categoryData'][$i]['description'] = $category['cdata'];
								break;
							case 'allowDescriptionHtml':
								$data['categoryData'][$i]['allowDescriptionHtml'] = intval($category['cdata']);
								break;
							case 'image':
								$data['categoryData'][$i]['image'] = intval($category['cdata']);
								break;
						}						
					}
					break;
					
				case 'linkListCategoryStructure':
					foreach ($child['children'] as $categoryStructure) {												 						
						switch ($categoryStructure['name']) {
							case 'parentID':
								$data['categoryStructureData'][$i]['parentID'] = intval($categoryStructure['cdata']);
								break;
							case 'categoryID':
								$data['categoryStructureData'][$i]['categoryID'] = intval($categoryStructure['cdata']);
								break;
							case 'position':
								$data['categoryStructureData'][$i]['position'] = intval($categoryStructure['cdata']);
								break;
						}						
					}
					break;
					
				case 'linkListLink':
					foreach ($child['children'] as $link) {												 						
						switch ($link['name']) {
							case 'linkID':
								$data['linkData'][$i]['linkID'] = intval($link['cdata']);
								break;
							case 'categoryID':
								$data['linkData'][$i]['categoryID'] = intval($link['cdata']);
								break;
							case 'subject':
								$data['linkData'][$i]['subject'] = $link['cdata'];
								break;
							case 'shortDescription':
								$data['linkData'][$i]['shortDescription'] = $link['cdata'];
								break;
							case 'message':
								$data['linkData'][$i]['message'] = $link['cdata'];
								break;
							case 'isDisabled':
								$data['linkData'][$i]['isDisabled'] = intval($link['cdata']);
								break;
							case 'isDeleted':
								$data['linkData'][$i]['isDeleted'] = intval($link['cdata']);
								break;
							case 'isClosed':
								$data['linkData'][$i]['isClosed'] = intval($link['cdata']);
								break;
							case 'isSticky':
								$data['linkData'][$i]['isSticky'] = intval($link['cdata']);
								break;
							case 'userID':
								$data['linkData'][$i]['userID'] = intval($link['cdata']);
								break;
							case 'username':
								$data['linkData'][$i]['username'] = $link['cdata'];
								break;
							case 'url':
								$data['linkData'][$i]['url'] = $link['cdata'];
								break;
							case 'time':
								$data['linkData'][$i]['time'] = intval($link['cdata']);
								break;
							case 'lastChangeTime':
								$data['linkData'][$i]['lastChangeTime'] = intval($link['cdata']);
								break;
							case 'visits':
								$data['linkData'][$i]['visits'] = intval($link['cdata']);
								break;
							case 'lastVisitorID':
								$data['linkData'][$i]['lastVisitorID'] = intval($link['cdata']);
								break;
							case 'lastVisitorName':
								$data['linkData'][$i]['lastVisitorName'] = $link['cdata'];
								break;
							case 'lastVisitTime':
								$data['linkData'][$i]['lastVisitTime'] = intval($link['cdata']);
								break;
							case 'enableSmilies':
								$data['linkData'][$i]['enableSmilies'] = intval($link['cdata']);
								break;
							case 'enableHtml':
								$data['linkData'][$i]['enableHtml'] = intval($link['cdata']);
								break;
							case 'enableBBCodes':
								$data['linkData'][$i]['enableBBCodes'] = intval($link['cdata']);
								break;
							case 'ipAddress':
								$data['linkData'][$i]['ipAddress'] = $link['cdata'];
								break;
						}						
					}
					break;
					
				case 'linkListLinkComment':
					foreach ($child['children'] as $comment) {												 						
						switch ($comment['name']) {
							case 'commentID':
								$data['commentData'][$i]['commentID'] = intval($comment['cdata']);
								break;
							case 'linkID':
								$data['commentData'][$i]['linkID'] = intval($comment['cdata']);
								break;
							case 'categoryID':
								$data['commentData'][$i]['categoryID'] = intval($comment['cdata']);
								break;
							case 'userID':
								$data['commentData'][$i]['userID'] = intval($comment['cdata']);
								break;
							case 'username':
								$data['commentData'][$i]['username'] = $comment['cdata'];
								break;
							case 'message':
								$data['commentData'][$i]['message'] = $comment['cdata'];
								break;
							case 'time':
								$data['commentData'][$i]['time'] = intval($comment['cdata']);
								break;
							case 'enableSmilies':
								$data['commentData'][$i]['enableSmilies'] = intval($comment['cdata']);
								break;
							case 'enableHtml':
								$data['commentData'][$i]['enableHtml'] = intval($comment['cdata']);
								break;
							case 'enableBBCodes':
								$data['commentData'][$i]['enableBBCodes'] = intval($comment['cdata']);
								break;
							case 'ipAddress':
								$data['commentData'][$i]['ipAddress'] = $comment['cdata'];
								break;
						}						
					}
					break;
			}
			$i++;
		}
		return $data;
	}
	
	/**
	 * Get data from import archive
	 */
	public static function getLinkListData($fileUpload) {
		// opens .gz file
		require_once(WCF_DIR.'lib/system/io/Tar.class.php');
		$tar = new Tar($fileUpload);
		// gets data
		$data = self::readLinkListData($tar);		
		$tar->close();		
		return $data;
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->fileUpload)) {
			throw new UserInputException('fileUpload');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		if (empty($this->fileUpload['tmp_name'])) return;
		
		// delete all categories, links and comments
		// delete categories
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category";
		WCF::getDB()->sendQuery($sql);
		
		// delete category structures
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_category_structure";
		WCF::getDB()->sendQuery($sql);

		// delete links
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link";
		WCF::getDB()->sendQuery($sql);
		
		// delete tags
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('de.chrihis.wcf.linkList.link');
			
		$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
			WHERE 		taggableID = ".$taggable->getTaggableID()."";
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// delete attachments
		$sql = "DELETE FROM	wcf".WCF_N."_attachment
				WHERE 		containerType = linkListLink";
			WCF::getDB()->registerShutdownUpdate($sql);
		
		// delete comments
		$sql = "DELETE FROM	wcf".WCF_N."_linkList_link_comment";
		WCF::getDB()->sendQuery($sql);
		
		// get all data
		$data = self::getLinkListData($this->fileUpload['tmp_name']);
		
		// import categories
		foreach ($data['categoryData'] as $categoryData) {
			// insert categories
			$sql = "INSERT INTO	wcf".WCF_N."_linkList_category
					(categoryID, parentID, title, description, allowDescriptionHtml, image, time)
			VALUES	(".$categoryData['categoryID'].", ".$categoryData['parentID'].", '".escapeString($categoryData['title'])."', '".escapeString($categoryData['description'])."', ".$categoryData['allowDescriptionHtml'].", '".escapeString($categoryData['image'])."', ".TIME_NOW.")";
			WCF::getDB()->sendQuery($sql);
		}
		
		// import category structures
		foreach ($data['categoryStructureData'] as $categoryStructureData) {
			// insert category structures
			$sql = "INSERT INTO	wcf".WCF_N."_linkList_category_structure
					(parentID, categoryID, position)
			VALUES	(".$categoryStructureData['parentID'].", ".$categoryStructureData['categoryID'].", ".$categoryStructureData['position'].")";
			WCF::getDB()->sendQuery($sql);
		}
		
		// import links
		if (count($data['linkData'])) {
			foreach ($data['linkData'] as $linkData) {
				// insert links
				$sql = "INSERT INTO	wcf".WCF_N."_linkList_link
					(linkID, categoryID, subject, message, shortDescription, userID, username, url, time, lastChangeTime, isSticky, isDisabled, isDeleted, isClosed, visits, lastVisitorID, lastVisitorName, lastVisitTime, enableSmilies, enableHtml, enableBBCodes, ipAddress)
			VALUES	(".$linkData['linkID'].", ".$linkData['categoryID'].", '".escapeString($linkData['subject'])."', '".escapeString($linkData['message'])."', '".escapeString($linkData['shortDescription'])."', ".$linkData['userID'].", '".escapeString($linkData['username'])."', '".escapeString($linkData['url'])."', ".$linkData['time'].", ".$linkData['lastChangeTime'].", ".$linkData['isSticky'].", ".$linkData['isDisabled'].", ".$linkData['isDeleted'].", ".$linkData['isClosed'].", ".$linkData['visits'].", ".$linkData['lastVisitorID'].", '".escapeString($linkData['lastVisitorName'])."', ".$linkData['lastVisitTime'].", ".$linkData['enableSmilies'].", ".$linkData['enableHtml'].", ".$linkData['enableBBCodes'].", '".escapeString($linkData['ipAddress'])."')";
				WCF::getDB()->sendQuery($sql);
			}
		}
		
		// import comments
		if (count($data['commentData'])) {
			foreach ($data['commentData'] as $commentData) {
				// insert comments
				$sql = "INSERT INTO	wcf".WCF_N."_linkList_link_comment
					(commentID, linkID, categoryID, userID, username, message, time, enableSmilies, enableHtml, enableBBCodes, ipAddress)
				VALUES	(".$commentData['commentID'].", ".$commentData['linkID'].", ".$commentData['categoryID'].", ".$commentData['userID'].", '".escapeString($commentData['username'])."', '".escapeString($commentData['message'])."', ".$commentData['time'].", ".$commentData['enableSmilies'].", ".$commentData['enableHtml'].", ".$commentData['enableBBCodes'].", '".escapeString($commentData['ipAddress'])."')";
				WCF::getDB()->sendQuery($sql);
			}
		}
		
		$this->saved();
		// forward to linklist categories list
		HeaderUtil::redirect('index.php?page=LinkListCategoryList&successfulImport=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		
		parent::show();
	}
	
}
?>