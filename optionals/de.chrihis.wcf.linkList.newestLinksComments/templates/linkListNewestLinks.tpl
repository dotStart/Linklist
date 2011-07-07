{if LINKLIST_NEWESTLINKS_TYPE == 1}
	<div class="{cycle values='container-1,container-2'}">
		<div class="containerIcon">
			<img src="{icon}linkListLinkM.png{/icon}" alt="" />
		</div>
		<div class="containerContent">
			<a href="javascript: void(0)" onclick="openList('linkListShowNewestLinks', { save:true })">
				<h3>{lang}wcf.linkList.newestLinks.title{/lang}</h3>
			</a>
			<p class="smallFont">{lang}wcf.linkList.newestLinks.description{/lang}</p>
			
			<ul id="linkListShowNewestLinks" class="newestLinkListLinks">
				{if !$links|count}
					<p class="smallFont"><em>{lang}wcf.linkList.newestLinks.noLinks{/lang}</em></p>
				{else}
					{foreach from=$links item=link}
						<li>
							<ul class="breadCrumbs">
								<li>
									<a href="index.php?page=LinkListLinkCategory&amp;categoryID={@$link->categoryID}{@SID_ARG_2ND}">
										<img src="{icon}{@$link->category->getIconName()}S.png{/icon}" alt="" />
										<span>{$link->category->title}</span>
									</a> &raquo;
								</li>
								<li>
									<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">
										<img src="{icon}linkListLinkS.png{/icon}" alt="" />
										<span>{$link->subject}</span>
									</a> 
									<span class="light">({@$link->time|shorttime})</span>
								</li>
							</ul>
						</li>
					{/foreach}
				{/if}
			</ul>
		</div>
	</div>
{else}
	<div class="border titleBarPanel">
		<div class="containerHead">
			<div class="containerIcon">
				<a href="javascript: void(0)" onclick="openList('linkListShowNewestLinks', { save:true })">
					<img src="{icon}minusS.png{/icon}" id="linkListShowNewestLinksImage" alt="" />
				</a>
			</div>
			<div class="containerContent">
				{lang}wcf.linkList.newestLinks.title{/lang}
			</div>
		</div>
	
		<div id="linkListShowNewestLinks">
			{if !$links|count}
				<p>{lang}wcf.linkList.newestLinks.noLinks{/lang}</p>
			{else}
				<table class="tableList">
					<tbody>
						{cycle values='container-1,container-2' name='className' print=false advance=false}
						{foreach from=$links item=link}
							<tr class="{cycle name='className'} normalFont">				
								<td class="columnTopXLinks" style="width:25%;">
									<img src="{icon}linkListCategoryS.png{/icon}" alt="" />
									<a href="index.php?page=LinkListCategory&amp;categoryID={$link->categoryID}{@SID_ARG_2ND}"> <span>{$link->category->title|truncate:35:"..."}</span></a> &raquo;
								</td>                        
								<td class="columnTopXLinksSubject" style="width:39%;">
									<span style="float: right;">
										{if $link->attachments}
											<img src="{icon}attachmentS.png{/icon}" alt="" title="{lang}wcf.linkList.newestLinks.attachments{/lang}" />
										{/if}
										{if $link->userID == $this->user->userID}
											<img src="{icon}userS.png{/icon}" alt="" title="{lang}wcf.linkList.newestLinks.ownLink{/lang}" />
										{/if}
									</span>
										<img src="{icon}linkListLinkS.png{/icon}" alt="" /> <a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}"><span>{$link->subject|truncate:50:"..."}</span></a>
								</td>
								<td class="columnTopXLinksTime" style="width:31%;">
									<div class="containerIconSmall">
										<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}"><img src="{icon}nextS.png{/icon}" alt="" /></a>
									</div>
									<div class="containerContentSmall">
										<p class="smallFont">{lang}wcf.linkList.link.by{/lang}
											{if $link->userID}
												{$link->getAuthor()->username}
											{else}
												{$link->username}
											{/if} 
											<span class="light">({@$link->time|shorttime})</span>
										</p>
									</div>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			{/if}
		</div>
	</div>
{/if}
<script type="text/javascript">
	//<![CDATA[
	initList('linkListShowNewestLinks', {@$status});
	//]]>
</script>     
