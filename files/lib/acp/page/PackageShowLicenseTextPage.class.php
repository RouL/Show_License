<?php
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/acp/package/Package.class.php');

/**
 * Shows the licensetext of a package
 *
 * @author	Markus Bartz <roul@black-storm.org>
 * @package	de.wbb3mods.wcf.pip.showlicense
 */
class PackageShowLicenseTextPage extends AbstractPage {
	public $package;
	public $packageID = 0;
	public $templateName = 'packageShowLicenseText';
	public $licenseTexts = '';
	public $licenseShowLang = 0;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['activePackageID'])) $this->packageID = intval($_REQUEST['activePackageID']);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get package data and licensetexts
		try {
			$this->package = new Package($this->packageID);
			$sql = "SELECT		languageID,licenseText
				FROM		wcf".WCF_N."_package_installation_licensetext
				WHERE		packageID = ".$this->packageID;
			WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows() < 1) throw new SystemException();
			while ($licensetxt = WCF::getDB()->fetchArray()) {
				$this->licenseTexts[$licensetxt['languageID']] = $licensetxt['licenseText'];
			}
			if (isset($this->licenseTexts[WCFACP::getLanguage()->getLanguageID()])) $this->licenseShowLang = WCFACP::getLanguage()->getLanguageID();
			elseif (isset($this->licenseTexts[Language::getDefaultLanguageID()])) $this->licenseShowLang = Language::getDefaultLanguageID();
			else {
				$licenseLanguageIDs = array_keys($this->licenseTexts);
				$this->licenseShowLang = $licenseLanguageIDs[0];
			}
		}
		catch (SystemException $e) {
			require_once(WCF_DIR.'lib/system/exception/IllegalLinkException.class.php');
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'licenseText' => $this->licenseTexts[$this->licenseShowLang],
			'package' => $this->package
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.package');

		parent::show();
	}
}
?>