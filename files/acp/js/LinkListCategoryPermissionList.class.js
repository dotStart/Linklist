/** 
 * @author	Sebastian Oettl (edited by Christoph H.)
 * @copyright	2009-2010 WCF Solutions <http://www.wcfsolutions.com/index.php>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.chrihis.wcf.linkList
 * @category	WoltLab Community Framework (WCF)
 */
var LinkListCategoryPermissionList = Class.create({
	/**
	 * Inits LinkListCategoryPermissionList.
	 */
	initialize: function(key, data, settings) {
		this.key = key;
		this.data = data;
		this.settings = settings;
		this.selectedIndex = -1;
		this.inputHasFocus = false;
		
		// add input listener
		var input = $(this.key+'AddInput');
		if (input) {
			input.onfocus = function(onfocusEvent, e) { this.inputHasFocus = true; onfocusEvent(e); }.bind(this, input.onfocus);
			input.onblur = function(onblurEvent, e) { this.inputHasFocus = false; onblurEvent(e); }.bind(this, input.onblur);
			input.onkeyup = function(onkeyupEvent, event) {
				var result = onkeyupEvent(event);
				if (!event) event = window.event;
			
				// get key code
				var keyCode = 0;
				if (event.which) keyCode = event.which;
				else if (event.keyCode) keyCode = event.keyCode;
				
				// return
				if (keyCode == 13 && result) {
					this.addPermission();
				}
			}.bind(this, input.onkeyup);
		}

		// add button listener
		var button = $(this.key+'AddButton');
		if (button) {
			button.onclick = this.addPermission.bind(this);
		}
		
		// refresh permissions
		this.refreshPermissions();
	},
	
	/**
	 * Refreshes the permission list.
	 */
	refreshPermissions: function() {
		var dataDiv = $(this.key);
		if (dataDiv) {
			// remove old content
			dataDiv.update();
			
			// create list
			var indexArray = this.data.keys();
			if (indexArray.length > 0) {
				var ul = new Element('ul');
				dataDiv.appendChild(ul);
				for (var i = 0; i < indexArray.length; i++) {
					var index = indexArray[i];
					var permission = this.data.get(index);	
					
					// create li
					var li = new Element('li', {
						id: this.key+i,
						className: (i == this.selectedPermission ? 'selected' : '')
					});
					ul.appendChild(li);
					
					// create remove link
					var removeLink = new Element('a', {
						className: 'remove'
					});
					removeLink.onclick = function(name) { this.removePermission(parseInt(name)); }.bind(this, i);
					li.appendChild(removeLink);
					
					// create remove link image
					var removeImage = new Element('img', {
						src: RELATIVE_WCF_DIR+'icon/deleteS.png'
					});
					removeLink.appendChild(removeImage);
					
					// create a
					var a = new Element('a');
					a.onclick = function(name) { this.selectPermission(parseInt(name)); }.bind(this, i);
					li.appendChild(a);
					
					// create image
					var img = new Element('img', {
						src: RELATIVE_WCF_DIR+'icon/groupS.png'
					});
					a.appendChild(img);
					
					// create title
					var title = document.createTextNode(permission.name);
					a.appendChild(title);
				}
			}
		}
	},
	
	/**
	 * Selects the permission with the given index.
	 */
	selectPermission: function(index) {
		var permission = this.data.get(index);

		// disable selected item
		if (this.selectedIndex != -1) {
			var li = $(this.key+this.selectedIndex);
			if (li) li.removeClassName('selected');
		}
		
		// select item
		this.selectedIndex = index;
		if (this.selectedIndex == -1) {
			this.hideSettings();
		}
		else {
			$(this.key+this.selectedIndex).addClassName('selected');
			
			// update title
			var h3 = $(this.key+'SettingsTitle');
			if (h3) {
				// remove old title
				h3.update();
				
				// add new title
				var title = document.createTextNode(language['wcf.acp.linkList.category.permissions.permissionsFor'].replace(/\{\$name\}/, permission.name));
				h3.appendChild(title);
			}
			
			// refresh settings
			this.refreshSettings();
			
			// show settings
			this.showSettings();
		}
	},
	
	/**
	 * Adds a new permission to the list.
	 */
	addPermission: function() {
		var query = $(this.key+'AddInput').getValue().strip();
		if (query) {
			this.ajaxRequest = new AjaxRequest();
			new Ajax.Request('index.php?page=LinkListCategoryPermissionsObjects'+SID_ARG_2ND, {
				method: 'post',
				parameters: {
					'query': query
				},
				onSuccess: function(response) {
					var objects = response.responseXML.getElementsByTagName('objects');
					if (objects.length > 0) {
						var firstNewKey = -1;
						for (var i = 0; i < objects[0].childNodes.length; i++) {
							// get name
							var name = objects[0].childNodes[i].childNodes[0].childNodes[0].nodeValue;
							var type = objects[0].childNodes[i].childNodes[1].childNodes[0].nodeValue;
							var id = objects[0].childNodes[i].childNodes[2].childNodes[0].nodeValue;  
							
							var doBreak = false;
							for (var j = 0; j < this.data.keys().length; j++) {
								if (this.data.get(j).id == id && this.data.get(j).type == type) doBreak = true;
							}
							if (doBreak) continue;

							var key = this.data.keys().length;
							if (firstNewKey == -1) firstNewKey = key;
							var settings = new Hash();
							settings.set('fullControl', -1);
							for (var j = 0; j < this.settings.length; j++) {
								settings.set(this.settings[j], -1);
							}
							this.data.set(key, {
								'name': name,
								'type': 'group',
								'id': id,
								'settings': settings
							});
						}
						
						$(this.key+'AddInput').value = '';
						this.refreshPermissions();
						
						// select new permission
						if (firstNewKey != -1) {
							this.selectPermission(firstNewKey);
						}
					}
				}.bind(this)
			});
		}
	},
	
	/**
	 * Removes a permission from the list.
	 */
	removePermission: function(index) {
		this.data.unset(index);
		this.refreshPermissions();
		
		if (this.selectedIndex == index) this.selectPermission(-1);
	},
	
	/**
	 * Refreshes the settings.
	 */
	refreshSettings: function() {
		permission = this.data.get(this.selectedIndex);
		
		var settingsDiv = $(this.key+'Settings');
		if (settingsDiv) {
			// remove old content
			settingsDiv.update();
			
			// create ul
			var ul = document.createElement('ul');
			settingsDiv.appendChild(ul);
				
			var settingIndexes = permission.settings.keys();
			for (var i = 0; i < settingIndexes.length; i++) {
				var setting = settingIndexes[i];
				var settingValue = permission.settings.get(setting);

				// create li
				var li = new Element('li');
				ul.appendChild(li);
					
				// deny
				// label
				var labelDeny = new Element('label', {
					'className': 'deny'
				});
				li.appendChild(labelDeny);
				// checkbox
				var checkboxDeny = new Element('input', {
					'type': 'checkbox',
					'id': this.key+'Setting'+setting+'Deny',
					'name': setting
				});				
				checkboxDeny.onclick = function(name, checkbox) { this.denySetting(name, checkbox.checked); }.bind(this, setting, checkboxDeny);
				labelDeny.appendChild(checkboxDeny);
				if (settingValue == 0) checkboxDeny.checked = true;

				// allow
				// label
				var labelAllow = new Element('label', {
					'className': 'allow'
				});
				li.appendChild(labelAllow);	
				// checkbox
				var checkboxAllow = new Element('input', {
					'type': 'checkbox',
					'id': this.key+'Setting'+setting+'Allow',
					'name': setting
				});				
				checkboxAllow.onclick = function(name, checkbox) { this.allowSetting(name, checkbox.checked); }.bind(this, setting, checkboxAllow);
				labelAllow.appendChild(checkboxAllow);
				if (settingValue == 1) checkboxAllow.checked = true;

				// create span
				var span = new Element('span');
				span.onmouseup = function(name) { $(name).focus(); }.bind(this, this.key+'Setting'+setting+'Allow');
				li.appendChild(span);
				
				// title
				var title = document.createTextNode(language['wcf.acp.linkList.category.permissions.'+setting]);
				span.appendChild(title);
			}

			this.checkFullControl();
		}
	},
	
	/**
	 * Shows the settings.
	 */
	showSettings: function() {
		$(this.key+'Settings').parentNode.parentNode.show();
	},
	
	/**
	 * Hides the settings.
	 */
	hideSettings: function() {
		$(this.key+'Settings').parentNode.parentNode.hide();
	},
	
	/**
	 * Checks or unchecks an allow setting.
	 */
	allowSetting: function(setting, checked) {
		if (setting == 'fullControl') this.allowSettingFullControl(checked);
		else this.data.get(this.selectedIndex).settings.set(setting, (checked ? 1 : -1));
		
		this.refreshSettings();
	},
	
	/**
	 * Checks or unchecks all allow settings.
	 */
	allowSettingFullControl: function(checked) {
		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			this.data.get(this.selectedIndex).settings.set(setting, (checked ? 1 : -1));
		}
	},
	
	/**
	 * Checks or unchecks a deny setting.
	 */
	denySetting: function(setting, checked) {
		if (setting == 'fullControl') this.denySettingFullControl(checked);
		else this.data.get(this.selectedIndex).settings.set(setting, (checked ? 0 : -1));
		
		this.refreshSettings();
	},
	
	/**
	 * Checks or unchecks all deny settings.
	 */
	denySettingFullControl: function(checked) {
		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			this.data.get(this.selectedIndex).settings.set(setting, (checked ? 0 : -1));
		}
	},
	
	/**
	 * Checks or unchecks the allow and deny full control setting.
	 */
	checkFullControl: function() {
		var value = undefined;

		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			if (value == undefined) value = this.data.get(this.selectedIndex).settings.get(setting);
			else {
				if (value != this.data.get(this.selectedIndex).settings.get(setting)) {
					value = -1; break;
				}
			}
		}
		
		$(this.key+'SettingfullControlAllow').checked = (value == 1);
		$(this.key+'SettingfullControlDeny').checked = (value == 0);
	},
	
	/**
	 * Stores the values in hidden fields.
	 */
	submit: function(form) {
		var indexArray = this.data.keys();
		for (var i = 0; i < indexArray.length; i++) {
			var index = indexArray[i];
			var permission = this.data.get(index);	
				
			// type field
			var typeField = new Element('input', {
				'type': 'hidden',
				'name': this.key+'['+i+'][type]',
				'value': permission.type
			});
			form.appendChild(typeField);
				
			// id field
			var idField = new Element('input', {
				'type': 'hidden',
				'name': this.key+'['+i+'][id]',
				'value': permission.id
			});
			form.appendChild(idField);
				
			// name field
			var nameField = new Element('input', {
				'type': 'hidden',
				'name': this.key+'['+i+'][name]',
				'value': permission.name
			});
			form.appendChild(nameField);

			// settings
			var settingIndexArray = permission.settings.keys();
			for (var j = 0; j < settingIndexArray.length; j++) {
				var setting = settingIndexArray[j];
				if (setting == 'fullControl') continue;
				var settingField = new Element('input', {
					'type': 'hidden',
					'name': this.key+'['+i+'][settings]['+setting+']',
					'value': permission.settings.get(setting)
				});
				form.appendChild(settingField);
			}
		}
	}
});