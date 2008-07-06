{include file='setupWindowHeader'}
<form method="post" name="chooseLanguage" id="chooseLanguage" action="index.php?page=Package">
	<input type="hidden" name="languageCode" id="useLanguage" value="{@$queueID}" />
	<input type="hidden" name="queueID" value="{@$queueID}" />
	<input type="hidden" name="action" value="{@$action}" />
	{@SID_INPUT_TAG}
	<input type="hidden" name="step" value="{@$step}" />
	<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	<input type="hidden" name="langChooser" value="langChooser" />
	<input type="hidden" name="send" value="send" />
</form>
<form method="post" action="index.php?page=Package">
	<fieldset>
		<legend>{lang}wcf.acp.package.licensetext{/lang}</legend>
		<div class="inner">
			<div style="text-align: right;">
				<label for="languageCode" style="display: inline;">{lang}wcf.acp.package.licensetext.chooseLanguage{/lang}</label>
				{htmlOptions name="languageCode" id="languageCode" options=$availableLanguages selected=$languageCode disableEncoding=true}
			</div>
			<script type="text/javascript">
				//<![CDATA[
				document.getElementById('languageCode').onchange = function() { document.getElementById('useLanguage').value = this.value; document.getElementById('chooseLanguage').submit(); };
				//]]>
			</script>
			
			<p>{lang}wcf.acp.package.licensetext.install.description{/lang}</p>

			{if $errorType}
				<p class="error">
					{if $errorType == 'missingAcception'}{lang}wcf.acp.package.licensetext.error.missingacception{/lang}{/if}
				</p>
			{/if}

			<textarea rows="20" cols="40" readonly="readonly">{$licenseText}</textarea>
			<p><label{if $errorType == 'missingAcception'} class="errorField"{/if}><input type="checkbox" name="licenseAccepted" value="1" /> {lang}wcf.acp.package.licensetext.accept.description{/lang}</label></p>

			<input type="hidden" name="queueID" value="{@$queueID}" />
			<input type="hidden" name="action" value="{@$action}" />
			{@SID_INPUT_TAG}
			<input type="hidden" name="step" value="{@$step}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
			<input type="hidden" name="send" value="send" />
		</div>
	</fieldset>

	<div class="nextButton">
		<input type="submit" value="{lang}wcf.global.button.next{/lang}" />
	</div>
</form>

<script type="text/javascript">
	//<![CDATA[
	parent.showWindow(true);
	parent.setCurrentStep('{lang}wcf.acp.package.step.title{/lang}{lang}wcf.acp.package.step.{@$action}.{@$step}{/lang}');
	//]]>
</script>

{include file='setupWindowFooter'}