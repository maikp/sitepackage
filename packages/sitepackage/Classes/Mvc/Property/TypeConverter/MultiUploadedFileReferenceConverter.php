<?php
declare(strict_types=1);

namespace BrezoIt\Sitepackage\Mvc\Property\TypeConverter;

use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter;

/**
 * Converts an array of UploadedFile objects (multi upload) into an ObjectStorage of FileReference objects
 * by delegating each item to TYPO3's core UploadedFileReferenceConverter.
 */
final class MultiUploadedFileReferenceConverter extends AbstractTypeConverter
{
    /**
     * We accept an array (UploadedFile[])
     * and return an ObjectStorage<FileReference>.
     */
    protected array $sourceTypes = ['array'];
    protected string $targetType = ObjectStorage::class;
    protected int $priority = 50;

    public const OPTION_UPLOAD_FOLDER = 'uploadFolder';
    public const OPTION_UPLOAD_SEED = 'uploadSeed';

    /**
     * @param array $source Array of UploadedFile objects
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null
    ): ObjectStorage {
        if (!is_array($source)) {
            throw new TypeConverterException('Expected array for multi upload', 1735930001);
        }

        $uploadFolder = (string)($configuration?->getConfigurationValue(self::class, self::OPTION_UPLOAD_FOLDER) ?? '');
        $uploadSeed = (string)($configuration?->getConfigurationValue(self::class, self::OPTION_UPLOAD_SEED) ?? '');

        // Build a configuration for the core converter. We reuse the given configuration when possible,
        // but also inject the options for UploadedFileReferenceConverter so it behaves like the core single-upload.
        $coreConfiguration = $configuration;
        if ($coreConfiguration instanceof PropertyMappingConfiguration) {
            if ($uploadFolder !== '') {
                $coreConfiguration->setTypeConverterOption(
                    UploadedFileReferenceConverter::class,
                    UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER,
                    $uploadFolder
                );
            }
            if ($uploadSeed !== '') {
                $coreConfiguration->setTypeConverterOption(
                    UploadedFileReferenceConverter::class,
                    UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_SEED,
                    $uploadSeed
                );
            }
        }

        $core = GeneralUtility::makeInstance(UploadedFileReferenceConverter::class);

        $storage = new ObjectStorage();
        foreach ($source as $item) {
            if ($item === null) {
                continue;
            }

            // Support both UploadedFile objects and $_FILES-like arrays per item
            if (!$item instanceof UploadedFile && !is_array($item)) {
                continue;
            }

            /** @var FileReference|null $converted */
            $converted = $core->convertFrom($item, FileReference::class, [], $coreConfiguration);
            if ($converted instanceof FileReference) {
                $storage->attach($converted);
            }
        }

        return $storage;
    }
}
