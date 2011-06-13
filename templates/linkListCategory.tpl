{include file="documentHeader"}
<head>
	<title>{lang}{@$category->title}{/lang} - {lang}wcf.linkList.index{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/LinkListLinkListEdit.class.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/linkList.css" />
	<script type="text/javascript">
		//<![CDATA[
		var linkData = new Hash();
		
		// url
		var url = 'index.php?page=LinkListCategory&categoryID={@$categoryID}{@SID_ARG_2ND_NOT_ENCODED}';
	
		// constants
		var ENABLE_RECYCLE_BIN = {@LINKLIST_LINK_ENABLE_RECYCLE_BIN};
	
		// ids
		var categoryID = {if $category|isset}{@$category->categoryID}{else}0{/if};

		// language
		var language = new Object();
		language['wcf.global.button.mark']		= '{lang}wcf.global.button.mark{/lang}';
		language['wcf.global.button.unmark']		= '{lang}wcf.global.button.unmark{/lang}';
		language['wcf.global.button.delete']		= '{lang}wcf.global.button.delete{/lang}';
		language['wcf.global.button.deleteCompletely'] 		= '{lang}wcf.global.button.deleteCompletely{/lang}';
		language['wcf.global.button.edit']		= '{lang}wcf.global.button.edit{/lang}';
	
		language['wcf.linkList.category.links.recover'] 	= '{lang}wcf.linkList.category.links.recover{/lang}';
		language['wcf.linkList.category.links.enable']	= '{lang}wcf.linkList.category.links.enable{/lang}';
		language['wcf.linkList.category.links.disable'] 	= '{lang}wcf.linkList.category.links.disable{/lang}';
		language['wcf.linkList.category.links.open'] 	= '{lang}wcf.linkList.category.links.open{/lang}';
		language['wcf.linkList.category.links.close'] 	= '{lang}wcf.linkList.category.links.close{/lang}';
		language['wcf.linkList.category.links.editSubject'] 	= '{lang}wcf.linkList.category.links.editSubject{/lang}';
		language['wcf.linkList.category.markedLinks'] 	= '{lang}wcf.linkList.category.markedLinks{/lang}';
		language['wcf.linkList.category.delete.sure'] 	= '{lang}wcf.linkList.category.deleteSure{/lang}';
		language['wcf.linkList.category.links.deleteCompletely.sure'] 	= '{lang}wcf.linkList.category.links.deleteCompletely.sure{/lang}';
		language['wcf.linkList.category.links.deleteMarked.sure'] 	= '{lang}wcf.linkList.category.links.deleteMarked.sure{/lang}';
		language['wcf.linkList.category.links.move'] 	= '{lang}wcf.linkList.category.links.move{/lang}';
		language['wcf.linkList.category.links.stick'] 	= '{lang}wcf.linkList.category.links.stick{/lang}';
		language['wcf.linkList.category.links.unstick'] 		= '{lang}wcf.linkList.category.links.unstick{/lang}';
		language['wcf.linkList.category.links.showMarkedLinks'] 	= '{lang}wcf.linkList.category.links.showMarkedLinks{/lang}';
		language['wcf.linkList.category.links.deleteMarked.sure'] 	= '{lang}wcf.linkList.category.links.deleteMarked.sure{/lang}';
		language['wcf.global.button.submit']			= '{lang}wcf.global.button.submit{/lang}';
		language['wcf.global.button.reset']			= '{lang}wcf.global.button.reset{/lang}';
		language['wcf.linkList.category.links.isDeleted']		= '<p class="linkListLinkDeleteNote error">{lang}wcf.linkList.category.links.isDeleted{/lang}</p>';
		language['wcf.linkList.link.isDisabled'] 	= '<p class="disableNote info">{lang}wcf.linkList.link.isDisabled{/lang}</p>';
		
		// permissions
		var permissions = new Object();
		permissions['canEditLink'] = {if $this->user->getPermission('mod.linkList.canEditLink')}1{else}0{/if};
		permissions['canPinLink'] = {if $this->user->getPermission('mod.linkList.canPinLink')}1{else}0{/if};
		permissions['canCloseLink'] = {if $this->user->getPermission('mod.linkList.canCloseLink')}1{else}0{/if};
		permissions['canDeleteLink'] = {if $this->user->getPermission('mod.linkList.canDeleteLink')}1{else}0{/if};
		permissions['canDeleteLinkCompletely'] = {if $this->user->getPermission('mod.linkList.canDeleteLinkCompletely')}1{else}0{/if};
		permissions['canEnableLink'] = {@$this->user->getPermission('mod.linkList.canEnableLink')};
		permissions['canMoveLink'] = {@$this->user->getPermission('mod.linkList.canMoveLink')};
	
		// init
		onloadEvents.push(function() {
			linkListLinkListEdit = new LinkListLinkListEdit(linkData, {@$markedLinks});
		});
		//]]>
	</script>	
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
		<li><a href="index.php?page=LinkList{@SID_ARG_2ND}"><img src="{icon}linkListS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.index{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=LinkListCategory&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{if $parentCategory->image}{@$parentCategory->image}{else}{@$parentCategory->getIconName()}{/if}S.png{/icon}" alt="" /> <span>{lang}{$parentCategory->title}{/lang}</span></a> &raquo;</li>
		{/foreach}
	</ul>

	<div class="mainHeadline">
		<img src="{icon}{if $category->image}{@$category->image}{else}{@$category->getIconName()}{/if}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2><a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{lang}{$category->title}{/lang}</a></h2>
		</div>
	</div>
	
	{if $userMessages|isset}{@$userMessages}{/if}
	
	{if $categories|count > 0}
		{cycle name='linkListCategoryListCycle' values='1,2' advance=false print=false}
		<div class="border content">
			<div class="container-1">
				<div class="contentBox">
					<h3 class="subHeadline">{lang}wcf.acp.linkList.category.list{/lang}</h3>
					<ul id="linkListCategoryList">
						{foreach from=$categories item=child}
							{* define *}
							{assign var="depth" value=$child.depth}
							{assign var="open" value=$child.open}
							{assign var="hasChildren" value=$child.hasChildren}
							{assign var="openParents" value=$child.openParents}
							{assign var="categoryList" value=$child.category}
							{assign var="categoryListID" value=$categoryList->categoryID}
							{counter assign=categoryNo print=false}
							{if $categoryList->isCategory()}
								{* category *}

								<li{if $depth == 1} class="category border titleBarPanel"{/if}>
									<div class="categoryListInner container-{cycle name='linkListCategoryListCycle'} category{@$categoryListID}">
										<div class="categoryListTitle linkListCategoryListCols-2">
											<div class="containerIcon">
												<img src="{icon}{if $categoryList->icon}{@$categoryList->image}{else}{@$categoryList->getIconName()}{/if}M.png{/icon}" alt=""/>
										</div>
							
										<div class="containerContent">
											{if $depth > 3}<h6 class="linkListCategoryTitle">{else}<h{@$depth+2} class="categoryTitle">{/if}
											<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryListID}{@SID_ARG_2ND}">{lang}{$categoryList->title}{/lang}</a>
											{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}

											{if $categoryList->description}
												<p class="linkListCategoryListDescription">
													{lang}{if $categoryList->allowDescriptionHtml}{@$categoryList->description}{else}{$categoryList->description}{/if}{/lang}
												</p>
											{/if}
								
											{if $subCategories.$categoryID|isset}
												<div class="categoryListSubcategories">
													<ul>{foreach name='subCategories' from=$subCategories.$categoryID item=subCategory}{assign var="subCategoryID" value=$subCategory->categoryID}{counter assign=categoryNo print=false}<li>{if $depth > 4}<h6>{else}<h{@$depth+3}>{/if}<img src="{icon}linkList{if $subCategory->isCategory()}Category{elseif $subCategory->isMainCategory()}MainCategory{/if}S.png{/icon}" alt="" />{*
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
											<dd>{#$categoryList->links}</dd>
											{if LINKLIST_ENABLE_COMMENTS}
												<dt>{lang}wcf.linkList.category.stats.comments{/lang}</dt>
												<dd>{#$categoryList->comments}</dd>
											{/if}
											<dt>{lang}wcf.linkList.category.stats.visits{/lang}</dt>
											<dd>{#$categoryList->visits}</dd>
										</dl>
									</div>

								</div>
							{/if}

							{if $categoryList->isMainCategory()}
								{* main category *}
								{cycle name='linkListCategoryListCycle' advance=false print=false reset=true}
								<li{if $depth == 1} class="mainCategory border titleBarPanel"{/if}>
									<div class="containerHead categoryListInner category{@$categoryListID}">
										<div class="categoryListTitle">
											{if $depth > 3}<h6 class="categoryTitle">{else}<h{@$depth+2} class="categoryTitle">{/if}
											<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryListID}{@SID_ARG_2ND}">{lang}{$categoryList->title}{/lang}</a>
											{if $depth > 3}</h6>{else}</h{@$depth+2}>{/if}
											{if $categoryList->description}
												<p class="linkListCategoryListDescription">
													{lang}{if $categoryList->allowDescriptionHtml}{@$categoryList->description}{else}{$categoryList->description}{/if}{/lang}
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
				</div>
			</div>
		</div>
	{/if}
	{if $category->isCategory()}
		<div class="border content">
			<div class="container-1">
				 {if $links|count}
				 	<div class="contentBox">
						<h3 class="subHeadline">{if $tagID}{lang}wcf.linkList.category.links.tagged{/lang}{else}{lang}wcf.linkList.category.links{/lang}{/if} <span>({#$items})</span></h3>
		
						<div class="contentHeader">
							{pages print=true assign=pagesOutput link="index.php?page=LinkListCategory&categoryID=$categoryID&tagID=$tagID&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}
				
							<div class="largeButtons">
								<ul>
									{if $category->getPermission('canAddLink')}
									<li><a href="index.php?form=LinkListLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.linkAdd.pageTitle{/lang}"><img src="{icon}linkListLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.linkAdd.pageTitle{/lang}</span></a></li>
									{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						</div>
	
						<div class="linkListLinkList">
							{cycle values='container-1,container-2' name='className' print=false advance=false}
							{if $sortOrder == 'DESC'}{assign var='messageNumber' value=$items-$startIndex+1}{else}{assign var='messageNumber' value=$startIndex}{/if}
							{foreach from=$links item=link}
								{assign var='linkID' value=$link->linkID}
								<div class="message{if $link->userID == $this->user->userID && $link->isDisabled} disabled{/if}" id="linkListLinkRow{@$link->linkID}">
									<div class="messageInner {cycle name='className'}">
										
										{if $additionalLeftContents.$linkID|isset}
											{@$additionalLeftContents.$linkID}
											<div class="linkListLinkDetails">
										{/if}
										<div class="messageHeader">
											<p class="messageCount">
												<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" class="messageNumber">{#$messageNumber}</a>
												{if $this->user->getPermission('mod.linkList.canEditLink')}
													<span class="messageMarkCheckBox">
														<label><input id="linkListLinkMark{@$link->linkID}" type="checkbox" /></label>
													</span>
												{/if}
											</p>
											<div class="containerIcon">
												{cycle name='className' print=false}
												<script type="text/javascript">
													//<![CDATA[
													linkData.set({@$link->linkID}, {
														'isMarked': {@$link->isMarked()},
														'isDeleted': {@$link->isDeleted},
														'isDisabled': {@$link->isDisabled},
														'isSticky': {@$link->isSticky},
														'isClosed': {@$link->isClosed},
														'className': '{cycle name="className"}'
													});
													//]]>
												</script>
												<img id="linkListLinkEdit{@$link->linkID}" src="{icon}{@$link->getIconName()}M.png{/icon}" alt="" />	
											</div>
											<div class="containerContent">
												<h3 id="linkListLinkSubject{@$link->linkID}" class="subject">
													<a href="index.php?page=LinkListLink&amp;linkID={@$link->linkID}{@SID_ARG_2ND}">{$link->subject}</a>
												</h3>
												<p class="light smallFont">{lang}wcf.linkList.link.by{/lang} {if $link->userID}<a href="index.php?page=User&amp;userID={@$link->getAuthor()->userID}{@SID_ARG_2ND}">{$link->getAuthor()->username}</a>{else}{$link->username}{/if} ({@$link->time|time})</p>
											</div>
										</div>
								
										<div class="messageBody">
											<div id="linkListLinkShortDescription{@$link->linkID}">
												{@$link->getFormattedShortDescription()}
											</div>
										</div>
										
										<div class="editNote smallFont light">
											<p>{lang}wcf.linkList.category.visits{/lang}: {#$link->visits}</p>
											<p>{lang}wcf.linkList.category.comments{/lang}: {#$link->comments}</p>
											{if $tags.$linkID|isset}<p>{lang}wcf.linkList.link.tags{/lang}: {implode from=$tags[$linkID] item=tag}<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}">{$tag->getName()}</a>{/implode}</p>{/if}
										</div>
						
										<div class="messageFooter">
											<div class="smallButtons">
												<ul>
													<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
													{if $category->getPermission('canVisitLink')}<li><a href="index.php?page=LinkListLinkVisit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.visit{/lang}"><img src="{icon}nextS.png{/icon}" alt="" /> <span>{lang}wcf.linkList.link.visit{/lang}</span></a></li>{/if}
													{if $link->isEditable($category)}<li><a href="index.php?form=LinkListLinkEdit&amp;linkID={@$link->linkID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.link.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
													{if $additionalSmallButtons.$linkID|isset}{@$additionalSmallButtons.$linkID}{/if}
												</ul>
											</div>
										</div>
									<hr />
									{if $additionalLeftContents.$linkID|isset}
										</div>
									{/if}
									</div>
								</div>
							{assign var='messageNumber' value=$messageNumber-1}		
							{/foreach}
						</div>
					
						<div class="contentFooter">
							{@$pagesOutput}

							<div id="linkListLinkEditMarked" class="optionButtons"></div>
					
							<div class="largeButtons">
								<ul>
									{if $category->getPermission('canAddLink')}
										<li><a href="index.php?form=LinkListLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.linkAdd.pageTitle{/lang}"><img src="{icon}linkListLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.linkAdd.pageTitle{/lang}</span></a></li>
									{/if}
									{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
								</ul>
							</div>
						</div>
					</div>
				{else}
					<h3 class="subHeadline">{lang}wcf.linkList.category.links{/lang} <span>(0)</span></h3>
					
					<div class="contentHeader">
						<div class="largeButtons">
							<ul>
								{if $category->getPermission('canAddLink')}
									<li><a href="index.php?form=LinkListLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.linkAdd.pageTitle{/lang}"><img src="{icon}linkListLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.linkAdd.pageTitle{/lang}</span></a></li>
								{/if}
								{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
							</ul>
						</div>
					</div>
					
					<div class="border tabMenuContent">
						<div class="container-2">
							<p>{lang}wcf.linklist.category.noLinks{/lang}</p>
						</div>
					</div>
					
					<div id="linkListLinkEditMarked" class="optionButtons"></div>
					
					<div class="largeButtons">
						<ul>
							{if $category->getPermission('canAddLink')}
								<li><a href="index.php?form=LinkListLinkAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wcf.linkList.linkAdd.pageTitle{/lang}"><img src="{icon}linkListLinkAddM.png{/icon}" alt="" /> <span>{lang}wcf.linkList.linkAdd.pageTitle{/lang}</span></a></li>
							{/if}
							{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
						</ul>
					</div>
				{/if}
			</div>
		</div>
		
		{cycle values='container-1,container-2' print=false advance=false}
		<div class="border infoBox">
			<div class="{cycle values='container-1,container-2'}">
				<div class="containerIcon">
					<img src="{icon}sortM.png{/icon}" alt="" />
				</div>

				<div class="containerContent">
					<h3>{lang}wcf.linkList.category.sort{/lang}</h3>
					<form method="get" action="index.php">
						<div class="floatContainer">
							<input type="hidden" name="page" value="LinkListCategory" />
							<input type="hidden" name="categoryID" value="{@$categoryID}" />
							<input type="hidden" name="pageNo" value="{@$pageNo}" />
							{if $tagID}<input type="hidden" name="tagID" value="{@$tagID}" />{/if}
														
							<div class="floatedElement">
								<label for="sortField">{lang}wcf.linkList.category.sortBy{/lang}</label>
								<select name="sortField" id="sortField">
									<option value="subject"{if $sortField == 'subject'} selected="selected"{/if}>{lang}wcf.linkList.category.sortBy.subject{/lang}</option>
									<option value="lastChange"{if $sortField == 'lastChangeTime'} selected="selected"{/if}>{lang}wcf.linkList.category.sortBy.lastChangeTime{/lang}</option>
									<option value="hits"{if $sortField == 'hits'} selected="selected"{/if}>{lang}wcf.linkList.category.sortBy.visits{/lang}</option>
									<option value="comments"{if $sortField == 'comments'} selected="selected"{/if}>{lang}wcf.linkList.category.sortBy.comments{/lang}</option>
									<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wcf.linkList.category.sortBy.time{/lang}</option>
									{if $additionalSortFields|isset}{@$additionalSortFields}{/if}
								</select>

								<select name="sortOrder" id="sortOrder">
									<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
									<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
								</select>
							</div>
					
							<div class="floatedElement">
								<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
							</div>

						{@SID_INPUT_TAG}
						</div>
					</form>
					
				</div>
			</div>
					
			{if $availableTags|count > 0}
				<div class="{cycle values='container-1,container-2'}">
					<div class="containerIcon">
						<img src="{icon}tagM.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h3>
							<span>{lang}wcf.tagging.filter{/lang}</span>
						</h3>
						<ul class="tagCloud">
							{foreach from=$availableTags item=tag}
								<li>
									<a href="index.php?page=LinkListCategory&amp;categoryID={@$categoryID}&amp;tagID={@$tag->getID()}&amp;sortField={@$sortField}&amp;sortOrder={@$sortOrder}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a>
								</li>
							{/foreach}
						</ul>						
					</div>
				</div>
			{/if}
			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}
			
</div>
{include file='footer' sandbox=false}
</body>
</html>