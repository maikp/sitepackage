<?php

defined('TYPO3') or die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\Mvc\Property\PropertyMappingConfiguration::class] = [
        'className' => \BrezoIt\MultiFileUpload\Mvc\Property\PropertyMappingConfiguration::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Form\ViewHelpers\RenderFormValueViewHelper::class] = [
        'className' => \BrezoIt\MultiFileUpload\ViewHelpers\Form\RenderFormValueViewHelper::class,
    ];
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
