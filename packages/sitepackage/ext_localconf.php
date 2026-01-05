<?php

defined('TYPO3') or die('Access denied.');

(function () {
    // Add default RTE configuration
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['sitepackage'] = 'EXT:sitepackage/Configuration/RTE/Default.yaml';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\Mvc\Property\PropertyMappingConfiguration::class] = [
        'className' => \BrezoIt\Sitepackage\Mvc\Property\PropertyMappingConfiguration::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\ViewHelpers\RenderFormValueViewHelper::class] = [
        'className' => \BrezoIt\Sitepackage\ViewHelpers\Form\RenderFormValueViewHelper::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\Domain\Finishers\EmailFinisher::class] = [
        'className' => \BrezoIt\Sitepackage\Form\Finishers\EmailFinisher::class,
    ];

    // Form Setup registrieren
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:sitepackage/Configuration/Yaml/FormSetup.yaml'"
    );

    // Explizite YAML-Registrierung für Form-Modul
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        module.tx_form {
            settings {
                yamlConfigurations {
                    1670424038 = EXT:sitepackage/Configuration/Yaml/FormSetup.yaml
                }
            }
        }
        plugin.tx_form {
            settings {
                yamlConfigurations {
                    1670424038 = EXT:sitepackage/Configuration/Yaml/FormSetup.yaml
                }
            }
        }
    ');

})();
