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
use Psr\Http\Message\ServerRequestInterface;

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
    public const OPTION_PROPERTY = 'property';

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
        $propertyName = (string)($configuration?->getConfigurationValue(self::class, self::OPTION_PROPERTY) ?? '');

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

        // Apply deletion flags from POST (e.g. imageupload-1__delete[123]=1)
        $deleteFileUids = $this->getDeleteFileUids($propertyName);
        $deleteFileUids = $this->getDeleteFileUids($propertyName);
        if ($deleteFileUids !== []) {
            // WICHTIG: Erst alle zu löschenden Refs sammeln
            $toRemove = [];

            foreach ($storage as $ref) {
                $uid = 0;

                // PseudoFileReference path
                if (is_object($ref) && method_exists($ref, 'getOriginalFile')) {
                    $originalFile = $ref->getOriginalFile();
                    if (is_object($originalFile) && method_exists($originalFile, 'getUid')) {
                        $uid = (int)$originalFile->getUid();
                    }
                }

                // Extbase FileReference path (if ever used)
                if ($uid === 0 && $ref instanceof FileReference) {
                    $originalResource = $ref->getOriginalResource();
                    if (is_object($originalResource) && method_exists($originalResource, 'getOriginalFile')) {
                        $originalFile = $originalResource->getOriginalFile();
                        if (is_object($originalFile) && method_exists($originalFile, 'getUid')) {
                            $uid = (int)$originalFile->getUid();
                        }
                    }
                }

                if ($uid > 0 && isset($deleteFileUids[$uid])) {
                    $toRemove[] = $ref;  // ← Nur sammeln, nicht sofort detachen!
                }
            }

            // DANN erst alle gesammelten Refs löschen
            foreach ($toRemove as $ref) {
                $storage->detach($ref);
            }
        }

        return $storage;
    }
    /**
     * Returns a map of sys_file uids to delete, based on POST fields named "<property>__delete[<uid>]".
     * Example: imageupload-1__delete[123]=1
     *
     * @return array<int,true>
     */
    private function getDeleteFileUids(string $propertyName): array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return [];
        }

        $body = (array)$request->getParsedBody();

        // If propertyName is not configured, try to auto-detect a single "__delete" key.
        if ($propertyName === '') {
            $candidates = [];
            foreach (array_keys($body) as $key) {
                if (is_string($key) && str_ends_with($key, '__delete')) {
                    $candidates[] = $key;
                }
            }
            if (count($candidates) === 1) {
                $propertyName = substr($candidates[0], 0, -strlen('__delete'));
            }
        }

        if ($propertyName === '') {
            return [];
        }

        $deleteMap = (array)($body[$propertyName . '__delete'] ?? []);
        $uids = [];
        foreach ($deleteMap as $fileUid => $flag) {
            if ((int)$flag === 1) {
                $uid = (int)$fileUid;
                if ($uid > 0) {
                    $uids[$uid] = true;
                }
            }
        }

        return $uids;
    }
}
