{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/linkListDataImportL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.linkList.import{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}
	
<div class="warning">{lang}wcf.acp.linkList.warning{/lang}</div>

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=LinkListCategoryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.linkList.category.list{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/linkListCategoryM.png" alt="" /> <span>{lang}wcf.acp.linkList.category.list{/lang}</span></a></li></ul>
	</div>
</div>

<form enctype="multipart/form-data" method="post" action="index.php?form=LinkListDataImport">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.linkList.import.sourcefile{/lang}</legend>				
				<div class="formElement{if $errorField == 'fileUpload'} formError{/if}">
					<div class="formFieldLabel">
						<label for="fileUpload">{lang}wcf.acp.linkList.import.chooseFile{/lang}</label>
					</div>
					<div class="formField">
						<input type="file" name="fileUpload" id="fileUpload" />
						{if $errorField == 'styleUpload'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType == 'uploadFailed'}{lang}wcf.acp.linkList.import.upload.failed{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
			</fieldset>
		</div>

	</div>
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 	</div>
</form>

{include file='footer'}