<?php
namespace Z7\Log\Backends;
use \TYPO3\CMS\Core\Utility\GeneralUtility as GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Lappe <nd@zimmer7.com>, zimmer7
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Factory for LogBackends
 *
 * @package log
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LogBackendFactory {

	/**
	 * Contains all active backends by type…
	 *
	 * @var \array<\Z7\Log\Backends\LogBackendInterface>
	 */
	protected static $backends = array();

	/**
	 * Initialize by returning an instance
	 * of the configured Backend.
	 *
	 * TODO Add exception if no backend is configured
	 * TODO Check if backend implements the interface…
	 *
	 * @param \string $type unique identifier, e.g. systemLog, developmentLog, depreciationLog…
	 * @return \Z7\Log\Backends\LogBackendInterface
	 */
	public static function getBackend($type) {
		if (NULL === self::$backends[$type]) {
			$backendName = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['settings'][$type]['backend'];
			if (NULL !== $backendName) {
				$backendSettings = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['settings'][$backendName];
				self::$backends[$type] = GeneralUtility::makeInstance($backendName, $backendSettings);
			}
		}

		return self::$backends[$type];
	}
}
?>