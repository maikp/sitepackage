<?php

declare(strict_types=1);

namespace BrezoIt\Sitepackage\ViewHelpers\Form;

use TYPO3\CMS\Form\Mvc\Property\TypeConverter\PseudoFileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Delete checkbox for one entry of a multi upload field (TYPO3 Form) using PseudoFileReference.
 * Identifier is sys_file.uid of the uploaded file.
 */
final class MultiUploadDeleteCheckboxViewHelper extends AbstractTagBasedViewHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->tagName = 'input';
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('id', 'string', 'ID of the generated checkbox element');
        $this->registerArgument('property', 'string', 'Name of the (form) property', true);
        $this->registerArgument('fileReference', PseudoFileReference::class, 'The pseudo file reference object', true);
    }

    public function render(): string
    {
        $property = (string)$this->arguments['property'];
        $idAttribute = (string)($this->arguments['id'] ?? '');

        $fileReference = $this->arguments['fileReference'];
        if (!$fileReference instanceof PseudoFileReference) {
            return '';
        }

        $fileUid = $this->deriveFileUid($fileReference);
        if ($fileUid === null) {
            return '';
        }

        $nameAttribute = $property . '__delete[' . $fileUid . ']';

        // Hidden ensures we get a submitted value even if unchecked
        $hidden = '<input type="hidden" name="' . htmlspecialchars($nameAttribute, ENT_QUOTES) . '" value="0" />';

        $this->tag->addAttribute('type', 'checkbox');
        $this->tag->addAttribute('name', $nameAttribute);
        $this->tag->addAttribute('value', '1');
        if ($idAttribute !== '') {
            $this->tag->addAttribute('id', $idAttribute);
        }

        return $hidden . $this->tag->render();
    }

    private function deriveFileUid(PseudoFileReference $fileReference): ?int
    {
        if (!method_exists($fileReference->getOriginalResource(), 'getOriginalFile')) {
            return null;
        }

        $originalFile = $fileReference->getOriginalResource()->getOriginalFile();
        if (!is_object($originalFile) || !method_exists($originalFile, 'getUid')) {
            return null;
        }

        $uid = (int)$originalFile->getUid();
        return $uid > 0 ? $uid : null;
    }
}
