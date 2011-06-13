{if $lastVisitors|count > 0}
	<div class="contentBox">
		<div class="border">
			<div class="containerHead">
				<h3>{lang}wcf.linkList.link.lastVisitors.title{/lang}</h3>
			</div>

			<ul class="dataList">
				{foreach from=$lastVisitors item=visitor}
					<li class="{cycle values='container-1,container-2'}">
						<div class="containerIcon">
							<a href="index.php?page=User&amp;userID={@$visitor->userID}{@SID_ARG_2ND}" title="{lang username=$visitor->username}wcf.user.viewProfile{/lang}">
								{if $visitor->getAvatar()}
									{assign var=x value=$visitor->getAvatar()->setMaxSize(24, 24)}
									{@$visitor->getAvatar()}
								{else}
									<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 24px; height: 24px" />
								{/if}
							</a>
						</div>
						<div class="containerContent">
							<h4><a href="index.php?page=User&amp;userID={@$visitor->userID}{@SID_ARG_2ND}" title="{lang username=$visitor->username}wcf.user.viewProfile{/lang}">{@$visitor->username}</a></h4>
							<p class="light smallFont">{@$visitor->time|time}</p>
						</div>
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}
