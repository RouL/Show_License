<?php
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/acp/package/Package.class.php');

/**
 * Shows the licensetext of a package
 *
 * @author		Markus Bartz <roul@codingcorner.info>
 * @copyright	2011 Markus Bartz
 * @license		GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		com.woltlab.community.roul.pip.showlicense
 * @subpackage	acp.page
 * @category 	Show License
 */
class PackageShowLicenseTextPage extends AbstractPage {
	public $package;
	public $activePackageID = 0;
	public $templateName = 'packageShowLicenseText';
	public $licenseTexts = array();
	public $languageCode = '';
	public $availableLanguages = array();
	public $defaultLanguage = '';

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['activePackageID'])) $this->activePackageID = intval($_REQUEST['activePackageID']);
		if (isset($_REQUEST['languageCode'])) $this->languageCode = StringUtil::trim($_REQUEST['languageCode']);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get package data and licensetexts
		try {
			$this->package = new Package($this->activePackageID);
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_package_installation_licensetext
				WHERE	packageID = ".$this->activePackageID;
			WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows() < 1) throw new SystemException();
			while ($licenseText = WCF::getDB()->fetchArray()) {
				$language = WCF::getLanguage()->getLanguage($licenseText['languageID']);
				$this->licenseTexts[$language['languageCode']] = $licenseText['licenseText'];
				if ($licenseText['defaultLang']) $this->defaultLanguage = $language['languageCode'];
			}
			
			$languageCode = $this->defaultLanguage;
			if (isset($this->licenseTexts[WCF::getLanguage()->getLanguageCode()])) $languageCode = WCF::getLanguage()->getLanguageCode();
			if (WCF::getLanguage()->getLanguageCode() == 'de-informal' && $languageCode != 'de-informal' && isset($this->licenseTexts['de'])) $languageCode = 'de';
			
			$this->availableLanguages = array();
			foreach ($this->licenseTexts as $langCode => $value) {
				if ($languageCode == '') $languageCode = $langCode;
				$this->availableLanguages[$langCode] = WCF::getLanguage()->get('wcf.global.language.'.$langCode).' ('.$langCode.')';
			}
			
			if ($this->languageCode == '') $this->languageCode = $languageCode;
			
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
			'languageCode' => $this->languageCode,
			'availableLanguages' => $this->availableLanguages,
			'licenseText' => $this->licenseTexts[$this->languageCode],
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