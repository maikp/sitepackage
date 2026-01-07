<?php
declare(strict_types=1);

namespace BrezoIt\MultiFileUpload\Mvc\Property;

use BrezoIt\MultiFileUpload\Form\Elements\MultiImageUpload;
use BrezoIt\MultiFileUpload\Mvc\Property\TypeConverter\MultiUploadedFileReferenceConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Mvc\Property\PropertyMappingConfiguration as CorePropertyMappingConfiguration;

final class PropertyMappingConfiguration extends CorePropertyMappingConfiguration
{
    protected function adjustPropertyMappingForFileUploadsAtRuntime(FormRuntime $formRuntime, RenderableInterface $renderable): void
    {
        parent::adjustPropertyMappingForFileUploadsAtRuntime($formRuntime, $renderable);

        if (!$renderable instanceof MultiImageUpload) {
            return;
        }

        $formDefinition = $formRuntime->getFormDefinition();
        $processingRule = $formDefinition->getProcessingRule($renderable->getIdentifier());
        $pmc = $processingRule->getPropertyMappingConfiguration();
        $properties = $renderable->getProperties();
        $saveToFileMount = (string)($properties['saveToFileMount'] ?? '');

        $pmc->setTypeConverter(
            GeneralUtility::makeInstance(MultiUploadedFileReferenceConverter::class)
        );

        $pmc->setTypeConverterOption(
            MultiUploadedFileReferenceConverter::class,
            MultiUploadedFileReferenceConverter::OPTION_UPLOAD_FOLDER,
            $saveToFileMount
        );

        $pmc->setTypeConverterOption(
            MultiUploadedFileReferenceConverter::class,
            MultiUploadedFileReferenceConverter::OPTION_PROPERTY,
            $renderable->getIdentifier()
        );

        if ($formRuntime->canProcessFormSubmission()) {
            $formSessionIdentifier = $formRuntime->getFormSession()?->getIdentifier();
            if (!empty($formSessionIdentifier)) {
                $pmc->setTypeConverterOption(
                    MultiUploadedFileReferenceConverter::class,
                    MultiUploadedFileReferenceConverter::OPTION_UPLOAD_SEED,
                    $formSessionIdentifier
                );
            }
        }
    }
}
