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
        // Core-Upload-Logik für FileUpload/ImageUpload beibehalten
        parent::adjustPropertyMappingForFileUploadsAtRuntime($formRuntime, $renderable);

        // Unser eigenes Element zusätzlich wie ein Upload behandeln
        if (!$renderable instanceof MultiImageUpload) {
            return;
        }

        $formDefinition = $formRuntime->getFormDefinition();

        $processingRule = $formDefinition->getProcessingRule($renderable->getIdentifier());

        $pmc = $processingRule->getPropertyMappingConfiguration();

        $properties = $renderable->getProperties();
        $saveToFileMount = (string)($properties['saveToFileMount'] ?? '');

        // Multi upload: this ProcessingRule maps exactly one element.
        // The submitted value is an array with numeric keys (0,1,2,...) at the ROOT of this rule.
        // Therefore the allow/converter configuration must be applied to the root PM configuration.
        $pmc->allowAllProperties();
        $pmc->forProperty('*')->allowAllProperties();

        $pmc->setTypeConverter(
            GeneralUtility::makeInstance(MultiUploadedFileReferenceConverter::class)
        );

        // Pass upload folder from form element configuration
        $pmc->setTypeConverterOption(
            MultiUploadedFileReferenceConverter::class,
            MultiUploadedFileReferenceConverter::OPTION_UPLOAD_FOLDER,
            $saveToFileMount
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

        $pmc->forProperty('0')->allowAllProperties();
    }
}
