{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.link.comment.{@$action}{/lang} - {$link->subject} - {lang}{$category->title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
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
{include file='header' sandbox=false}

<div id="main">
	
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkList{@SID_ARG_2ND}"><img src="{icon}linkListS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.index{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{if $parentCategory->image}{@$parentCategory->image}{else}{@$parentCategory->getIconName()}{/if}S.png{/icon}" alt="" /> <span>{lang}{$parentCategory->title}{/lang}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{if $category->image}{@$category->image}{else}{@$category->getIconName()}{/if}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkListLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}{@$link->getIconName()}S.png{/icon}" alt="" /> <span>{$link->subject}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}linkListLinkL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2 id="linkListLinkSubject{@$link->linkID}">
				<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a>
			</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	{include file='linkListLinkMessages'}

	{include file='linkListLinkMenu' activeTabMenuItem='wcf.linkList.link.menu.comments' activeSubTabMenuItem='wcf.linkList.link.menu.comment.add'}
	
	<div class="border tabMenuContent">
		<div class="container-1">
			<div class="contentBox">
				<h3 class="subHeadline">{lang}wcf.linkList.link.comment.{@$action}{/lang}</h3>
	
				<form method="post" action="index.php?form=LinkListLinkComment{@$action|ucfirst}{if $action == 'add'}&amp;linkID={@$linkID}{elseif $action == 'edit'}&amp;commentID={@$commentID}{/if}">
				
					<div class="contentHeader">
						<div class="largeButtons">
							<ul>
								<li><a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$linkID}{@SID_ARG_2ND}#profileContent" title="{lang}wcf.linkList.link.commentList{/lang}"><img src="{icon}messageM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.commentList{/lang}</span></a></li>
								{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
							</ul>
						</div>
					</div>
				
					{if $preview|isset}
						<div class="message content">
							<div class="messageInner container-1">
								<div class="messageHeader">
									<h4>{lang}wcf.message.preview{/lang}</h4>
								</div>
								<div class="messageBody">
									<div>{@$preview}</div>
								</div>
							</div>
						</div>
					{/if}
				
					{if !$this->user->userID}
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
						
							{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
						</fieldset>
					{/if}
							
					<fieldset>
						<legend>{lang}wcf.linkList.link.comment.add.message{/lang}</legend>
					
						<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
							<div class="formFieldLabel">
								<label for="text">{lang}wcf.linkList.link.comment.add.message{/lang}</label>
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
							
					<div class="formSubmit">
						<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
						<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
						<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
						{@SID_INPUT_TAG}
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{include file='footer' sandbox=false}
</body>
</html>