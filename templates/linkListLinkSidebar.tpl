<div class="contentBox">
	<div class="border">
		<div class="containerHead">
			<h3>{lang}wcf.linkList.link.generalData{/lang}</h3>
		</div>
	
		<ul class="dataList">
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}linkListLinkUserM.png{/icon}" alt="" />
				</div>
				<div class="containerContent">
					<h4 class="smallFont">{lang}wcf.linkList.link.author{/lang}</h4>
					<p>{if $link->userID != 0}<a href="index.php?page=User&amp;userID={@$link->userID}{@SID_ARG_2ND}">{$link->getAuthor()->username}</a>{else}{$link->username}{/if}</p>
				</div>
			</li>
			{if $link->kind}
				<li class="{cycle values='container-1,container-2'}">
					<div class="containerIcon"> </div>
					<div class="containerContent">
						<h4 class="smallFont">{lang}wcf.linkList.link.kind{/lang}</h4>
						<p>{lang}{@$link->kind}{/lang}</p>
					</div>
				</li>
			{/if}
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}cronjobsM.png{/icon}" alt="" />
				</div>
				<div class="containerContent">
					<h4 class="smallFont">{lang}wcf.linkList.link.lastChangeTime{/lang}</h4>
					<p>{@$link->lastChangeTime|time}</p>
				</div>
			</li>
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon"> </div>
				<div class="containerContent">
					<h4 class="smallFont">{lang}wcf.linkList.link.time{/lang}</h4>
					<p>{@$link->time|date}</p>
				</div>
			</li>
			<li class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}visitsM.png{/icon}" alt="" />
				</div>
				<div class="containerContent">
					<h4 class="smallFont">{lang}wcf.linkList.link.visits{/lang}</h4>
					<p>{#$link->visits}</p>
				</div>
			</li>
			{if $link->lastVisitTime}
				<li class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
					
					</div>
					<div class="containerContent">
						<h4 class="smallFont">{lang}wcf.linkList.link.lastVisit{/lang}</h4>
						<p>{if $link->lastVisitorID != 0}<a href="index.php?page=User&amp;userID={@$link->lastVisitorID}{@SID_ARG_2ND}">{$link->lastVisitorName}</a>{else}{lang}wcf.linkList.link.guest{/lang}{/if} ({@$link->lastVisitTime|time})</p>
					</div>
				</li>
			{/if}
			{if $link->comments}
				<li class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
						<img src="{icon}messageM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h4 class="smallFont">{lang}wcf.linkList.link.menu.comments{/lang}</h4>
						<p><a href="index.php?page=LinkListLinkCommentList&linkID={$link->linkID}">{$link->comments}</a></p>
					</div>
				</li>
			{/if}
			{if $tags|count}
				<li class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
						<img src="{icon}tagM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h4 class="smallFont">{lang}wcf.linkList.link.tags{/lang}</h4>
						<p>{implode from=$tags item=tag}<a href="index.php?page=TaggedObjects&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%;">{$tag->getName()}</a>{/implode}</p>
					</div>
				</li>
			{/if}
			{if $link->ipAddress && $this->user->getPermission('mod.linkList.canEditLink')}
				<li class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
						<img src="{icon}ipAddressM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h4 class="smallFont">{lang}wcf.linkList.link.ipAddress{/lang}</h4>
						<p>{@$link->ipAddress}</p>
					</div>
				</li>
			{/if}
			{if $additionalDataBoxes|isset}{@$additionalDataBoxes}{/if}
		</ul>
	</div>
</div>
						
<div class="contentBox">
	<div class="border"> 
		<div class="containerHead"> 
			<h3>{lang}wcf.linkList.link.selection{/lang}</h3> 
		</div> 
		<div class="pageMenu"> 
			<ul class="twoRows">
				<li class="{cycle values='container-1,container-2'}">
					<a href="index.php?page=LinkListLinkVisit&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}visitsM.png{/icon}" alt="" /> 
					<label class="smallFont">{lang}wcf.linkList.link.visit{/lang}</label>
					<span>{lang}wcf.linkList.link.visit.value{/lang}</span></a>
				</li>
				{if ($category->allowComments == -1 && LINKLIST_ENABLE_COMMENTS) || $category->allowComments == 1}
					<li class="{cycle values='container-1,container-2'}">
						<a href="index.php?form=LinkListLinkCommentAdd&amp;linkID={@$linkID}{@SID_ARG_2ND}"><img src="{icon}messageAddM.png{/icon}" alt="" /> 
						<label class="smallFont">{lang}wcf.linkList.link.comment.add{/lang}</label>
						<span>{lang}wcf.linkList.link.comment.add.value{/lang}</span></a>
					</li>
				{/if}
				{if $additionalSelection|isset}{@$additionalSelection}{/if}
			</ul>
		</div> 
	</div>
</div>

{if $additionalLinkBoxes|isset}{@$additionalLinkBoxes}{/if}