{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.link{@$action|ucfirst}.pageTitle{/lang} - {lang}{$category->title}{/lang} - {lang}wcf.linkList.index{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wcf.linkList.link.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="types[]" value="linkListLink" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkList{@SID_ARG_2ND}"><img src="{icon}linkListS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.index{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$parentCategory->title}{/lang}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$category->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo; </li>
		{if $linkID}
			<li><a href="index.php?page=LinkListLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}linkListLinkS.png{/icon}" alt="" /> <span>{lang}{$link->subject}{/lang}</span></a> &raquo;</li>
		{/if}
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}linkListLinkAddL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.linkList.link{@$action|ucfirst}.pageTitle{/lang}</h2>
			{if $linkID}<p>{$link->subject}</p>{/if}
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	{if $preview|isset}
		<div class="border messagePreview">
			<div class="containerHead">
				<h3>{lang}wcf.message.preview{/lang}</h3>
			</div>
			<div class="message content">
				<div class="messageInner container-1">
					{if $subject}
						<h4>{$subject}</h4>
					{/if}
					<div class="messageBody">
						<div>{@$preview}</div>
					</div>
				</div>
			</div>
		</div>
	{/if}
	
	<form enctype="multipart/form-data" method="post" action="index.php?form=LinkListLink{@$action|ucfirst}{if $action == 'add'}&amp;categoryID={@$category->categoryID}{else}&amp;linkID={@$linkID}{/if}">
		<div class="border content">
			<div class="container-1">
	
				{if $this->user->getPermission('mod.linkList.canPinLink')}
					<fieldset>
						<legend>{lang}wcf.linkList.linkAdd.isSticky{/lang}</legend>
						
						<div class="formGroup">
							<div class="formGroupLabel">{lang}wcf.linkList.linkAdd.isSticky.status{/lang}</div>
							<div class="formGroupField">
								<fieldset>
									<legend>{lang}wcf.linkList.linkAdd.isSticky{/lang}</legend>
									<div class="formField">
										<ul class="formOptions">
											<li><label><input type="radio" name="isSticky" value="0" {if $isSticky == 0}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> <img src="{icon}linkListLinkM.png{/icon}" alt="" /> {lang}wcf.linkList.linkAdd.isSticky.0{/lang}</label></li>
											<li><label><input type="radio" name="isSticky" value="1" {if $isSticky == 1}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> <img src="{icon}linkListLinkStickyM.png{/icon}" alt="" /> {lang}wcf.linkList.linkAdd.isSticky.1{/lang}</label></li>
										</ul>
									</div>
								</fieldset>
							</div>
						</div>
					</fieldset>
				{/if}
					
				<fieldset>
					<legend>{lang}wcf.linkList.linkAdd.generalData{/lang}</legend>
					
					{if !$this->user->userID}
						<div class="formElement{if $errorField == 'username'} formError{/if}">
							<div class="formFieldLabel">
								<label for="username">{lang}wcf.user.username{/lang}</label>
							</div>
							<div class="formField">
								<input type="text" class="inputText" name="username" id="username" value="{$username}" tabindex="{counter name='tabindex'}" />
								{if $errorField == 'username'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
										{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
					{/if}
					
					<div class="formElement{if $errorField == 'subject'} formError{/if}">
						<div class="formFieldLabel">
							<label for="subject">{lang}wcf.linkList.linkAdd.subject{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="subject" id="subject" value="{$subject}" tabindex="{counter name='tabindex'}" />
							{if $errorField == 'subject'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
					
					<div class="formElement{if $errorField == 'url'} formError{/if}">
						<div class="formFieldLabel">
							<label for="url">{lang}wcf.linkList.linkAdd.url{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="url" id="url" value="{if $url != ''}{$url}{else}http://{/if}" tabindex="{counter name='tabindex'}" />
							{if $errorField == 'url'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}wcf.linkList.linkAdd.url.description{/lang}</p>
						</div>
					</div>
					
					<div id="descriptionDiv" class="formElement">
						<div class="formFieldLabel">
							<label for="shortDescription">{lang}wcf.linkList.linkAdd.shortDescription{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="shortDescription" name="shortDescription" cols="15" rows="3" tabindex="{counter name='tabindex'}">{$shortDescription}</textarea>
						</div>
						<div class="formFieldDesc">
							<p>{lang}wcf.linkList.linkAdd.shortDescription.description{/lang}</p>
						</div>
					</div>
					
					{if MODULE_TAGGING}
						{include file='tagAddBit'}
					{/if}
					
					{if $additionalGeneralDataFields|isset}{@$additionalGeneralDataFields}{/if}
				</fieldset>
				
				<fieldset>
					<legend>{lang}wcf.linkList.linkAdd.text{/lang}</legend>
					
					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
	
						<div class="formFieldLabel">
							<label for="text">{lang}wcf.linkList.linkAdd.text{/lang}</label>
						</div>
						
						<div class="formField">				
							<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
							{if $errorField == 'text'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
									{if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if}
								</p>
							{/if}
						</div>
						
					</div>

					{include file='messageFormTabs'}
					
				</fieldset>
				
				{include file='captcha'}
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
			<input type="hidden" name="idHash" value="{$idHash}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>