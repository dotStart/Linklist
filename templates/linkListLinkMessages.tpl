<div id="linkListLinkMessages{@$linkID}">
	{if $link->isDeleted}
		<div class="error linkListLinkDeleteNote">{lang}wcf.linkList.link.isDeleted{/lang}</div>
	{/if}
	{if $link->isDisabled == 1 && $link->everEnabled == 1}
		<div class="disableNote info">{lang}wcf.linkList.link.isDisabled{/lang}</div>
	{elseif $link->everEnabled == 0}
		<div class="disableNote info">{lang}wcf.linkList.link.notEverEnabled{/lang}</div>
	{/if}
</div>