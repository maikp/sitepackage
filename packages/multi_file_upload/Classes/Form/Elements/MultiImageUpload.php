<?php

declare(strict_types=1);

namespace BrezoIt\MultiFileUpload\Form\Elements;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Form\Domain\Model\FormElements\AbstractFormElement;

final class MultiImageUpload extends AbstractFormElement
{
    public function initializeFormElement(): void
    {
        // Multi upload will be converted to an ObjectStorage of (pseudo) FileReference objects
        $this->setDataType(ObjectStorage::class);
        parent::initializeFormElement();
    }

    public function isMultiValue(): bool
    {
        return true;
    }
}
