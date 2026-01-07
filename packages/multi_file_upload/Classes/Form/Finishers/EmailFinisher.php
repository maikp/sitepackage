<?php

declare(strict_types=1);

namespace BrezoIt\MultiFileUpload\Form\Finishers;

use BrezoIt\MultiFileUpload\Form\Elements\MultiImageUpload;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher as CoreEmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;
use Symfony\Component\Mime\Address;

/**
 * Extended EmailFinisher that supports MultiImageUpload elements.
 *
 * This finisher extends the core EmailFinisher to handle multiple file uploads
 * from MultiImageUpload form elements as email attachments.
 */
class EmailFinisher extends CoreEmailFinisher
{
    protected function executeInternal(): void
    {
        $languageBackup = null;
        // Flexform overrides write strings instead of integers so
        // we need to cast the string '0' to false.
        if (
            isset($this->options['addHtmlPart'])
            && $this->options['addHtmlPart'] === '0'
        ) {
            $this->options['addHtmlPart'] = false;
        }

        $subject = (string)$this->parseOption('subject');
        $recipients = $this->getRecipients('recipients');
        $senderAddress = $this->parseOption('senderAddress');
        $senderAddress = is_string($senderAddress) ? $senderAddress : '';
        $senderName = $this->parseOption('senderName');
        $senderName = is_string($senderName) ? $senderName : '';
        $replyToRecipients = $this->getRecipients('replyToRecipients');
        $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients');
        $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients');
        $addHtmlPart = $this->parseOption('addHtmlPart') ? true : false;
        $attachUploads = $this->parseOption('attachUploads');
        $title = (string)$this->parseOption('title') ?: $subject;

        if ($subject === '') {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipients)) {
            throw new FinisherException('The option "recipients" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $formRuntime = $this->finisherContext->getFormRuntime();

        $translationService = GeneralUtility::makeInstance(TranslationService::class);
        if (is_string($this->options['translation']['language'] ?? null) && $this->options['translation']['language'] !== '') {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }

        $mail = $this
            ->initializeFluidEmail($formRuntime)
            ->from(new Address($senderAddress, $senderName))
            ->to(...$recipients)
            ->subject($subject)
            ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
            ->assign('title', $title);

        if (!empty($replyToRecipients)) {
            $mail->replyTo(...$replyToRecipients);
        }

        if (!empty($carbonCopyRecipients)) {
            $mail->cc(...$carbonCopyRecipients);
        }

        if (!empty($blindCarbonCopyRecipients)) {
            $mail->bcc(...$blindCarbonCopyRecipients);
        }

        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        if ($attachUploads) {
            $this->attachUploads($mail, $formRuntime);
        }

        GeneralUtility::makeInstance(MailerInterface::class)->send($mail);
    }

    /**
     * Attach uploaded files to the email, including multi-file uploads.
     */
    protected function attachUploads(FluidEmail $mail, FormRuntime $formRuntime): void
    {
        foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
            if ($element instanceof MultiImageUpload) {
                $files = $formRuntime[$element->getIdentifier()];
                if (is_iterable($files)) {
                    foreach ($files as $file) {
                        $this->attachFile($mail, $file);
                    }
                }
            } elseif ($element instanceof FileUpload) {
                $file = $formRuntime[$element->getIdentifier()];
                $this->attachFile($mail, $file);
            }
        }
    }

    /**
     * Attach a single file to the email.
     */
    protected function attachFile(FluidEmail $mail, mixed $file): void
    {
        if (!$file) {
            return;
        }

        if ($file instanceof FileReference) {
            $file = $file->getOriginalResource();
        }

        $mail->attach($file->getContents(), $file->getName(), $file->getMimeType());
    }
}
