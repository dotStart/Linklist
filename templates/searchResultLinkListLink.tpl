<div class="message content">
	<div class="messageInner container-{cycle name='results' values='1,2'}">
		<div class="messageHeader">
			<div class="containerIcon">
				<a href="index.php?page=LinkListLink&amp;linkID={@$item.message->linkID}&amp;highlight={$query|urlencode}{@SID_ARG_2ND}"><img src="{icon}{@$item.message->getIconName()}M.png{/icon}" alt="" style="width: 24px;" /></a>
			</div>
			<div class="containerContent">
				<p class="light smallFont">{@$item.message->time|time}</p>
				<p class="light smallFont">{lang}wcf.linkList.link.by{/lang} <a href="index.php?page=User&amp;userID={@$item.message->userID}{@SID_ARG_2ND}">{$item.message->username}</a></p>
			</div>
		</div>
		
		<h3><a href="index.php?page=LinkListLink&amp;linkID={@$item.message->linkID}&amp;highlight={$query|urlencode}{@SID_ARG_2ND}">{$item.message->subject}</a></h3>
		
		<div class="messageBody">
			{@$item.message->getFormattedMessage()}
		</div>
		
		<div class="messageFooter">
			<ul class="breadCrumbs light">
				<li><img src="{icon}linkListS.png{/icon}" alt="" /> <a href="index.php?page=LinkList{@SID_ARG_2ND}">{lang}wcf.linkList.links{/lang}</a> &raquo;</li>
				<li><img src="{icon}linkListCategoryS.png{/icon}" alt="" /> <a href="index.php?page=LinkListCategory&amp;categoryID={@$item.message->categoryID}{@SID_ARG_2ND}">{lang}{$item.message->title}{/lang}</a> &raquo; </li>
				<li><img src="{icon}linkListLinkS.png{/icon}" alt="" /> <a href="index.php?page=LinkListLink&amp;linkID={@$item.message->linkID}&amp;highlight={$query|urlencode}{@SID_ARG_2ND}">{$item.message->subject}</a></li>
			</ul>
			<div class="smallButtons">
				<ul>
					<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
				</ul>
			</div>
		</div>
		<hr />
	</div>
</div>