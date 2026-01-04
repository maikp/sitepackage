<?php
declare(strict_types=1);

namespace BrezoIt\Sitepackage\Mvc\Property;

use BrezoIt\Sitepackage\Form\Elements\MultiImageUpload;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Mvc\Property\PropertyMappingConfiguration as CorePropertyMappingConfiguration;
use BrezoIt\Sitepackage\Mvc\Property\TypeConverter\MultiUploadedFileReferenceConverter;

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

        $field = $renderable->getIdentifier();

        // IMPORTANT: Use the runtime processing rule from FormState (core behavior)
        $processingRule = null;

        // TYPO3 v13.4: Processing rules live on the form definition (root renderable).
        $formDefinition = $formRuntime->getFormDefinition();
        if (method_exists($formDefinition, 'getProcessingRule')) {
            $processingRule = $formDefinition->getProcessingRule($field);
        }

        // Optional fallback (falls $field nicht passt / API-Variante abweicht)
        if ($processingRule === null && method_exists($renderable->getRootForm(), 'getProcessingRule')) {
            $processingRule = $renderable->getRootForm()->getProcessingRule($field);
        }

        if ($processingRule === null) {
            return;
        }

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
