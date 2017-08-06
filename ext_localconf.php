<?php
defined('TYPO3_MODE') or die('Access denied.');

\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep(PATH_site . 'typo3temp/var/mail');
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = 'mbox';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'] = PATH_site . 'typo3temp/var/mail/' . date('Y-m-d') . '.mbox';
