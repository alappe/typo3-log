<?php
namespace Z7\Log\Tests;
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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Z7\Log\Hooks\SystemLog.
 */
class SystemLogTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	public function getBackendMock($callback) {
		$backendMock = $this->getMock('\Z7\Log\Backends\NullBackend', array('log'), array());
		$backendMock->expects($this->once())
			->method('log')
			->with($this->callback($callback));
		return $backendMock;
	}

	/**
	 * @test
	 */
	public function logForDataContainingBacktraceSerializesBacktrace() {
		$backendMock = $this->getBackendMock(function($subject) {
			$data = json_decode($subject);
			$this->assertTrue($data->backTrace !== NULL);
			return TRUE;
		});

		$subject = new \Z7\Log\Hooks\SystemLog($backendMock);
		$subject->log(array(
			'message' => 'some message',
			'severity' => 3,
			'backTrace' => debug_backtrace()
		));
	}

	/**
	 * @test
	 */
	public function logAttachesSyslogTags() {
		$backendMock = $this->getBackendMock(function($subject) {
			$this->assertContains('sys', json_decode($subject)->tags);
			$this->assertContains('syslog', json_decode($subject)->tags);
			return TRUE;
		});

		$subject = new \Z7\Log\Hooks\SystemLog($backendMock);
		$subject->log(array(
			'message' => 'some message',
			'severity' => 3
		));
	}
}
?>