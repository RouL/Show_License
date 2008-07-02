{include file='setupWindowHeader'}

<form method="post" action="index.php?page=Package">
	<fieldset>
		<legend>{lang}wcf.acp.package.licensetext{/lang}</legend>
		<div class="inner">
			<p>{lang}wcf.acp.package.licensetext.install.description{/lang}</p>

			{if $errorType}
				<p class="error">
					{if $errorType == 'missingAcception'}{lang}wcf.acp.package.licensetext.error.missingacception{/lang}{/if}
				</p>
			{/if}

			<textarea rows="20" cols="40" style="width: 100%" readonly="readonly">{$licenseText}</textarea>
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