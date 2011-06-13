{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.moderation{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
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
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}linkListModerationL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.linkList.moderation{/lang}</h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	<div id="profileContent" class="tabMenu">
		<ul>
			<li class="activeTabMenu"><a href="index.php?page=LinkListModeration{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.overview{/lang}</span></a></li>
			{if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}
				<li><a href="index.php?page=LinkListModerationDeletedLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.deletedLinks{/lang}</span></a></li>
			{/if}
			{if $this->user->getPermission('mod.linkList.canEnableLink')}
				<li><a href="index.php?page=LinkListModerationDisabledLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.disabledLinks{/lang}</span></a></li>
			{/if}
			{if $this->user->getPermission('mod.linkList.canEditLink')}
				<li><a href="index.php?page=LinkListModerationMarkedLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.markedLinks{/lang}</span></a></li>
			{/if}
			{if $additionalTabMenuItems|isset}{@$additionalTabMenuItems}{/if}
		</ul>
	</div>
	
	<div class="subTabMenu">
		<div class="containerHead">
			<div> </div>
		</div>
	</div>
	
	<div class="border tabMenuContent">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.linkList.moderation.overview{/lang}</h3>
			<div class="border">
				<ul class="dataList">
					{if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}
						<li class="{cycle values='container-1,container-2'}">
							<div class="containerIcon">
								<img src="{icon}moderationDeletedLinkListLinksM.png{/icon}" alt="" style="width: 24px;" />
							</div>
						
							<div class="containerContent">
								<h4><a href="index.php?page=LinkListModerationDeletedLinks{@SID_ARG_2ND}"{if $deletedLinks} class="new"{/if}>{lang}wcf.linkList.moderation.deletedLinks{/lang}{if $deletedLinks} ({#$deletedLinks}){/if}</a></h4>
							</div>
						</li>
					{/if}
				
					{if $this->user->getPermission('mod.linkList.canEnableLink')}
						<li class="{cycle values='container-1,container-2'}">
							<div class="containerIcon">
								<img src="{icon}moderationDisabledLinkListLinksM.png{/icon}" alt="" style="width: 24px;" />
							</div>
						
							<div class="containerContent">
								<h4><a href="index.php?page=LinkListModerationDisabledLinks{@SID_ARG_2ND}"{if $disabledLinks} class="new"{/if}>{lang}wcf.linkList.moderation.disabledLinks{/lang}{if $disabledLinks} ({#$disabledLinks}){/if}</a></h4>
							</div>
						</li>
					{/if}
				
					{if $this->user->getPermission('mod.linkList.canEditLink')}
						<li class="{cycle values='container-1,container-2'}">
							<div class="containerIcon">
								<img src="{icon}moderationMarkedLinkListLinksM.png{/icon}" alt="" style="width: 24px;" />
							</div>
						
							<div class="containerContent">
								<h4><a href="index.php?page=LinkListModerationMarkedLinks{@SID_ARG_2ND}"{if $markedLinks} class="new"{/if}>{lang}wcf.linkList.moderation.markedLinks{/lang}{if $markedLinks} ({#$markedLinks}){/if}</a></h4>
							</div>
						</li>
					{/if}
					{if $additionalModerationItems|isset}{@$additionalModerationItems}{/if}
				</ul>
			</div>
		</div>
	</div>
</div>

{include file='footer' sandbox=false}

</body>
</html>