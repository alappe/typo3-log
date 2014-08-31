<?php
namespace Z7\Log\Backends;

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
 * Redis Backend
 */
class RedisBackend implements LogBackendInterface, \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * var \array
	 */
	protected $settings;

	/**
	 * @var \Redis
	 */
	protected $connection = NULL;

	/**
	 * Initialize
	 *
	 * @param \array $settings
	 * @return \void
	 */
	public function __construct($settings = array()) {
		$this->settings = $settings;
		$this->connect();
	}

	/**
	 * Initialize the connection, then return this backend for further usage.
	 *
	 * @return \Z7\Log\Backends\RedisBackend
	 */
	public function connect() {
		if ($this->connection === NULL) {
			$this->connection = new \Redis();
			$this->connection->connect($this->settings['host'], $this->settings['port']);
			$this->connection->select($this->settings['database']);
		}

		return $this;
	}

	/**
	 * Ping redis…
	 *
	 * @return \boolean
	 */
	public function ping() {
		$result = FALSE;

		try {
			$answer = $this->connection->ping();
			if ($answer === '+PONG') {
				$result = TRUE;
			}
		} catch (\RedisException $e) {
			// Omitted, because we already default to FALSE;
		}

		return $result;
	}

	/**
	 * Return a report about the connection
	 * to redis…
	 *
	 * @return \TYPO3\CMS\Report\Status
	 */
	public function getReport() {
		if (FALSE === class_exists('\\Redis')) {
			$value = 'Redis PHP Extension not found…';
			$message = 'This backend cannot be used, please install phpredis first…';
			$status = \TYPO3\CMS\Reports\Status::WARNING;
		} else {
			// Try to connect to redis and gather information…
			if (FALSE === $this->connect()->ping()) {
				$value = 'Redis PHP Extension found but connection failed';
				$message = 'Check the configured host and port…';
				$status = \TYPO3\CMS\Reports\Status::WARNING;
			} else {
				$info = $this->connection->info();
				$message = '<p>Server Informations:</p>'
					. '<ul>'
					. '<li>Version: ' . $info['redis_version'] . '</li>'
					. '<li>Mode: ' . $info['redis_mode'] . '</li>'
					. '<li>OS: ' . $info['os'] . '</li>'
					. '<li>Uptime: ' . round($info['uptime_in_seconds'] / 3600, 1) . ' hours</li>'
					. '</ul>';
				$value = 'All looks fine!';
				$status = \TYPO3\CMS\Reports\Status::OK;
			}
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Reports\\Status', 'Redis Backend', $value, $message, $status);
	}

	/**
	 * Return an identifier to this backend…
	 *
	 * @return \string
	 */
	public function getName() {
		return 'Redis Backend';
	}

	/**
	 * Log a passed in message
	 *
	 * @param \string $message
	 * @return \boolean
	 */
	public function log($message) {
		return $this->connection->rPush($this->settings['namespace'], $message);
	}
}
?>