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
class SystemLog extends AbstractLog {

	/**
	 * @var \Z7\Log\Backends\LogBackendInterface
	 */
	protected $backend;

	/**
	 * Initialize…
	 *
	 * @param \Z7\Log\Backends\LogBackendInterface $backend optional
	 * @return \void
	 */
	public function __construct(\Z7\Log\Backends\LogBackendInterface $backend = NULL) {
		$this->backend = ($backend !== NULL) ? $backend : \Z7\Log\Backends\LogBackendFactory::getBackend('systemLog');
	}

	/**
	 * Log the passed in data…
	 *
	 * @param \array $data
	 * @return \void
	 */
	public function log(array $data) {
		// Logstash uses message, and it's the better key anyway…
		$data['message'] = ($data['msg'] === NULL) ? '' : $data['msg'];
		unset($data['msg']);

		$data['severityDescription'] = $this->getSeverityDescription($data['severity']);
		$data['siteName'] = $this->getSitename();
		$data['typo3Version'] = $this->getSystemVersion();
		$data['time'] = $this->getTime();
		$data['tags'] = array('sys', 'syslog');

		$data['backTrace'] = ($data['backTrace'] !== NULL) ? (array) $data['backTrace'] : NULL;

		if ($this->backend !== NULL) {
			$json = json_encode($data);
			$this->backend->log($json);
		}
	}
}
?>