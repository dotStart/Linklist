{if 'LINKLISTLINK_SHOW_COMMENTS_ON_LINK_PAGE'|defined == false && $category->allowComments && !$additionalTabMenuItems|isset}
	<div id="profileContent" class="tabMenu">
		<ul>
			<li{if $activeTabMenuItem == 'wcf.linkList.link.menu.link'} class="activeTabMenu"{/if}><a href="index.php?page=LinkListLink&amp;linkID={@$linkID}{@SID_ARG_2ND}"><span>{lang}wcf.linkList.link.menu.link{/lang}</span></a></li>
			{if 'LINKLISTLINK_SHOW_COMMENTS_ON_LINK_PAGE'|defined == false}
				<li{if $activeTabMenuItem == 'wcf.linkList.link.menu.comments'} class="activeTabMenu"{/if}><a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$linkID}{@SID_ARG_2ND}"><span>{lang}wcf.linkList.link.menu.comments{/lang}</span></a></li>
			{/if}
			{if $additionalTabMenuItems|isset}{@$additionalTabMenuItems}{/if}
		</ul>
	</div>
	
	<div class="subTabMenu">
		<div class="containerHead">
			{if $activeTabMenuItem == 'wcf.linkList.link.menu.comments'}
				<ul>
					{if $activeTabMenuItem == 'wcf.linkList.link.menu.comments' && 'LINKLISTLINK_SHOW_COMMENTS_ON_LINK_PAGE'|defined == false}
						<li{if $activeSubTabMenuItem == 'wcf.linkList.link.menu.comments'} class="activeSubTabMenu"{/if}><a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$linkID}{@SID_ARG_2ND}"><span>{lang}wcf.linkList.link.menu.comments{/lang}</span></a></li>
						<li{if $activeSubTabMenuItem == 'wcf.linkList.link.menu.comment.add'} class="activeSubTabMenu"{/if}><a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}"><span>{lang}wcf.linkList.link.menu.comment.add{/lang}</span></a></li>
						{if $additionalSubTabMenuItems.wcf.linkList.link.menu.comments|isset}{@$additionalSubTabMenuItems.wcf.linkList.link.menu.comments}{/if}
					{/if}
				</ul>
			{else}
				<div> </div>
			{/if}
		</div>
	</div>
{/if}