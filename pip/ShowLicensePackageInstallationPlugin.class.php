<?php
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractPackageInstallationPlugin.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * This PIP shows a licensetext for the plugin.
 *
 * @author		Markus Bartz <roul@codingcorner.info>
 * @copyright	2009 RouLs Coding Corner
 * @license		GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		com.woltlab.community.roul.pip.showlicense
 * @subpackage	acp.package.plugin
 * @category 	Show License
 */
class ShowLicensePackageInstallationPlugin extends AbstractPackageInstallationPlugin {
	public $tagName = 'licensetexts';
	public $tableName = 'package_installation_licensetext';
	public $licenseTexts = array();
	
	public $defaultLanguage = '';
	public $installedLanguages = array();

	/**
	 * @see	 PackageInstallationPlugin::install()
	 */
	public function install() {
		$instructions = $this->installation->getInstructions();
		if(isset($instructions[$this->tagName]['cdata'])) $licenseTextFiles = array($instructions[$this->tagName]);
		else $licenseTextFiles = $instructions[$this->tagName];

		$this->loadInstalledLanguages();
		foreach ($licenseTextFiles as $licenseTextFile) {
			if ($licenseText = $this->readLicenseText($licenseTextFile)) {
				// check required attributes
				if (!isset($licenseTextFile['languagecode'])) {
					throw new SystemException("required 'languagecode' attribute for 'licensetexts' tag is missing in '".PackageArchive::INFO_FILE."'");
				}
				// check language encoding
				if (!Language::isSupported($licenseTextFile['languagecode'])) {
					// unsupported encoding
					continue;
				}
				
				$default = 0;
				if (isset($licenseTextFile['default'])) $default = $licenseTextFile['default'];
				
				if (array_key_exists($licenseTextFile['languagecode'], $this->installedLanguages)) {
					$this->licenseTexts[$licenseTextFile['languagecode']] = array(
						'languageID' => $this->installedLanguages[$licenseTextFile['languagecode']],
						'licenseText' => $licenseText
					);
					if ($default == 1) $this->defaultLanguage = $licenseTextFile['languagecode'];
				}
			}
		}

		if (count($this->licenseTexts) < 1) {
			throw new SystemException("no license informations in your supported languages available in '".PackageArchive::INFO_FILE."'", 0);
		}
		
		if ($this->defaultLanguage == '') {
			if (isset($this->licenseTexts[WCF::getLanguage()->getLanguageCode()])) $this->defaultLanguage = WCF::getLanguage()->getLanguageCode();
			if ($this->defaultLanguage == '' && WCF::getLanguage()->getLanguageCode() == 'de-informal' && isset($this->licenseTexts['de'])) $this->defaultLanguage = 'de'; 
			if (isset($this->licenseTexts['en'])) $this->defaultLanguage = 'en';
		}

		$this->promptLicenseConfirmation();

		$itemInserts = '';
		foreach ($this->licenseTexts as $languageCode => $licenseData) {
			if (!empty($itemInserts)) $itemInserts .= ',';
			$itemInserts .= "(
				".intval($this->installation->getPackageID()).",
				".intval($licenseData['languageID']).",
				".(($this->defaultLanguage == $languageCode) ? (1) : (0)).",
				'".escapeString($licenseData['licenseText'])."'
			)";
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
	 * Prompts for license confirmation
	 *
	 * @return	bool
	 */
	protected function promptLicenseConfirmation() {
		$errorType = '';
		$languageCode = $this->defaultLanguage;
		if (isset($this->licenseTexts[WCF::getLanguage()->getLanguageCode()])) $languageCode = WCF::getLanguage()->getLanguageCode();
		if (WCF::getLanguage()->getLanguageCode() == 'de-informal' && $languageCode != 'de-informal' && isset($this->licenseTexts['de'])) $languageCode = 'de';
		 
		if (isset($_POST['send'])) {
			if (isset($_POST['languageCode'])) {
				if (array_key_exists($_POST['languageCode'], $this->licenseTexts)) $languageCode = $_POST['languageCode']; 
			}
			if (!isset($_POST['langChooser'])) {
				if (isset($_POST['licenseAccepted'])) {
					if (intval(StringUtil::trim($_POST['licenseAccepted'])) == 1) return true;
				}
				$errorType = 'missingAcception';
			}
		}

		$availableLanguages = array();
		foreach ($this->licenseTexts as $langCode => $value) {
			if ($languageCode == '') $languageCode = $langCode;
			$availableLanguages[$langCode] = WCF::getLanguage()->get('wcf.global.language.'.$langCode).' ('.$langCode.')';
		}
		
		$licenseText = $this->licenseTexts[$languageCode]['licenseText'];
		if (CHARSET != 'UTF-8') $licenseText = StringUtil::convertEncoding('UTF-8', CHARSET, $licenseText);

		WCF::getTPL()->assign(array(
			'licenseText' => $licenseText,
			'languageCode' => $languageCode,
			'availableLanguages' => $availableLanguages,
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
			throw new SystemException("license text file '".($licensetxtfile['cdata'])."' not found.", 0);
		}

		$licensetext = $this->installation->getArchive()->getTar()->extractToString($fileIndex);
		return $licensetext;
	}
}
?>