{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $categories|count > 0 && $categories|count < 100 && $this->user->getPermission('admin.linkList.canEditCategory')}
			new ItemListEditor('categoryList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=LinkListCategoryRename&categoryID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/linkListCategoryL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.linkList.category.list{/lang}</h2>
	</div>
</div>

{if $adminMessages|isset}{@$adminMessages}{/if}

{if $successfulSorting}
	<p class="success">{lang}wcf.acp.linkList.category.sort.success{/lang}</p>	
{/if}

{if $successfulDeleting}
	<p class="success">{lang}wcf.acp.linkList.category.delete.success{/lang}</p>	
{/if}

{if $this->user->getPermission('admin.linkList.canAddCategory')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=LinkListCategoryAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.linkList.category.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/linkListCategoryAddM.png" alt="" /> <span>{lang}wcf.acp.linkList.category.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $categories|count > 0}
	{if $this->user->getPermission('admin.linkList.canEditCategory')}
	<form method="post" action="index.php?action=LinkListCategorySort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="categoryList">
					{foreach from=$categories item=child}
						{* define *}
						{assign var="category" value=$child.category}
						{assign var="categoryID" value=$category->categoryID}
						
						<li id="item_{@$category->categoryID}" class="deletable">
							<div class="buttons">
								{if $this->user->getPermission('admin.linkList.canEditCategory')}
									<a href="index.php?form=LinkListCategoryEdit&amp;categoryID={@$categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.global.button.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.global.button.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.linkList.canAddCategory')}
									<a href="index.php?form=LinkListCategoryAdd&amp;parentID={@$categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.linkList.category.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}wcf.acp.linkList.category.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.linkList.canDeleteCategory')}
									<a href="index.php?action=LinkListCategoryDelete&amp;categoryID={@$categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.global.button.delete{/lang}" class="deleteButton"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" longdesc="{lang}wcf.acp.linkList.category.delete.sure{/lang}"  /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.global.button.delete{/lang}" />
								{/if}
								
								{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle{if $category->isMainCategory()} itemListCategory{/if}">
								
								{if $this->user->getPermission('admin.linkList.canEditCategory')}
									<select name="categoryListPositions[{@$categoryID}][{@$child.parentID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$categoryID} <a href="index.php?form=LinkListCategoryEdit&amp;categoryID={@$categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$category->title}{/lang}</a>
							</h3>
						
						{if $child.hasChildren}<ol id="parentItem_{@$categoryID}">{else}<ol id="parentItem_{@$categoryID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.linkList.canEditCategory')}
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" id="reset" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	 		{@SID_INPUT_TAG}
	 	</div>
	</form>
	{/if}
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.acp.linkList.category.count.noCategories{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}
