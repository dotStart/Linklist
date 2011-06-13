{include file='header'}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var tabMenu = new TabMenu();
	onloadEvents.push(function() { tabMenu.showSubTabMenu("{$activeTabMenuItem}") });
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/linkListCategory{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.linkList.category.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.acp.linkList.category.{@$action}.success{/lang}</p>	
{/if}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}acp/js/LinkListCategoryPermissionList.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var language = new Object();
	language['wcf.acp.linkList.category.permissions.permissionsFor'] = '{staticlang}wcf.acp.linkList.category.permissions.permissionsFor{/staticlang}';
	language['wcf.acp.linkList.category.permissions.fullControl'] = '{lang}wcf.acp.linkList.category.permissions.fullControl{/lang}';

	{foreach from=$permissionSettings item=permissionSetting}
		language['wcf.acp.linkList.category.permissions.{@$permissionSetting}'] = '{lang}wcf.acp.linkList.category.permissions.{@$permissionSetting}{/lang}';
	{/foreach}
	
	var permissions = new Hash();
	{assign var=i value=0}		
	{foreach from=$permissions item=permission}
		var settings = new Hash();
		settings.set('fullControl', -1);
		
		{foreach from=$permission.settings key=setting item=value}
			{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
				settings.set('{@$setting}', {@$value});
			{/if}
		{/foreach}
		
		permissions.set({@$i}, {
			'name': '{@$permission.name|encodeJS}',
			'type': '{@$permission.type}',
			'id': '{@$permission.id}',
			'settings': settings
		});

		{assign var=i value=$i+1}
	{/foreach}
	
	var permissionSettings = new Array({implode from=$permissionSettings item=permissionSetting}'{@$permissionSetting}'{/implode});
	
	// category type
	function setCategoryType(newType) {
		switch (newType) {
			case 0:
				showOptions('imageDiv', 'allowCommentsDiv');
				break;
			case 1:
				hideOptions('imageDiv', 'allowCommentsDiv');
				break;
		}
	}
	document.observe("dom:loaded", function() {
		setCategoryType({@$categoryType});
		
		// group permissions
		var permissionList = new LinkListCategoryPermissionList('permission', permissions, permissionSettings);
		
		// add onsubmit event
		$('categoryAddForm').onsubmit = function() { 
			if (suggestion.selectedIndex != -1) return false;
			if (permissionList.inputHasFocus) return false;
			permissionList.submit(this);
		};

	});
	//]]>
</script>

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=LinkListCategoryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.linkList.category.list{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/linkListCategoryM.png" alt="" /> <span>{lang}wcf.acp.linkList.category.list{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=LinkListCategory{@$action|ucfirst}{if $categoryID}&amp;categoryID={@$categoryID}{/if}" id="categoryAddForm">
	{if $categoryID && $categoryQuickJumpOptions|count > 1}
		<fieldset>
			<legend>{lang}wcf.acp.linkList.category.edit{/lang}</legend>
			<div class="formElement">
				<div class="formFieldLabel">
					<label for="categoryChange">{lang}wcf.acp.linkList.category.edit{/lang}</label>
				</div>
				<div class="formField">
					<select id="categoryChange" onchange="document.location.href=fixURL('index.php?form=LinkListCategoryEdit&amp;categoryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
						{htmloptions options=$categoryQuickJumpOptions selected=$categoryID disableEncoding=true}
					</select>
				</div>
			</div>
		</fieldset>
	{/if}
	
	<div class="tabMenu">
		<ul>
			<li id="data"><a onclick="tabMenu.showSubTabMenu('data');"><span>{lang}wcf.acp.linkList.category.data{/lang}</span></a></li>
			<li id="permissions"><a onclick="tabMenu.showSubTabMenu('permissions');"><span>{lang}wcf.acp.linkList.category.permissions{/lang}</span></a></li>
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"><div> </div></div>
	</div>
	
	<div class="border tabMenuContent hidden" id="data-content">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wcf.acp.linkList.category.data{/lang}</h3>
				
				<fieldset>
					<legend>{lang}wcf.acp.linkList.category.categoryType{/lang}</legend>
					<div class="formElement{if $errorField == 'categoryType'} formError{/if}">
						<ul class="formOptions">
							<li><label><input onclick="if (IS_SAFARI) setCategoryType(0)" onfocus="setCategoryType(0)" type="radio" name="categoryType" value="0" {if $categoryType == 0}checked="checked" {/if}/> {lang}wcf.acp.linkList.category.categoryType.0{/lang}</label></li>
							<li><label><input onclick="if (IS_SAFARI) setCategoryType(1)" onfocus="setCategoryType(1)" type="radio" name="categoryType" value="1" {if $categoryType == 1}checked="checked" {/if}/> {lang}wcf.acp.linkList.category.categoryType.1{/lang}</label></li>
						</ul>
						{if $errorField == 'categoryType'}
							<p class="innerError">
								{if $errorType == 'invalid'}{lang}wcf.acp.linkList.category.error.categoryType.invalid{/lang}{/if}
							</p>
						{/if}
					</div>
				</fieldset>
				
				<fieldset>
					<legend>{lang}wcf.acp.linkList.category.data.general{/lang}</legend>
					
					<div class="formElement{if $errorField == 'title'} formError{/if}">
						<div class="formFieldLabel">
							<label for="title">{lang}wcf.acp.linkList.category.title{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="title" name="title" value="{$title}" />
							{if $errorField == 'title'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
			
					<div id="descriptionDiv" class="formElement">
						<div class="formFieldLabel">
							<label for="description">{lang}wcf.acp.linkList.category.description{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="description" name="description" cols="40" rows="10">{$description}</textarea>
							<label><input type="checkbox" name="allowDescriptionHtml" value="1" {if $allowDescriptionHtml}checked="checked" {/if}/> {lang}wcf.acp.linkList.category.allowDescriptionHtml{/lang}</label>
						</div>
					</div>
					
					<div class="formElement" id="imageDiv">
						<div class="formFieldLabel">
							<label for="image">{lang}wcf.acp.linkList.category.image{/lang}</label>
						</div>
						<div class="formField">	
							<input type="text" class="inputText" id="image" name="image" value="{$image}" />
						</div>
						<div class="formFieldDesc hidden" id="imageHelpMessage">
							<p>{lang}wcf.acp.linkList.category.image.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">
						//<![CDATA[
						inlineHelp.register('image');
						//]]>
					</script>		
										
					<div class="formElement" id="allowCommentsDiv">
						<div class="formFieldLabel">{lang}{lang}wcf.acp.linkList.category.allowComments{/lang}{/lang}</div>
						<div class="formField">
							<ul class="formOptionsLong">
								<li><label><input type="radio" name="allowComments" value="-1"{if $allowComments == -1} checked="checked"{/if} /> {lang}wcf.acp.linkList.category.allowComments.standard{/lang}</label></li>
								<li><label><input type="radio" name="allowComments" value="0"{if $allowComments == 0} checked="checked"{/if} /> {lang}wcf.acp.linkList.category.allowComments.deny{/lang}</label></li>
								<li><label><input type="radio" name="allowComments" value="1"{if $allowComments == 1} checked="checked"{/if} /> {lang}wcf.acp.linkList.category.allowComments.allow{/lang}</label></li>
							</ul>
						</div>
					</div>

					
					{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
				</fieldset>
				
				<fieldset>
					<legend>{lang}wcf.acp.linkList.category.data.position{/lang}</legend>
					
					{if $categoryOptions|count > 0}
						<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
							<div class="formFieldLabel">
								<label for="parentID">{lang}wcf.acp.linkList.category.parentID{/lang}</label>
							</div>
							<div class="formField">
								<select name="parentID" id="parentID">
									<option value="0"></option>
									{htmlOptions options=$categoryOptions disableEncoding=true selected=$parentID}
								</select>
								{if $errorField == 'parentID'}
									<p class="innerError">
										{if $errorType == 'invalid'}{lang}wcf.acp.linkList.category.error.parentID.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
							<div class="formFieldDesc hidden" id="parentIDHelpMessage">
								<p>{lang}wcf.acp.linkList.category.parentID.description{/lang}</p>
							</div>
						</div>
						<script type="text/javascript">//<![CDATA[
							inlineHelp.register('parentID');
						//]]>
						</script>
					{/if}
			
					<div class="formElement{if $errorField == 'position'} formError{/if}" id="positionDiv">
						<div class="formFieldLabel">
							<label for="position">{lang}wcf.acp.linkList.category.position{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="position" name="position" value="{@$position}" />
							{if $errorField == 'position'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="positionHelpMessage">
							<p>{lang}wcf.acp.linkList.category.position.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('position');
					//]]>
					</script>
					
					{if $additionalPositionFields|isset}{@$additionalPositionFields}{/if}
				</fieldset>
				
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
	
		<div class="border tabMenuContent hidden" id="permissions-content">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wcf.acp.linkList.category.permissions{/lang}</h3>
		
				<div class="formElement">
					<div class="formFieldLabel" id="permissionTitle">
						{lang}wcf.acp.linkList.category.permissions.title{/lang}
					</div>
					<div class="formField"><div id="permission" class="accessRights"></div></div>
				</div>
				<div class="formElement">
					<div class="formField">	
						<input id="permissionAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
						<script type="text/javascript">
							//<![CDATA[
							suggestion.setSource('index.php?page=LinkListCategoryPermissionsObjectsSuggest{@SID_ARG_2ND_NOT_ENCODED}');
							suggestion.enableIcon(true);
							suggestion.init('permissionAddInput');
							//]]>
						</script>
						<input id="permissionAddButton" type="button" value="{lang}wcf.acp.linkList.category.permissions.add{/lang}" />
					</div>
				</div>
					
				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="permissionSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="permissionHeader" class="accessRightsHeader">
							<span class="deny">{lang}wcf.acp.linkList.category.permissions.deny{/lang}</span>
							<span class="allow">{lang}wcf.acp.linkList.category.permissions.allow{/lang}</span>
						</div>
						<div id="permissionSettings" class="accessRights"></div>
					</div>
				</div>
			</div>
				
		</div>
	
	{if $additionalTabContents|isset}{@$additionalTabContents}{/if}
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		<input type="hidden" id="activeTabMenuItem" name="activeTabMenuItem" value="{$activeTabMenuItem}" />
 	</div>
</form>

{include file='footer'}