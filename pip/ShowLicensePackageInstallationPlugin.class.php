<?php
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractPackageInstallationPlugin.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');
//require_once(WCF_DIR.'lib/system/exception/wBB3ModsSystemException.class.php');

/**
 * This PIP shows a licensetext for the plugin.
 *
 * @author	Markus Bartz <roul@black-storm.org>
 * @package	de.wbb3mods.wcf.pip.showlicense
 */
class ShowLicensePackageInstallationPlugin extends AbstractPackageInstallationPlugin {
	public $tagName = 'licensetexts';
	public $tableName = 'package_installation_licensetext';
	public $installedLanguages = array();
	public $licenseTexts = array();
	public $licenseShowLang = 0;

	/**
	 * @see	 PackageInstallationPlugin::install()
	 */
	public function install() {
		$instructions = $this->installation->getInstructions();
		$licensetxtfiles = $instructions[$this->tagName];

		$this->loadInstalledLanguages();
		$userLanguage = WCFACP::getUser()->languageID;
		$availableTexts = array();
		foreach ($licensetxtfiles as $licensetxtfile) {
			if ($licensetext = $this->readLicenseText($licensetxtfile)) {
				// check required attributes
				if (!isset($licensetxtfile['languagecode'])) {
					//throw new wBB3ModsSystemException("required 'languagecode' attribute for 'licensetexts' tag is missing in '".PackageArchive::INFO_FILE."'", 10001);
					throw new SystemException("required 'languagecode' attribute for 'licensetexts' tag is missing in '".PackageArchive::INFO_FILE."'", 0);
				}
				// check language encoding
				if (!Language::isSupported($licensetxtfile['languagecode'])) {
					// unsupported encoding
					continue;
				}

				if ($licensetxtfile['languagecode'] == WCFACP::getLanguage()->getLanguageCode()) $this->licenseShowLang = WCFACP::getLanguage()->getLanguageID();
				$availableTexts[$licensetxtfile['languagecode']] = $licensetext;
			}
		}

		if (count($availableTexts) < 1) {
			//throw new wBB3ModsSystemException("no license informations in you supported languages available in '".PackageArchive::INFO_FILE."'", 10002);
			throw new SystemException("no license informations in you supported languages available in '".PackageArchive::INFO_FILE."'", 0);
		}


		$fallback = 0;
		foreach ($availableTexts as $languageCode => $licensetext) {

			if (array_key_exists($languageCode, $this->installedLanguages)) {
				if ($fallback == 0) $fallback = $this->installedLanguages[$languageCode];
				if ($languageCode == 'en') $fallback = $this->installedLanguages[$languageCode];
				if ($this->licenseShowLang == 0 &&
					WCFACP::getLanguage()->getLanguageID() != Language::getDefaultLanguageID() &&
					$this->installedLanguages[$languageCode] == Language::getDefaultLanguageID()
				) $this->licenseShowLang = $this->installedLanguages[$languageCode];
				$this->licenseTexts[$this->installedLanguages[$languageCode]] = $licensetext;
			}
		}

		if ($this->licenseShowLang == 0) {
			if ($fallback != 0) $this->licenseShowLang = $fallback;
			else {
				reset($availableTexts);
				$this->licenseTexts[Language::getDefaultLanguageID()] = current($availableTexts);
				$this->licenseShowLang = Language::getDefaultLanguageID();
			}
		}

		$this->promptLicenseConfirmation();

		$itemInserts = '';
		foreach ($this->licenseTexts as $languageID => $licensetext) {

			if (!empty($itemInserts)) $itemInserts .= ',';
			$itemInserts .= "(".$this->installation->getPackageID().",".$languageID.",'".escapeString($licensetext)."')";
		}
		$sql = "INSERT INTO wcf".WCF_N."_".$this->tableName."
			VALUES ".$itemInserts;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * @see	 PackageInstallationPlugin::hasUpdate()
	 */
	public function hasUpdate() {
       		return false;
	}

	/**
	 * @see	 PackageInstallationPlugin::update()
	 */
	public function update() {}

	/**
	 * @see	 PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall() {
		if (parent::hasUninstall()) return true;
		return false;
	}

	/**
	 * @see	 PackageInstallationPlugin::uninstall()
	 */
	public function uninstall() {
		$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
			WHERE		packageID = ".$this->installation->getPackageID();
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Prompts for license confirmation
	 *
	 * @return	bool						accepted
	 */
	protected function promptLicenseConfirmation() {
		$errorType = '';
		if (isset($_POST['send'])) {
			if (isset($_POST['licenseAccepted'])) {
				if (intval(StringUtil::trim($_POST['licenseAccepted'])) == 1) return true;
			}
			$errorType = 'missingAcception';
		}

		$licenseText = $this->licenseTexts[$this->licenseShowLang];
		if (CHARSET != 'UTF-8') $licenseText = StringUtil::convertEncoding('UTF-8', CHARSET, $licenseText);

		WCF::getTPL()->assign(array(
			'licenseText' => $licenseText,
			'errorType' => $errorType
		));
		WCF::getTPL()->display('packageInstallationShowLicenseText');
		exit;
	}

	/**
	 * loads the installed languages in an array
	 *
	 */
	protected function loadInstalledLanguages() {
		foreach (Language::getLanguageCodes() as $languageID => $languageCode) {
			$this->installedLanguages[$languageCode] = $languageID;
		}
	}

	/**
	 * Extracts the licensetext file and returns it's
     * content. If the specified licensetext file
	 * was not found, an error message is thrown.
	 *
	 * @param	string				$licensetxtfile
	 * @return 	string				licensetext
	 */
	protected function readLicenseText($licensetxtfile) {
		// No <licensetexts>-tag in the instructions in package.xml
		if (!isset($licensetxtfile['cdata']) || !$licensetxtfile['cdata']) {
			return false;
		}
		// search licensetext files in package archive
		// throw error message if not found
		if (($fileIndex = $this->installation->getArchive()->getTar()->getIndexByFilename($licensetxtfile['cdata'])) === false) {
			//throw new wBB3ModsSystemException("license text file '".($licensetxtfile['cdata'])."' not found.", 10003);
			throw new SystemException("license text file '".($licensetxtfile['cdata'])."' not found.", 0);
		}

		$licensetext = $this->installation->getArchive()->getTar()->extractToString($fileIndex);
		return $licensetext;
	}
}
?>