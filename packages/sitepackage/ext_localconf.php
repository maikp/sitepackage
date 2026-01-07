<?php

defined('TYPO3') or die('Access denied.');

(function () {
    // Add default RTE configuration
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['sitepackage'] = 'EXT:sitepackage/Configuration/RTE/Default.yaml';

})();
