/**
 * @author 	Christoph H.
 * @copyright	2010 Christoph H.
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	de.chrihis.wcf.linkList
 */
function LinkListLinkListEdit(data, count) {
	this.data = data;
	this.count = count;
	
	/**
	 * Saves the marked status.
	 */
	this.saveMarkedStatus = function(data) {
		var ajaxRequest = new AjaxRequest();
		ajaxRequest.openPost('index.php?page=LinkListLinkAction&t='+SECURITY_TOKEN+SID_ARG_2ND, data);
	}
	
	/**
	 * Returns a list of the edit options for the edit menu.
	 */
	this.getEditOptions = function(id) {
		var options = new Array();
		var i = 0;
		var linkListLink = this.data.get(id);
		
		// edit subject
		if (permissions['canEditLink']) {
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.startTitleEdit('+id+');';
			options[i]['text'] = language['wcf.linkList.category.links.editSubject'];
			i++;
		}
		
		// make sticky
		if (permissions['canPinLink']) {
			if (linkListLink.isSticky == 1) {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.unstick('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.unstick'];
				i++;
			}
			else {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.stick('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.stick'];
				i++;
			}
		}
		
		// enable / disable
		if (permissions['canEnableLink']) {
			if (linkListLink.isDisabled == 1) {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.enable('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.enable'];
				i++;
			}
			else if (linkListLink.isDeleted == 0) {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.disable('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.disable'];
				i++;
			}
		}
			
		// close / open
		if (permissions['canCloseLink']) {
			if (linkListLink.isClosed == 1) {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.open('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.open'];
				i++;
			}
			else {
				options[i] = new Object();
				options[i]['function'] = 'linkListLinkListEdit.close('+id+');';
				options[i]['text'] = language['wcf.linkList.category.links.close'];
				i++;
			}
		}
		
		// delete
		if (permissions['canDeleteLink'] && (permissions['canDeleteLinkCompletely'] || (linkListLink.isDeleted == 0 && ENABLE_RECYCLE_BIN))) {
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.remove('+id+');';
			options[i]['text'] = (linkListLink.isDeleted == 0 ? language['wcf.global.button.delete'] : language['wcf.global.button.deleteCompletely']);
			i++;
		}
			
		// recover
		if (linkListLink.isDeleted == 1 && permissions['canDeleteLinkCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.recover('+id+');';
			options[i]['text'] = language['wcf.linkList.category.links.recover'];
			i++;
		}
		
		// edit title
		if (permissions['canEditLink']) {
			options[i] = new Object();
			options[i]['function'] = 'document.location.href = fixURL("index.php?form=LinkListLinkEdit&linkID='+id+SID_ARG_2ND+'")';
			options[i]['text'] = language['wcf.global.button.edit'];
			i++;
		}
			
		// marked status
		if (permissions['canEditLink']) {
			var markedStatus = linkListLink ? linkListLink.isMarked : false;
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.parentObject.markItem(' + (markedStatus ? 'false' : 'true') + ', '+id+');';
			options[i]['text'] = markedStatus ? language['wcf.global.button.unmark'] : language['wcf.global.button.mark'];
			i++;
		}
		
		return options;
	}
	
	/**
	 * Returns a list of the edit options for the edit marked menu.
	 */
	this.getEditMarkedOptions = function() {
		var options = new Array();
		var i = 0;
		
		// move
		if (permissions['canMoveLink']) {
			options[i] = new Object();
			options[i]['function'] = "linkListLinkListEdit.move('move');";
			options[i]['text'] = language['wcf.linkList.category.links.move'];
			i++;
		}
			
		// close and close
		if (permissions['canCloseLink']) {
			// close
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.closeAll();';
			options[i]['text'] = language['wcf.linkList.category.links.close'];
			i++;
		
			// open
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.openAll();';
			options[i]['text'] = language['wcf.linkList.category.links.open'];
			i++;
		}
		
		// delete
		if (permissions['canDeleteLink'] && (permissions['canDeleteLinkCompletely'] || ENABLE_RECYCLE_BIN)) {
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.removeAll();';
			options[i]['text'] = language['wcf.global.button.delete'];
			i++;
		}
		
		// recover
		if (ENABLE_RECYCLE_BIN && permissions['canDeleteLinkCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'linkListLinkListEdit.recoverAll();';
			options[i]['text'] = language['wcf.linkList.category.links.recover'];
			i++;
		}
		
		// unmark all
		options[i] = new Object();
		options[i]['function'] = 'linkListLinkListEdit.unmarkAll();';
		options[i]['text'] = language['wcf.global.button.unmark'];
		i++;
		
		// show marked
		options[i] = new Object();
		options[i]['function'] = 'document.location.href = fixURL("index.php?page=LinkListModerationMarkedLinks'+SID_ARG_2ND+'")';
		options[i]['text'] = language['wcf.linkList.category.links.showMarkedLinks'];
		i++;
		
		return options;
	}
	
	/**
	 * Returns the title of the edit marked menu.
	 */
	this.getMarkedTitle = function() {
		return eval(language['wcf.linkList.category.markedLinks']);
	}
	
	/**
	 * Sticks this link.
	 */
	this.stick = function(id) {
		var link = this.data.get(id);
		if (link.isSticky == 0) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=stick&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isSticky = 1;
			this.showStatus(id);
		}
	}
	
	/**
	 * Unsticks this link.
	 */
	this.unstick = function(id) {
		var link = this.data.get(id);
		if (link.isSticky == 1) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=unstick&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isSticky = 0;
			this.showStatus(id);
		}
	}
	
	/**
	 * Deletes a link.
	 */
	this.remove = function(id) {
		var link = this.data.get(id);
		if (link.isDeleted == 0 && ENABLE_RECYCLE_BIN) {
			if (confirm(language['wcf.linkList.category.delete.sure'])) {
				if (permissions['canDeleteLink']) {
					var ajaxRequest = new AjaxRequest();
					ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=trash&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
					link.isDeleted = 1;
					this.showStatus(id);
					$('linkListLinkMessages' + id).insert(language['wcf.linkList.category.links.isDeleted']);
					if (link.isDisabled) {
						$('linkListLinkMessages' + id).down('.disableNote').remove();
					}
				}
				else {
					document.location.href = fixURL('index.php?page=LinkListLinkAction&action=trash&linkID='+id+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
				}
			}
		}
		else {
			if (confirm((link.isDeleted == 0 ? language['wcf.linkList.category.delete.sure'] : language['wcf.linkList.category.links.deleteCompletely.sure']))) {
				document.location.href = fixURL('index.php?page=LinkListLinkAction&action=delete&linkID='+id+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
	}
	
	/**
	 * Deletes the marked links.
	 */
	this.removeAll = function() {
		if (ENABLE_RECYCLE_BIN) {
			if (confirm(language['wcf.linkList.category.links.deleteMarked.sure'])) {
				document.location.href = fixURL('index.php?page=LinkListLinkAction&action=deleteAll&categoryID='+categoryID+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
		else if (confirm(language['wcf.linkList.category.links.deleteMarked.sure'])) {
			document.location.href = fixURL('index.php?page=LinkListLinkAction&action=deleteAll&categoryID='+categoryID+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
		}
	}
	
	/**
	 * Moves links.
	 */
	this.move = function(action) {
		document.location.href = fixURL('index.php?page=LinkListLinkAction&action='+action+'&categoryID='+categoryID+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	}
	
	/**
	 * Ummarked all marked links
	 */
	this.unmarkAll = function() {
		var ajaxRequest = new AjaxRequest();
		ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=unmarkAll&t='+SECURITY_TOKEN+SID_ARG_2ND);

		// checkboxes
		this.count = 0;
		var linkListLinkIDArray = this.data.keys();
		for (var i = 0; i < linkListLinkIDArray.length; i++) {
			var id = linkListLinkIDArray[i];
			var linkListLink = this.data.get(id);
		
			linkListLink.isMarked = 0;
			var checkbox = document.getElementById('linkListLinkMark' + id);
			if (checkbox) {
				checkbox.checked = false;
			}
			
			this.showStatus(id);
		}
		
		// mark all checkbox
		this.parentObject.checkMarkAll(false);
		
		// edit marked menu
		this.parentObject.showMarked();
	}
	
	/**
	 * Recovers a link.
	 */
	this.recover = function(id) {
		var link = this.data.get(id);
		if (link.isDeleted == 1) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=recover&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isDeleted = 0;
			this.showStatus(id);
			$('linkListLinkMessages' + id).down('.linkListLinkDeleteNote').remove();
		}
	}
	
	/**
	 * Recovers the marked links.
	 */
	this.recoverAll = function() {
		document.location.href = fixURL('index.php?page=LinkListLinkAction&action=recoverAll&categoryID='+categoryID+'&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	}
	
	/**
	 * Enables a link.
	 */
	this.enable = function(id) {
		var link = this.data.get(id);
		if (link.isDisabled == 1) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=enable&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isDisabled = 0;
			this.showStatus(id);
			$('linkListLinkMessages' + id).down('.disableNote').remove();
		}
	}
	
	/**
	 * Disables a link.
	 */
	this.disable = function(id) {
		var link = this.data.get(id);
		if (link.isDisabled == 0 && link.isDeleted == 0) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=disable&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isDisabled = 1;
			this.showStatus(id);
			$('linkListLinkMessages' + id).insert(language['wcf.linkList.link.isDisabled']);
		}
	}
	
	/**
	 * Opens a link.
	 */
	this.open = function(id) {
		var link = this.data.get(id);
		if (link.isClosed == 1) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=open&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isClosed = 0;
			this.showStatus(id);
		}
	}
	
	/**
	 * Closes a link.
	 */
	this.close = function(id) {
		var link = this.data.get(id);
		if (link.isClosed == 0) {
			var ajaxRequest = new AjaxRequest();
			ajaxRequest.openGet('index.php?page=LinkListLinkAction&action=close&linkID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			link.isClosed = 1;
			this.showStatus(id);
		}
	}
	
	/**
	 * Closes the marked links.
	 */
	this.closeAll = function() {
		document.location.href = fixURL('index.php?page=LinkListLinkAction&categoryID='+categoryID+'&action=closeAll&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	}
	
	/**
	 * Opens the marked links.
	 */
	this.openAll = function() {
		document.location.href = fixURL('index.php?page=LinkListLinkAction&categoryID='+categoryID+'&action=openAll&url='+encodeURIComponent(url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	}
	
	/**
	 * Show the status of a link.
	 */
	this.showStatus = function(id) {
		var linkListLink = this.data.get(id);
		
		// get row
		var row = document.getElementById('linkListLinkRow'+id);
		
		// update css class
		if (row) {
			// remove all classes
			row.removeClassName('marked');			
			row.removeClassName('disabled');
			row.removeClassName('deleted');
			
			// disabled
			if (linkListLink.isDisabled) {
				row.addClassName('disabled');
			}
			
			// deleted
			if (linkListLink.isDeleted) {
				row.addClassName('deleted');
			}
			
			// marked
			if (linkListLink.isMarked) {
				row.addClassName('marked');
			}
		}
		
		// update icon
		var icon = document.getElementById('linkListLinkEdit'+id);
		if (icon && icon.src != undefined) {
			// deleted
			if (linkListLink.isDeleted) {
				icon.src = linkListLink.icon.replace(/[a-z0-9-_]*?(?=(?:Options)?(?:S|M|L|XL)\.png$)/i, 'linkListLinkTrash');
			}
			else {
				linkListLink.icon = linkListLink.icon.replace(/linkListLinkTrash/i, 'linkListLink');
				
				// closed
				if (linkListLink.isClosed) {
					icon.src = linkListLink.icon.replace(/(?:Closed)?(?=(?:Options)?(?:S|M|L|XL)\.png$)/, 'Closed');
				}
				else {
					icon.src = linkListLink.icon.replace(/Closed(?=(?:Closed)?(?:S|M|L|XL)\.png$)/, '');
				}
				
				// sticky/important
				if (linkListLink.isSticky) {
					icon.src = linkListLink.icon.replace(/(?:Sticky)?(?=(?:Options)?(?:S|M|L|XL)\.png$)/, 'Sticky');
				}
				else {
					icon.src = linkListLink.icon.replace(/Sticky(?=(?:Sticky)?(?:S|M|L|XL)\.png$)/, '');
				}
			}
		}
	}
	
	/**
	 * Initialises special link options.
	 */
	this.initItem = function(id) {
		var link = this.data.get(id);
		// init subject edit
		if (permissions['canEditLink']) {
			var linkListLinkSubjectDiv = document.getElementById('linkListLinkSubject'+id);
			if (linkListLinkSubjectDiv) {
				linkListLinkSubjectDiv.name = id;
				linkListLinkSubjectDiv.ondblclick = function(event) { 
					if (!event) event = window.event;
					var target;
					if (event.target) target = event.target;
					else if (event.srcElement) target = event.srcElement;
					if (target.nodeType == 3) {// defeat Safari bug
						target = target.parentNode;
					}
					linkListLinkListEdit.startTitleEdit(this.name); 
				}
			}
		}
	}
	
	/**
	 * Starts the editing of a link title.
	 */
	this.startTitleEdit = function(id) {
		if ($('linkListLinkSubjectInput'+id)) return;
		var linkSubjectDiv = $('linkListLinkSubject'+id);
		if (linkSubjectDiv) {
			// get value and hide title
			var value = '';
			var title = linkSubjectDiv.select('a')[0];
			if (title) {
				title.addClassName('hidden');

				// IE, Opera, Safari, Konqueror
				if (title.innerText) {
					value = title.innerText;
				}
				// Firefox
				else {
					value = title.innerHTML.unescapeHTML();
				}
			}
		
			// show input field
			var inputField = new Element('input', {
				'id': 'linkListLinkSubjectInput'+id,
				'type': 'text',
				'className': 'inputText',
				'style': ('width: 300px;'),
				'value': value
			});
			linkSubjectDiv.insert(inputField);
			
			// add event listeners
			inputField.onkeydown = function(name, e) { this.doTitleEdit(name, e); }.bind(this, id);
			inputField.onblur = function(name) { this.abortTitleEdit(name); }.bind(this, id);
			
			// set focus
			inputField.focus();
		}
	}
	
	/**
	 * Aborts the editing of a link title.
	 */
	this.abortTitleEdit = function(id) {
		// remove input field
		var linkSubjectInputDiv = $('linkListLinkSubjectInput'+id);
		if (linkSubjectInputDiv) {
			linkSubjectInputDiv.remove();
		}
		
		// show title
		var linkSubjectDiv = $('linkListLinkSubject'+id);
		if (linkSubjectDiv) {
			// show first child
			var title = linkSubjectDiv.select('a')[0];
			if (title) {
				title.removeClassName('hidden');
			}
		}
	}
	
	/**
	 * Takes the value of the input-field and creates an ajax-request to save the new subject.
	 * enter = save
	 * esc = abort
	 */
	this.doTitleEdit = function(id, e) {
		if (!e) e = window.event;
		
		// get key code
		var keyCode = 0;
		if (e.which) keyCode = e.which;
		else if (e.keyCode) keyCode = e.keyCode;
	
		// get input field
		var inputField = $('linkListLinkSubjectInput'+id);
		
		// enter
		if (keyCode == '13' && inputField.value != '') {
			// set new value
			inputField.value = inputField.getValue().strip();
			var linkSubjectDiv = $('linkListLinkSubject'+id);
			var title = linkSubjectDiv.select('a')[0];
			if (title) {
				title.update(inputField.getValue().escapeHTML());
			}
			
			// save new value
			new Ajax.Request('index.php?page=LinkListLinkAction&linkID='+id+'&action=changeSubject&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				parameters: {
					subject: inputField.getValue()
				}
			});
			
			// abort editing
			inputField.blur();
			return false;
		}
		// esc
		else if (keyCode == '27') {
			inputField.blur();
			return false;
		}
	}
	
	
	this.parentObject = new InlineListEdit('linkListLink', this);
}