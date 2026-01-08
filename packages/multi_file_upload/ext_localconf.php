<?php

defined('TYPO3') or die();

(function () {
    // Register hooks for MultiFile property mapping configuration
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterBuildingFinished'][1670424038]
        = \BrezoIt\MultiFileUpload\Mvc\Property\MultiFilePropertyMappingConfiguration::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterFormStateInitialized'][1670424038]
        = \BrezoIt\MultiFileUpload\Mvc\Property\MultiFilePropertyMappingConfiguration::class;

    // XCLASS override for EmailFinisher (multi-file attachments)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\Domain\Finishers\EmailFinisher::class] = [
        'className' => \BrezoIt\MultiFileUpload\Form\Finishers\EmailFinisher::class,
    ];

    // Form Setup registrieren
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:multi_file_upload/Configuration/Yaml/FormSetup.yaml'"
    );

    // Explizite YAML-Registrierung für Form-Modul
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        module.tx_form {
            settings {
                yamlConfigurations {
                    1670424038 = EXT:multi_file_upload/Configuration/Yaml/FormSetup.yaml
                }
            }
        }
        plugin.tx_form {
            settings {
                yamlConfigurations {
                    1670424038 = EXT:multi_file_upload/Configuration/Yaml/FormSetup.yaml
                }
            }
        }
    ');

})();
