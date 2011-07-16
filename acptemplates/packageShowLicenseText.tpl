{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/packageLicenseTextL.png" alt="" />
	<div class="headlineContainer">
		<h2>{$package->getName()}</h2>
		<p>{$package->getDescription()}</p>
	</div>
</div>

<fieldset>
	<legend>{lang}wcf.acp.package.licensetext{/lang}</legend>
	<form method="get" name="chooseLanguage" id="chooseLanguage" style="text-align: right;" action="index.php">
		<input type="hidden" name="page" value="PackageShowLicenseText" />
		<label for="languageCode" style="display: inline;">{lang}wcf.acp.package.licensetext.chooseLanguage{/lang}</label>
		{htmlOptions name="languageCode" id="languageCode" options=$availableLanguages selected=$languageCode disableEncoding=true}
		<input type="hidden" name="activePackageID" value="{@$package->getPackageID()}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
			{@SID_INPUT_TAG}
	</form>
	<script type="text/javascript">
		//<![CDATA[
		document.getElementById('languageCode').onchange = function() { document.getElementById('chooseLanguage').submit(); };
		//]]>
	</script>
	<div class="formElement">{lang}wcf.acp.package.licensetext.description{/lang}</div>
	<textarea rows="20" cols="40" readonly="readonly">{$licenseText}</textarea>
</fieldset>

<div class="formSubmit">
	<input type="button" accesskey="c" value="{lang}wcf.global.button.back{/lang}" onclick="document.location.href='index.php?page=PackageView&amp;activePackageID={@$package->getPackageID()}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'" />
</div>
{include file='footer'}