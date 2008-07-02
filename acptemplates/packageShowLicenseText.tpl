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
	<div class="formElement">{lang}wcf.acp.package.licensetext.description{/lang}</div>
	<textarea rows="20" cols="40" style="width: 100%" readonly="readonly">{$licenseText}</textarea>
</fieldset>

<div class="formSubmit">
	<input type="button" accesskey="c" value="{lang}wcf.global.button.back{/lang}" onclick="document.location.href='index.php?page=PackageView&amp;activePackageID={@$package->getPackageID()}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'" />
</div>
{include file='footer'}