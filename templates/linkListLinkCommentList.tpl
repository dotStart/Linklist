{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.link.commentList{/lang} - {$link->subject} - {lang}{$category->title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	
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

	{include file='linkListLinkMenu' activeTabMenuItem='wcf.linkList.link.menu.comments' activeSubTabMenuItem='wcf.linkList.link.menu.comments'}
	
	<div class="border tabMenuContent">
		<div class="layout-2">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
					
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wcf.linkList.link.commentList{/lang} <span>({@$items})</span></h3>
			
							<div class="contentHeader">
								{pages print=true assign=pagesOutput link="index.php?page=LinkListLinkCommentList&linkID=$linkID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
				
								<div class="largeButtons">
									<ul>
										{if $category->getPermission('canAddComment') && (!$link->isClosed ||$this->user->getPermission('mod.linkList.canEditComment'))}
											<li><a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.add{/lang}"><img src="{icon}messageAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.comment.add{/lang}</span></a></li>
										{/if}
										{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
									</ul>
								</div>
							</div>
							
							{if $items == 0}
								<div class="border tabMenuContent">
									<div class="container-2">
										<p>{lang}wcf.linklist.link.commentList.noComments{/lang}</p>
									</div>
								</div>
							{/if}
			
							{* build message css classes *}
							{if $this->getStyle()->getVariable('messages.color.cycle')}
								{cycle name=messageCycle values='2,1' print=false}
							{else}
								{cycle name=messageCycle values='1' print=false}
							{/if}

							{if $this->getStyle()->getVariable('messages.sidebar.color.cycle')}
								{if $this->getStyle()->getVariable('messages.color.cycle')}
									{cycle name=postCycle values='1,2' print=false}
								{else}
									{cycle name=postCycle values='3,2' print=false}
								{/if}
							{else}
								{cycle name=postCycle values='3' print=false}
							{/if}
	
							{capture assign='messageClass'}message{if $this->getStyle()->getVariable('messages.framed')}Framed{/if}{@$this->getStyle()->getVariable('messages.sidebar.alignment')|ucfirst}{if $this->getStyle()->getVariable('messages.sidebar.divider.use')} dividers{/if}{/capture}
							{capture assign='messageFooterClass'}messageFooter{@$this->getStyle()->getVariable('messages.footer.alignment')|ucfirst}{/capture}

							{assign var='messageNumber' value=$items-$startIndex+1}
							{foreach from=$comments item=comment}
								{assign var="sidebar" value=$sidebarFactory->get('linkListLinkComment', $comment->commentID)}
								{assign var="author" value=$sidebar->getUser()}
								{assign var="commentID" value=$comment->commentID}
								
								<div id="linkListLinkCommentRow{@$commentID}" class="deletable message">
									<div class="messageInner {@$messageClass} container-{cycle name=postCycle}{if !$sidebar->getUser()->userID} guestPost{/if}">
										<a id="comment{@$commentID}"></a>
					
										{include file='messageSidebar'}
					
										<div class="messageContent">
											<div class="messageContentInner color-{cycle name=messageCycle}">
												<div class="messageHeader">
													<p class="messageCount">
														<a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$linkID}&amp;commentID={@$commentID}{@SID_ARG_2ND}#comment{@$commentID}" class="messageNumber">{#$messageNumber}</a>
													</p>
													<div class="containerIcon">
														<img src="{icon}messageM.png{/icon}" alt="" />
													</div>
													<div class="containerContent">
														<p class="smallFont light">{@$comment->time|time} {if $comment->lastChangeTime != 0}({lang}wcf.linkList.link.commentList.lastChangeTime{/lang}: {@$comment->lastChangeTime|time}){/if}</p>
													</div>
												</div>
							
												<div class="messageBody">
													<div id="linkListLinkCommentText{@$commentID}">
														{@$comment->getFormattedMessage()}
													</div>
												</div>
							
												{if MODULE_USER_SIGNATURE == 1 && $comment->getSignature()}
													<div class="signature">
														{@$comment->getSignature()}
													</div>
												{/if}

												<div class="{@$messageFooterClass}">
													<div class="smallButtons">
														<ul id="linkListLinkCommentButtons{@$comment->commentID}">
															<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
															{if $link->isClosed == 0 || $this->user->getPermission('mod.linkList.canEditComment')}
																{if $comment->isEditable($category, $link)}
																	<li><a href="index.php?form=LinkListLinkCommentEdit&amp;commentID={@$commentID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>
																{/if}
										
																{if $comment->isDeletable($category, $link)}
																	<li><a href="index.php?action=LinkListLinkCommentDelete&amp;commentID={@$commentID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.linkList.link.comment.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" longdesc="{lang}wcf.linkList.link.comment.delete.sure{/lang}" /> <span>{lang}wcf.global.button.delete{/lang}</span></a></li>
																{/if}
																{if $this->user->getPermission('mod.linkList.canEditComment') && $comment->ipAddress}<li><a href="javascript: void(0)" onclick="openList('linkListLinkComment{@$comment->commentID}IPAddress', false)" title="{lang}wcf.linkList.link.commentList.show.ipAddress{/lang}"><img src="{icon}ipAddressS.png{/icon}" alt="" /><span>{lang}wcf.linkList.link.ipAddress{/lang}</span></a></li>{/if}
															{/if}
															{if $additionalSmallButtons.$commentID|isset}{@$additionalSmallButtons.$commentID}{/if}
														</ul>
													</div>
												</div>
												{if $comment->ipAddress && $this->user->getPermission('mod.linkList.canEditComment')}
													<div id="linkListLinkComment{@$comment->commentID}IPAddress" class="signature"><em class="smallFont"><strong>{lang}wcf.linkList.link.commentList.ipAddressBy{/lang}</strong> <span>{@$comment->ipAddress}</span></em></div>
												{/if}
											<hr />
											</div>
										</div>
									</div>
								</div>
								<script type="text/javascript">
									//<![CDATA[
									initList('linkListLinkComment{@$comment->commentID}IPAddress', 0);
									//]]>
								</script>
							{assign var='messageNumber' value=$messageNumber-1}
							{/foreach}
							
							<div class="contentFooter">
								{@$pagesOutput}
					
								<div class="largeButtons">
									<ul>
										{if $category->getPermission('canAddComment') && (!$link->isClosed ||$this->user->getPermission('mod.linkList.canEditComment'))}<li><a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.add{/lang}"><img src="{icon}messageAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.comment.add{/lang}</span></a></li>{/if}
										{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				{if LINKLIST_COMMENTLIST_SHOW_SIDEBAR}
					<div class="container-3 column second">
						<div class="columnInner">
					
							{include file='linkListLinkSidebar'}

						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>

{include file='footer' sandbox=false}
</body>
</html>
