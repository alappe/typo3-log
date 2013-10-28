<?php
namespace Z7\Log\Reports;

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
 * BE report
 *
 * @package log
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LogReport implements \TYPO3\CMS\Reports\StatusProviderInterface {

	/**
	 * Extension settings
	 *
	 * @var \array
	 */
	protected $settings = array();

	/**
	 * All backends
	 *
	 * @var \array
	 */
	protected $backends = array();

	/**
	 * Initialize…
	 *
	 * @return \void
	 */
	public function __construct() {
		$this->settings = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['settings'];
		$this->backends = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['backends'];
	}

	/**
	 * Compiles a collection of system status checks as a status report.
	 *
	 * @return \array
	 */
	public function getStatus() {
		$reports = array();
		$reports = $this->addDevelopmentLogReport($reports);
		$reports = $this->addSystemLogReport($reports);
		$reports = $this->addBackendReports($reports);

		return $reports;
	}

	/**
	 * Add the devLog report if devLog is enabled…
	 *
	 * @param \array $reports
	 * @return \array
	 */
	public function addDevelopmentLogReport($reports) {
		if (TRUE === $this->settings['developmentLog']['enabled']) {
			array_push($reports, $this->checkIfDevelopmentLogConnectionWorks());
		}

		return $reports;
	}

	/**
	 * Add the sysLog report if sysLog is enabled…
	 *
	 * @param \array $reports
	 * @return \array
	 */
	public function addSystemLogReport($reports) {
		if (TRUE === $this->settings['systemLog']['enabled']) {
			array_push($reports, $this->checkIfSystemLogConnectionWorks());
		}

		return $reports;
	}

	/**
	 * Add backend reports for all available backends…
	 *
	 * @param \array $reports
	 * @return \array
	 */
	public function addBackendReports($reports) {
		foreach ($this->backends as $backendName) {
			$backendSettings = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['settings'][$backendName];
			$backend = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($backendName, $backendSettings);
			array_push($reports, $backend->getReport());
		}

		return $reports;
	}

	/**
	 * Check if the connection to the DevelopmentLog works…
	 *
	 * @return \TYPO3\CMS\Reports\Status
	 */
	protected function checkIfDevelopmentLogConnectionWorks() {
		$backend = \Z7\Log\Backends\LogBackendFactory::getBackend('developmentLog');
		$pong = $backend->connect()->ping();
		$message = 'Using ' . $backend->getName();

		if (TRUE === $pong) {
			$value = 'DevelopmentLog Connection seems to work fine…';
			$status = \TYPO3\CMS\Reports\Status::OK;
		} else {
			$value = 'DevelopmentLog Connection failed…';
			$status = \TYPO3\CMS\Reports\Status::ERROR;
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Reports\\Status', 'DevelopmentLog', $value, $message, $status);
	}

	/**
	 * Check if the connection to the SystemLog works…
	 *
	 * @return \TYPO3\CMS\Report\Status
	 */
	protected function checkIfSystemLogConnectionWorks() {
		$backend = \Z7\Log\Backends\LogBackendFactory::getBackend('systemLog');
		$pong = $backend->connect()->ping();
		$message = 'Using ' . $backend->getName();

		if (TRUE === $pong) {
			$value = 'SystemLog Connection seems to work fine…';
			$status = \TYPO3\CMS\Reports\Status::OK;
		} else {
			$value = 'SystemLog Connection failed…';
			$status = \TYPO3\CMS\Reports\Status::ERROR;
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Reports\\Status', 'SystemLog', $value, $message, $status);
	}
}
?>