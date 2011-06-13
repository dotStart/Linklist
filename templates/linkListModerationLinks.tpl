{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.moderation.{@$action}{/lang} - {lang}wcf.linkList.moderation{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/LinkListLinkListEdit.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var linkData = new Hash();
		
		// url
		var url = '{@$url}';
	
		// constants
		var ENABLE_RECYCLE_BIN = {@LINKLIST_LINK_ENABLE_RECYCLE_BIN};
	
		// ids
		var categoryID = {if $category|isset}{@$category->categoryID}{else}0{/if};

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
		language['wcf.linkList.category.links.isDeleted']		= '<p class="linkListLinkDeleteNote error">{lang}wcf.linkList.category.links.isDeleted{/lang}</p>';
		language['wcf.linkList.link.isDisabled'] 	= '<p class="disableNote info">{lang}wcf.linkList.link.isDisabled{/lang}</p>';
		
		// permissions
		var permissions = new Object();
		permissions['canEditLink'] = {if $this->user->getPermission('mod.linkList.canEditLink')}1{else}0{/if};
		permissions['canPinLink'] = {if $this->user->getPermission('mod.linkList.canPinLink')}1{else}0{/if};
		permissions['canCloseLink'] = {if $this->user->getPermission('mod.linkList.canCloseLink')}1{else}0{/if};
		permissions['canDeleteLink'] = {if $this->user->getPermission('mod.linkList.canDeleteLink')}1{else}0{/if};
		permissions['canDeleteLinkCompletely'] = {if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}1{else}0{/if};
		permissions['canEnableLink'] = {@$this->user->getPermission('mod.linkList.canEnableLink')};
	
		// init
		onloadEvents.push(function() {
			linkListLinkListEdit = new LinkListLinkListEdit(linkData, {@$markedLinks});
		});
		//]]>
	</script>	
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}
<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkList{@SID_ARG_2ND}"><img src="{icon}linkListS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.index{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=LinkListModeration{@SID_ARG_2ND}"><img src="{icon}linkListModerationS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.moderation.overview{/lang}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}linkListModerationL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.linkList.moderation{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{include file='linkListModerationMenu' activeTabMenuItem=$action}
	
	<div class="border tabMenuContent">
		<div class="container-1">
			<div class="contentBox">
				<h3 class="subHeadline">{lang}wcf.linkList.moderation.{@$action}.list{/lang} <span>({#$items})</span></h3>

				{if $items}
					<div class="contentHeader">
						{pages print=true assign=pagesOutput link=$url|concat:SID_ARG_2ND_NOT_ENCODED}
					</div>
	
					<div class="linkListLinkList">
						{cycle values='container-1,container-2' name='className' print=false advance=false}
						{assign var='messageNumber' value=$items-$startIndex+1}
						{foreach from=$links item=link}
							{assign var='linkID' value=$link->linkID}
							<div class="deletable message" id="linkListLinkRow{@$link->linkID}">
								<div class="messageInner {cycle name='className'}">
					
									<div class="messageHeader">
										<p class="messageCount">
											<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" class="messageNumber">{#$messageNumber}</a>
											<span class="messageMarkCheckBox">
												<label><input id="linkListLinkMark{@$link->linkID}" type="checkbox" /></label>
											</span>
										</p>
										<div class="containerIcon">
											{cycle name='className' print=false}
											<script type="text/javascript">
												//<![CDATA[
												linkData.set({@$link->linkID}, {
													'isMarked': {@$link->isMarked()},
													'isDeleted': {@$link->isDeleted},
													'isDisabled': {@$link->isDisabled},
													'isSticky': {@$link->isSticky},
													'isClosed': {@$link->isClosed},
													'className': '{cycle name="className"}'
												});
												//]]>
											</script>
											<img id="linkListLinkEdit{@$link->linkID}" src="{icon}{@$link->getIconName()}M.png{/icon}" alt="" />	
										</div>
										<div class="containerContent">
											<h3 id="linkListLinkSubject{@$link->linkID}" class="subject">
												<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a>
											</h3>
											<p class="light smallFont">{lang}wcf.linkList.link.by{/lang} {if $link->userID}<a href="index.php?page=User&amp;userID={@$link->getAuthor()->userID}{@SID_ARG_2ND}">{$link->getAuthor()->username}</a>{else}{$link->username}{/if} ({@$link->time|time})</p>
										</div>
									</div>
								
									<div class="messageBody">
										<div id="linkListLinkShortDescription{@$link->linkID}">
											{@$link->getFormattedShortDescription()}
										</div>
									</div>
										
									<div class="editNote smallFont light">
										<p>{lang}wcf.linkList.category.visits{/lang}: {#$link->visits}</p>
										<p>{lang}wcf.linkList.category.comments{/lang}: {#$link->comments}</p>
									</div>
									
									{if $additionalData.$linkID|isset}{@$additionalData.$linkID}{/if}
									
									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												<li><a href="index.php?form=LinkListLinkEdit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>
												{if $additionalSmallButtons.$linkID|isset}{@$additionalSmallButtons.$linkID}{/if}
											</ul>
										</div>
									</div>
								<hr />
								</div>
							</div>
						{assign var='messageNumber' value=$messageNumber-1}		
						{/foreach}
					</div>
					
					<div class="contentFooter">
						{@$pagesOutput}

						<div id="linkListLinkEditMarked" class="optionButtons"></div>
					</div>
				{else}
					<div class="border tabMenuContent">
						<div class="container-2">
							<p>{lang}wcf.linkList.moderation.{@$action}.noLinks{/lang}</p>
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