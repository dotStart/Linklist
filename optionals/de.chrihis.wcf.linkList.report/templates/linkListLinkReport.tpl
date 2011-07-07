{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.link.report{/lang} - {$link->subject} - {lang}{$category->title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/LinkListEdit.class.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/linkList.css" />
	<script type="text/javascript">
		//<![CDATA[
		var linkListLinkData = new Hash();
		linkListLinkData.set({@$linkID}, {
			'isMarked': {@$link->isMarked()},
			'isSticky': {@$link->isSticky},
			'isDeleted': {@$link->isDeleted},
			'isDisabled': {@$link->isDisabled},
			'isClosed': {@$link->isClosed}
		});
		
		// url
		var url = 'index.php?page=LinkListLink&linkID={@$linkID}{@SID_ARG_2ND_NOT_ENCODED}';
		
		// this user
		var user = '<a href="index.php?page=User&userID={@$this->user->userID}{@SID_ARG_2ND_NOT_ENCODED}">{$this->user->username}</a>';
	
		// constants
		var ENABLE_RECYCLE_BIN = {@LINKLIST_LINK_ENABLE_RECYCLE_BIN};
	
		// language
		var language = new Object();
		language['wcf.global.button.mark']		= '{lang}wcf.global.button.mark{/lang}';
		language['wcf.global.button.unmark']		= '{lang}wcf.global.button.unmark{/lang}';
		language['wcf.global.button.delete']		= '{lang}wcf.global.button.delete{/lang}';
		language['wcf.global.button.deleteCompletely'] 		= '{lang}wcf.global.button.deleteCompletely{/lang}';
		language['wcf.global.button.edit']		= '{lang}wcf.global.button.edit{/lang}';
	
		language['wcf.linkList.category.links.recover'] 	= '{lang}wcf.linkList.category.links.recover{/lang}';
		language['wcf.linkList.category.links.enable']	= '{lang}wcf.linkList.category.links.enable{/lang}';
		language['wcf.linkList.category.links.disable'] 	= '{lang}wcf.linkList.category.links.disable{/lang}';
		language['wcf.linkList.category.links.open'] 	= '{lang}wcf.linkList.category.links.open{/lang}';
		language['wcf.linkList.category.links.close'] 	= '{lang}wcf.linkList.category.links.close{/lang}';
		language['wcf.linkList.category.links.editSubject'] 	= '{lang}wcf.linkList.category.links.editSubject{/lang}';
		language['wcf.linkList.category.markedLinks'] 	= '{lang}wcf.linkList.category.markedLinks{/lang}';
		language['wcf.linkList.category.delete.sure'] 	= '{lang}wcf.linkList.category.deleteSure{/lang}';
		language['wcf.linkList.category.links.deleteCompletely.sure'] 	= '{lang}wcf.linkList.category.links.deleteCompletely.sure{/lang}';
		language['wcf.linkList.category.links.deleteMarked.sure'] 	= '{lang}wcf.linkList.category.links.deleteMarked.sure{/lang}';
		language['wcf.linkList.category.links.move'] 	= '{lang}wcf.linkList.category.links.move{/lang}';
		language['wcf.linkList.category.links.stick'] 	= '{lang}wcf.linkList.category.links.stick{/lang}';
		language['wcf.linkList.category.links.unstick'] 		= '{lang}wcf.linkList.category.links.unstick{/lang}';
		language['wcf.linkList.category.links.showMarkedLinks'] 	= '{lang}wcf.linkList.category.links.showMarkedLinks{/lang}';
		language['wbb.board.threads.delete.reason'] 		= '{lang}wbb.board.threads.delete.reason{/lang}';
		language['wcf.linkList.category.links.deleteMarked.sure'] 	= '{lang}wcf.linkList.category.links.deleteMarked.sure{/lang}';
		language['wcf.global.button.submit']			= '{lang}wcf.global.button.submit{/lang}';
		language['wcf.global.button.reset']			= '{lang}wcf.global.button.reset{/lang}';
	
		// permissions
		var permissions = new Object();
		permissions['canEditLink'] = {if $this->user->getPermission('mod.linkList.canEditLink')}1{else}0{/if};
		permissions['canPinLink'] = {if $this->user->getPermission('mod.linkList.canPinLink')}1{else}0{/if};
		permissions['canCloseLink'] = {if $this->user->getPermission('mod.linkList.canCloseLink')}1{else}0{/if};
		permissions['canDeleteLink'] = {if $this->user->getPermission('mod.linkList.canDeleteLink')}1{else}0{/if};
		permissions['canDeleteLinkCompletely'] = {if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}1{else}0{/if};
		permissions['canEnableLink'] = {@$this->user->getPermission('mod.linkList.canEnableLink')};
	
		onloadEvents.push(function() { linkListEdit = new LinkListEdit(linkListLinkData, {@$markedLinks}); });
	
		//]]>
	</script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkList{@SID_ARG_2ND}"><img src="{icon}linkListS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.index{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$parentCategory->title}{/lang}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$category->getIconName()}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img id="linkListLinkEdit{@$link->linkID}" src="{icon}linkListLinkL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2 id="linkListLinkSubject{@$link->linkID}">
				<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a>
			</h2>
			<p>{@$link->getFormattedShortDescription()}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{include file='linkListLinkMessages'}

	{include file='linkListLinkMenu' activeTabMenuItem='wcf.linkList.link.menu.link'}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	<div class="border tabMenuContent">
		<div class="layout-2">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
					
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wcf.linkList.link.report{/lang}</h3>
							
							<form method="post" action="index.php?form=LinkListLinkReport&amp;linkID={@$linkID}">

								{if !$this->user->userID}
									<fieldset>
										<legend>{lang}wcf.linkList.link.report.generalData{/lang}</legend>
								
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
									</fieldset>
								{/if}
				
								<fieldset>
									<legend>{lang}wcf.linkList.link.report{/lang}</legend>
									<div class="formElement">
										<div class="formFieldLabel">
											<label for="text">{lang}wcf.linkList.link.report.label{/lang}</label>
										</div>	
						
										<div class="formField{if $errorField == 'text'} formError{/if}">
											<textarea id="text" name="text" rows="10" cols="20"></textarea>
											{if $errorField == 'text'}
												<p class="innerError">{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}</p>
											{/if}
										</div>
										<div class="formFieldDesc">
											<p>{lang}wcf.linkList.link.report.description{/lang}</p>
										</div>
									</div>
								</fieldset>
								
								{include file='captcha'}
				
								{if $additionalFields|isset}{@$additionalFields}{/if}
		
								<div class="formSubmit">
									<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
									<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
									{@SID_INPUT_TAG}
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<div class="container-3 column second">
					<div class="columnInner">
					
						{include file='linkListLinkSidebar'}

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{include file='footer' sandbox=false}
</body>
</html>