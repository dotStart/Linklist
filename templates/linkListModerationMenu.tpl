<div id="profileContent" class="tabMenu">
	<ul>
		<li><a href="index.php?page=LinkListModeration{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.overview{/lang}</span></a></li>
		{if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}
			<li{if $activeTabMenuItem == 'deletedLinks'} class="activeTabMenu"{/if}><a href="index.php?page=LinkListModerationDeletedLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.deletedLinks{/lang}</span></a></li>
		{/if}
		{if $this->user->getPermission('mod.linkList.canEnableLink')}
			<li{if $activeTabMenuItem == 'disabledLinks'} class="activeTabMenu"{/if}><a href="index.php?page=LinkListModerationDisabledLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.disabledLinks{/lang}</span></a></li>
		{/if}
		{if $this->user->getPermission('mod.linkList.canEditLink')}
			<li{if $activeTabMenuItem == 'markedLinks'} class="activeTabMenu"{/if}><a href="index.php?page=LinkListModerationMarkedLinks{@SID_ARG_2ND}"><span>{lang}wcf.linkList.moderation.markedLinks{/lang}</span></a></li>
		{/if}
		{if $additionalTabMenuItems|isset}{@$additionalTabMenuItems}{/if}
	</ul>
</div>
	
<div class="subTabMenu">
	<div class="containerHead">
		<div> </div>
	</div>
</div>