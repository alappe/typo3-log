<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['backends'][] = 'Z7\\Log\\Backends\\RedisBackend';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['log']['backends'][] = 'Z7\\Log\\Backends\\NullBackend';

// Register status report…
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['log'][] = 'Z7\\Log\\Reports\\LogReport';

// Register the hook for SystemLog
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'][$_EXTKEY] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/SystemLog.php:Z7\\Log\\Hooks\\SystemLog->log';

// Register the hook for DevelopmentLog
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'][$_EXTKEY] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/DevelopmentLog.php:Z7\\Log\\Hooks\DevelopmentLog->log';
?>