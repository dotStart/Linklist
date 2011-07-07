{if LINKLIST_NEWESTCOMMENTS_TYPE == 1}
	<div class="{cycle values='container-1,container-2'}">
		<div class="containerIcon">
			<img src="{icon}messageM.png{/icon}" alt="" />
		</div>
		<div class="containerContent">
			<a href="javascript: void(0)" onclick="openList('linkListShowNewestComments', { save:true })">
				<h3>{lang}wcf.linkList.newestComments.title{/lang}</h3>
			</a>
			<p class="smallFont">{lang}wcf.linkList.newestComments.description{/lang}</p>			
			
			<ul id="linkListShowNewestComments" class="newestLinkListComments">
				{if !$comments|count}
					<p class="smallFont"><em>{lang}wcf.linkList.newestComments.noComments{/lang}</em></p>
				{else}
					{foreach from=$comments item=comment}
						<li>
							<ul class="breadCrumbs">
								<li>
									<a href="index.php?page=LinkListLink&amp;linkID={@$comment->linkID}{@SID_ARG_2ND}">
										<img src="{icon}linkListLinkS.png{/icon}" alt="" />
										<span>{$comment->link->subject}</span>
									</a> &raquo;
								</li>
								<li>
									<a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$comment->linkID}&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}#comment{@$comment->commentID}">
										<img src="{icon}messageS.png{/icon}" alt="" />
										<span>{$comment->getExcerpt()|truncate:150:"..."}</span>
									</a> 
									<span class="light">({@$comment->time|shorttime})</span>
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
				<a href="javascript: void(0)" onclick="openList('linkListShowNewestComments', { save:true })">
					<img src="{icon}minusS.png{/icon}" id="linkListShowNewestCommentsImage" alt="" />
				</a>
			</div>
			<div class="containerContent">
				{lang}wcf.linkList.newestComments.title{/lang}
			</div>
		</div>
	
		<div id="linkListShowNewestComments">
			{if !$comments|count}
				<p>{lang}wcf.linkList.newestComments.noComments{/lang}</p>
			{else}
				<table class="tableList">                			                                                        	
					<tbody>
						{cycle values='container-1,container-2' name='className' print=false advance=false}
						{foreach from=$comments item=comment}
							<tr class="{cycle name='className'} normalFont">				
								<td class="columnTopXComments" style="width:25%;">
									<img src="{icon}linkListLinkS.png{/icon}" alt="" />
									<a href="index.php?page=LinkListLink&amp;linkID={@$comment->categoryID}{@SID_ARG_2ND}"> <span>{$comment->link->subject|truncate:35:"..."}</span></a> &raquo;
								</td>                        
								<td class="columnTopXCommentsExcerpt" style="width:39%;">
									<span style="float: right;">
										{if $comment->userID == $this->user->userID}
											<img src="{icon}userS.png{/icon}" alt="" title="{lang}wcf.linkList.newestComments.ownComment{/lang}" />
										{/if}
									</span>
										<img src="{icon}messageS.png{/icon}" alt="" /> <a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$comment->linkID}&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}#comment{@$comment->commentID}"><span>{$comment->getExcerpt()|truncate:150:"..."}</span></a>										
								</td>										
								<td class="columnTopXCommentsTime" style="width:31%;">						
									<div class="containerIconSmall">
										<a href="index.php?page=LinkListLinkCommentList&amp;linkID={@$comment->linkID}&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}#comment{@$comment->commentID}"><img src="{icon}nextS.png{/icon}" alt="" /></a>
									</div>
									<div class="containerContentSmall">
										<p class="smallFont">{lang}wcf.linkList.link.by{/lang}
											{if $comment->userID}
												<a href="index.php?page=User&amp;userID={@$comment->userID}{@SID_ARG_2ND}">{$comment->getUser()->username}</a>
											{else}
												{$comment->username}
											{/if} 
											<span class="light">({@$comment->time|shorttime})</span>
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
	initList('linkListShowNewestComments', {@$status});
	//]]>
</script>
