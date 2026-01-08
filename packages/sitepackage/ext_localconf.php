<?php

defined('TYPO3') or die('Access denied.');

(function () {
    // Add default RTE configuration
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['sitepackage'] = 'EXT:sitepackage/Configuration/RTE/Default.yaml';

    // Register form configuration
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        'module.tx_form.settings.yamlConfigurations.1000 = EXT:sitepackage/Configuration/Yaml/FormSetup.yaml'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        'plugin.tx_form.settings.yamlConfigurations.1000 = EXT:sitepackage/Configuration/Yaml/FormSetup.yaml'
    );
})();
