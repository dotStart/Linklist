<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH}; 
	//]]>
</script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
<a id="comments"></a>
<div class="contentBox">
	<h4 class="subHeadline">{lang}wcf.linkList.link.commentList{/lang} <span>({#$items})</span></h4>
								
	<div class="contentHeader">
		{pages print=true assign=pagesOutput link="index.php?page=LinkListLink&linkID=$linkID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}

		<div class="smallButtons">
			<ul>
				{if $category->getPermission('canAddComment') && (!$link->isClosed ||$this->user->getPermission('mod.linkList.canEditComment'))}
					<li><a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.add{/lang}"><img src="{icon}addS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.comment.add{/lang}</span></a></li>
				{/if}
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
			</ul>
		</div>
	</div>
	{if $comments|count}
		<div class="border">
			<ul class="dataList messages">
				{assign var='messageNumber' value=$items-$startIndex+1}
				{foreach from=$comments item=comment}
					<li class="deletable {cycle values='container-1,container-2'}">
						<a id="comment{@$comment->commentID}"></a>
						<div class="containerIcon">
							{if $comment->getUser()->getAvatar()}
								{assign var=x value=$comment->getUser()->getAvatar()->setMaxSize(24, 24)}
								{if $comment->userID}<a href="index.php?page=User&amp;userID={@$comment->userID}{@SID_ARG_2ND}" title="{lang username=$comment->username}wcf.user.viewProfile{/lang}">{/if}{@$comment->getUser()->getAvatar()}{if $comment->userID}</a>{/if}
							{else}
								{if $comment->userID}<a href="index.php?page=User&amp;userID={@$comment->userID}{@SID_ARG_2ND}" title="{lang username=$comment->username}wcf.user.viewProfile{/lang}">{/if}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 24px; height: 24px" />{if $comment->userID}</a>{/if}
							{/if}
						</div>
						<div class="containerContent">
							<div class="buttons">
								{if $link->isClosed == 0 || $this->user->getPermission('mod.linkList.canEditComment')}
									{if $comment->isEditable($category, $link)}<a href="index.php?form=LinkListLinkCommentEdit&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /></a>{/if}
									{if $comment->isDeletable($category, $link)}<a href="index.php?action=LinkListLinkCommentDelete&amp;commentID={@$comment->commentID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.linkList.link.comment.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" longdesc="{lang}wcf.linkList.link.comment.delete.sure{/lang}" /></a>{/if}
									{if $this->user->getPermission('mod.linkList.canEditComment') && $comment->ipAddress}<a href="javascript: void(0)" onclick="openList('linkListLinkComment{@$comment->commentID}IPAddress', false)" title="{lang}wcf.linkList.link.commentList.show.ipAddress{/lang}"><img src="{icon}ipAddressS.png{/icon}" alt="" /></a>{/if}
								{/if}
								<a href="index.php?page=LinkListLink&amp;linkID={@$linkID}&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}#comment{@$comment->commentID}">#{#$messageNumber}</a>
							</div>
							<p class="firstPost smallFont light">{lang}wcf.linkList.link.by{/lang} {if $comment->userID}<a href="index.php?page=User&amp;userID={@$comment->userID}{@SID_ARG_2ND}">{$comment->username}</a>{else}{$comment->username}{/if} ({@$comment->time|time})</p>
							<p>{@$comment->getFormattedMessage()}</p>
							{if $comment->ipAddress && $this->user->getPermission('mod.linkList.canEditComment')}
								<div id="linkListLinkComment{@$comment->commentID}IPAddress"><em class="smallFont"><strong>{lang}wcf.linkList.link.commentList.ipAddressBy{/lang}</strong> <span>{@$comment->ipAddress}</span></em></div>
							{/if}
						</div>
					</li>
					<script type="text/javascript">
						//<![CDATA[
						initList('linkListLinkComment{@$comment->commentID}IPAddress', 0);
						//]]>
					</script>
					<hr />
				{assign var='messageNumber' value=$messageNumber-1}
				{/foreach}
			</ul>
		</div>
	{else}
		<div class="border tabMenuContent">
			<div class="container-2">
				<p>{lang}wcf.linklist.link.commentList.noComments{/lang}</p>
			</div>
		</div>
	{/if}
	
	<div class="contentFooter">
		{@$pagesOutput}
			
		<div class="smallButtons">
			<ul>
				{if $category->getPermission('canAddComment') && (!$link->isClosed ||$this->user->getPermission('mod.linkList.canEditComment'))}
					<li><a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.comment.add{/lang}"><img src="{icon}addS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.comment.add{/lang}</span></a></li>
				{/if}
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
			</ul>
		</div>
	</div>
								
	<div class="buttonBar">
		<div class="smallButtons">
			<ul>
				<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
			</ul>
		</div>
	</div>
</div>
