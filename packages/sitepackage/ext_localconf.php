<?php

defined('TYPO3') or die('Access denied.');

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
