{include file="documentHeader"}
<head>
	<title>{$link->subject} - {lang}{$category->title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
	
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH};
		//]]>
	</script>
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/LinkListLinkListEdit.class.js"></script>
	{if $this->user->getPermission('mod.linkList.canEditLink')}
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
		
			date = new Date();
			hour = date.getHours();
			minute = date.getMinutes();
			currentTime = hour+':'+minute;
	
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
			language['wcf.global.button.submit']			= '{lang}wcf.global.button.submit{/lang}';
			language['wcf.global.button.reset']			= '{lang}wcf.global.button.reset{/lang}';
			language['wcf.linkList.category.links.isDeleted']		= '<p class="linkListLinkDeleteNote error">{lang}wcf.linkList.category.links.isDeleted{/lang}</p>';
			language['wcf.linkList.link.isDisabled'] 	= '<p class="disableNote info">{lang}wcf.linkList.link.isDisabled{/lang}</p>';
	
			// permissions
			var permissions = new Object();
			permissions['canEditLink'] = {if $this->user->getPermission('mod.linkList.canEditLink')}1{else}0{/if};
			permissions['canPinLink'] = {if $this->user->getPermission('mod.linkList.canPinLink')}1{else}0{/if};
			permissions['canCloseLink'] = {if $this->user->getPermission('mod.linkList.canCloseLink')}1{else}0{/if};
			permissions['canDeleteLink'] = {if $this->user->getPermission('mod.linkList.canDeleteLink')}1{else}0{/if};
			permissions['canDeleteLinkCompletely'] = {if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}1{else}0{/if};
			permissions['canEnableLink'] = {if $this->user->getPermission('mod.linkList.canEnableLink')}1{else}0{/if};
	
			onloadEvents.push(function() { linkListLinkListEdit = new LinkListLinkListEdit(linkListLinkData, {@$markedLinks}); });
			//]]>
		</script>
	{/if}
	
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
			<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{if $parentCategory->image}{@$parentCategory->image}{else}{@$parentCategory->getIconName()}{/if}S.png{/icon}" alt="" /> <span>{lang}{$parentCategory->title}{/lang}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}"><img src="{icon}{if $category->image}{@$category->image}{else}{@$category->getIconName()}{/if}S.png{/icon}" alt="" /> <span>{lang}{$category->title}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img id="linkListLinkEdit{@$link->linkID}" src="{icon}{@$link->getIconName()}L.png{/icon}" alt="" />
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

	<div class="border tabMenuContent">
		<div class="layout-2">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
					
						<div class="contentBox">
							<h3 class="subHeadline">{$link->subject}</h3>
							
							<div class="messageBody">
								{if $additionalMessageBodyContents|isset}{@$additionalMessageBodyContents}{/if}
								<div id="linkListLinkText{@$linkID}">
									{@$link->getFormattedMessage()}
								</div>
								<br />
							</div>
							
							{if $category->getPermission('canVisitLink')}
								<div class="buttonBar">
									<div style="font-size: 4pt"><br /></div>
									<h3 style="text-align:right; font-size: 14pt">
										&raquo; <a href="index.php?page=LinkListLinkVisit&amp;linkID={@$linkID}{@SID_ARG_2ND}">{lang}wcf.linkList.link.visit.value{/lang}</a>
									</h3>
								</div>
							{/if}
							
							{include file='attachmentsShow' messageID=$link->linkID author=$link->getAuthor()}
							
							<div class="buttonBar">
								<div class="smallButtons">
									<ul id="linkListLinkButtons{@$link->linkID}">
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if $link->isEditable($link->category)}
											<li><a href="index.php?form=LinkListLinkEdit&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>
										{/if}
										{if $link->isDeletable($link->category)}
											<li><a href="index.php?page=LinkListLinkAction&amp;linkID={@$linkID}&amp;action=trash&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wcf.linkList.category.deleteSure{/lang}')" title="{lang}wcf.linkList.link.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.delete{/lang}</span></a></li>
										{/if}										
										{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
									</ul>
								</div>
							</div>
							
							{if LINKLIST_LINK_SHOW_FACEBOOK_LIKEBUTTON}
								<div style="float:left; padding:2px 7px 0 0; width:460px">
									<script type="text/javascript">
										document.write('<iframe src="http://www.facebook.de/plugins/like.php?href='+encodeURIComponent(location.href)+'&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=26" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:30px;" allowTransparency="true"></iframe>');
									</script>
								</div>
							{/if}
							
							{if $additionalFirstColumnContent|isset}{@$additionalFirstColumnContent}{/if}
							<hr />
						</div>
					</div>
					
				</div>
				{if LINKLIST_LINK_SHOW_SIDEBAR}
					<div class="container-3 column second">
						<div class="columnInner">
							{include file='linkListLinkSidebar'}
							{if LINKLIST_LINK_SHOW_LASTVISITORS}
								{include file='linkListLinkLastVisitors'}
							{/if}
						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>
	{if $additionalLinkFooterContent|isset}{@$additionalLinkFooterContent}{/if}
</div>
{include file='footer' sandbox=false}
</body>
</html>
