<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows a ShowLicense Button on PackageViewPage if necessary.
 *
 * @author	Markus Bartz
 * @package	de.wbb3mods.wcf.pip.showlicense
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
				// tell the template that there are licensetexts, so that the button will be shown
				WCF::getTPL()->assign(array(
					'hasLicenseText' => true
				));
			}
		}
	}
}
?>