<div class="contentBox">
	<div class="border">
		<div class="containerHead">
			<h3>{lang}wcf.linkList.link.thumbnail{/lang}</h3>
		</div>
		<div class="container-2">
			{if $service == 'fadeout'}
				<br />
			{/if}
			<div style="text-align: center">
				<a href="index.php?page=LinkListLinkVisit&amp;linkID={$linkID}" title="{lang}wcf.linkList.link.visit.value{/lang}"><img src="{$url}" alt="" /></a>
			</div>
		</div>
	</div>
</div>