<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * Shows a ShowLicense Button on PackageViewPage if necessary.
 *
 * @author		Markus Bartz <roul@codingcorner.info>
 * @copyright	2009 RouLs Coding Corner
 * @license		GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		com.woltlab.community.roul.pip.showlicense
 * @subpackage	system.event.listener
 * @category 	Show License
 */
class PackageViewPageShowLicenseListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventObj->packageID) {
			// look if there are licensetexts available for the package
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_package_installation_licensetext
				WHERE	packageID = ".$eventObj->packageID;
			$licenseTextsCount = WCF::getDB()->getFirstRow($sql);
			if ($licenseTextsCount['count'] > 0)
			{
				// Show licensebutton
				WCF::getTPL()->append('additionalLargeButtons','<li><a href="index.php?page=PackageShowLicenseText&amp;activePackageID='.$eventObj->packageID.'&amp;packageID='.PACKAGE_ID.SID_ARG_2ND.'"><img src="'.RELATIVE_WCF_DIR.'icon/packageLicenseTextM.png" alt="" /> <span>'.WCF::getLanguage()->get('wcf.acp.package.view.button.showlicensetext').'</span></a></li>');
			}
		}
	}
}
?>