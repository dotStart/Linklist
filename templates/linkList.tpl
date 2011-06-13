{include file="documentHeader"}
<head>
	<title>{lang}wcf.linkList.index{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/linkList.css" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wcf.linkList.link.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="types[]" value="linkListLink" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
	</ul>
	
	<div class="mainHeadline">
		<img src="{icon}linkListL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.linkList.index{/lang}</h2>
			<p>{lang}wcf.linkList.index.description{/lang}</p>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $disabledLinks != 0 || $markedLinks != 0}
		<div class="info smallFont">
			{if $disabledLinks}<a href="index.php?page=LinkListModerationDisabledLinks{@SID_ARG_2ND}">{if $disabledLinks == 1}1 Link</a> ist{else}{#$disabledLinks} Links</a> sind{/if} deaktiviert.{/if} {if $markedLinks}<a href="index.php?page=LinkListModerationMarkedLinks{@SID_ARG_2ND}">{if $markedLinks == 1}1 Link</a> ist{else}{#$markedLinks} Links</a> sind{/if} markiert.{/if}
		</div>
	{/if}
	
	{if $additionalMessages|isset}{@$additionalMessages}{/if}
	
	{if $categories|count}
		{cycle name='linkListCategoryListCycle' values='1,2' advance=false print=false}
		<div class="border content">
			<div class="container-1">
				<div class="contentBox">
					<ul id="linkListCategoryList">
						{if $categories|count > 0}
							{foreach from=$categories item=child}
								{* define *}
								{assign var="depth" value=$child.depth}
								{assign var="open" value=$child.open}
								{assign var="hasChildren" value=$child.hasChildren}
								{assign var="openParents" value=$child.openParents}
								{assign var="category" value=$child.category}
								{assign var="categoryID" value=$category->categoryID}
								{counter assign=boardNo print=false}
								{if $category->isCategory()}
									{* category *}

									<li{if $depth == 1} class="category border titleBarPanel"{/if}>
										<div class="categoryListInner container-{cycle name='linkListCategoryListCycle'} category{@$categoryID}">
											<div class="categoryListTitle linkListCategoryListCols-2">
												<div class="containerIcon">
													<img src="{icon}{if $category->image}{@$category->image}{else}{@$category->getIconName()}{/if}M.png{/icon}" alt=""/>
											</div>
							
											<div class="containerContent">
												{if $depth > 3}<h6 class="boardTitle">{else}<h{@$depth+2} class="categoryTitle">{/if}
												<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{lang}{$category->title}{/lang}</a>
												{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}

												{if $category->description}
													<p class="linkListCategoryListDescription">
														{lang}{if $category->allowDescriptionHtml}{@$category->description}{else}{$category->description}{/if}{/lang}
													</p>
												{/if}
								
												{if $subCategories.$categoryID|isset}
													<div class="categoryListSubcategories">
														<ul>{foreach name='subCategories' from=$subCategories.$categoryID item=subCategory}{assign var="subCategoryID" value=$subCategory->categoryID}{counter assign=categoryNo print=false}<li>{if $depth > 4}<h6>{else}<h{@$depth+3}>{/if}<img src="{icon}{if $category->image}{@$category->image}{else}{@$category->getIconName()}{/if}S.png{/icon}" alt="" />{*
														*}&nbsp;<a href="index.php?page=LinkListCategory&amp;categoryID={@$subCategoryID}{@SID_ARG_2ND}">{lang}{$subCategory->title}{/lang}</a>{if $depth > 4}</h6>{else}</h{@$depth+3}>{/if}{*
															*}</li>{/foreach}</ul>
													</div>
												{/if}
								
												{if $child.additionalBoxes|isset}{@$child.additionalBoxes}{/if}
											</div>
										</div>
						
										<div class="linkListCategoryListStats">
											<dl>
												<dt>{lang}wcf.linkList.category.stats.links{/lang}</dt>
												<dd>{#$category->links}</dd>
												{if LINKLIST_ENABLE_COMMENTS}
													<dt>{lang}wcf.linkList.category.stats.comments{/lang}</dt>
													<dd>{#$category->comments}</dd>
												{/if}
												<dt>{lang}wcf.linkList.category.stats.visits{/lang}</dt>
												<dd>{#$category->visits}</dd>
											</dl>
										</div>

									</div>
								{/if}

								{if $category->isMainCategory()}
									{* main category *}
									{cycle name='linkListCategoryListCycle' advance=false print=false reset=true}
									<li{if $depth == 1} class="mainCategory border titleBarPanel"{/if}>
										<div class="containerHead categoryListInner category{@$categoryID}">
											<div class="categoryListTitle">
												{if $depth > 3}<h6 class="categoryTitle">{else}<h{@$depth+2} class="categoryTitle">{/if}
												<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{lang}{$category->title}{/lang}</a>
												{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}
												{if $category->description}
													<p class="linkListCategoryListDescription">
														{lang}{if $category->allowDescriptionHtml}{@$category->description}{else}{$category->description}{/if}{/lang}
													</p>
												{/if}
							
												{if $subCategories.$categoryID|isset}
													<div class="categoryListSubcategories">
														<ul>{foreach name='subCategories' from=$subCategories.$categoryID item=subCategory}{assign var="subCategoryID" value=$subCategory->categoryID}<li>{if $depth > 4}<h6>{else}<h{@$depth+3}>{/if}<img src="{icon}linkList{if $subCategory->isCategory()}Category{elseif $subCategory->isMainCategory()}MainCategory{/if}S.png{/icon}" alt="" />{*
														*}&nbsp;<a href="index.php?page=LinkListCategory&amp;categoryID={@$subCategoryID}{@SID_ARG_2ND}">{lang}{$subCategory->title}{/lang}</a>{if $depth > 4}</h6>{else}</h{@$depth+3}>{/if}{*
														*}</li>{/foreach}</ul>
													</div>
												{/if}
								
												{if $child.additionalBoxes|isset}{@$child.additionalBoxes}{/if}
											</div>
										</div>
							{/if}
			
							{if $hasChildren}<ul id="category{@$categoryID}">{else}</li>{/if}
							{if $openParents > 0}{@"</ul></li>"|str_repeat:$openParents}{/if}
							{/foreach}
						</ul>
					{/if}
				</div>
			</div>
		</div>
	{/if}
	
	{cycle values='container-1,container-2' print=false advance=false}
	<div class="border infoBox">
		<div class="{if $additionalLinkListBoxes|isset}container-2{else}container-1{/if}">
			<div class="containerIcon">
				<img src="{icon}statisticsM.png{/icon}" alt="" />
			</div>
			<div class="containerContent">
				<h3>{lang}wcf.linkList.statistics{/lang}</h3> 
				<p class="smallFont">{lang}wcf.linkList.statistics.details{/lang}</p>
			</div>
		</div>
		{if $additionalLinkListBoxes|isset}{@$additionalLinkListBoxes}{/if}	
	</div>
	
	<div class="contentFooter">				
		<div class="largeButtons">
			<ul>
				{if $this->user->getPermission('mod.linkList.canEnableLink') || $this->user->getPermission('mod.linkList.canEditLink') || $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}
					<li><a href="index.php?page=LinkListModeration{@SID_ARG_2ND}" title="{lang}wcf.linkList.moderation{/lang}"><img src="{icon}linkListModerationM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.moderation{/lang}</span></a></li>
				{/if}
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
			</ul>
		</div>
	</div>
	
</div>

{include file='footer' sandbox=false}

</body>
</html>