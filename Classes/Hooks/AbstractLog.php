<?php
namespace Z7\Log\Hooks;

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
 * The hook to capture devlog…
 *
 * @package log
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractLog implements \TYPO3\CMS\Core\SingletonInterface {

	protected $severityDescription = array(
		0 => 'info',
		1 => 'notice',
		2 => 'warning',
		3 => 'fatal error',
		4 => 'ok'
	);

	/**
	 * Human readable description of severity 4
	 *
	 * @var \string
	 */
	const SEVERITY_DESCRIPTION_NEG1 = 'ok';

	/**
	 * The extension key…
	 *
	 * @var \string
	 */
	const EXT_KEY = 'log';

	/**
	 * Extension settings
	 *
	 * @var \array
	 */
	protected $settings;

	/**
	 * Pass in the severity and return the description…
	 *
	 * @param \integer $severity
	 * @return \string
	 */
	public function getSeverityDescription($severity) {
		// Because we cannot use -1 in the array
		if ($severity === -1) {
			$severity = 4;
		}

		return $this->severityDescription[$severity];
	}

	/**
	 * Get the TYPO3 Sitename
	 *
	 * @return \string
	 */
	public function getSitename() {
		return $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
	}

	/**
	 * Get the TYPO3 version
	 *
	 * @return \string
	 */
	public function getSystemVersion() {
		return TYPO3_version;
	}

	/**
	 * Return the current DateTime in ISO8601 format
	 *
	 * @return \string
	 */
	public function getTime() {
		$now = new \DateTime('now');
		return $now->format(\DateTime::ISO8601);
	}
}
?>