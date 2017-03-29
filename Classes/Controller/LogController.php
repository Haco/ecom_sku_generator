<?php
namespace S3b0\EcomSkuGenerator\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * LogController
 */
class LogController extends \S3b0\EcomSkuGenerator\Controller\GeneratorController
{

    /**
     * action confirmation
     *
     * @return void
     */
    public function confirmationAction()
    {
        $this->view->assign('message', $this->contentRepository->findByUid((int)$this->settings['mail']['confirmationMessage']));
    }

    /**
     * action new
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Log $newLog
     * @ignorevalidation $newLog
     * @return void
     */
    public function newAction(\S3b0\EcomSkuGenerator\Domain\Model\Log $newLog = null)
    {
        $configuration = $this->feSession->get('config') ?: [];
        $minOrderQuantity = $this->feSession->get('min-order-quantity') ?: 0;

        if (!sizeof($configuration)) {
            $this->forward('index', 'Generator');
        }

        $data = $this->getIndexActionData();

        if ($data['progress'] < 1) {
            $this->redirect('index', 'Generator');
        }

        $this->view->assignMultiple([
            'newLog' => $newLog,
            'minOrderQuantity' => $minOrderQuantity,
            'countryList' => $this->regionRepository->findByType(0),
            'stateList' => $this->stateRepository->findAll()
        ]);
    }

    /**
     * action initializeCreate
     */
    protected function initializeCreateAction()
    {
        $propertyMappingConfiguration = $this->arguments['newLog']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowProperties('country');
        $propertyMappingConfiguration->allowProperties('state');
    }

    /**
     * action create
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Log $newLog
     * @return void
     */
    public function createAction(\S3b0\EcomSkuGenerator\Domain\Model\Log $newLog)
    {
        /*
            Creates new log record based on configuration
            If configuration is found in session
         */
        $configuration = $this->feSession->get('config') ?: [];
        if (sizeof($configuration)) {
            $this->addConfigurationToLog($newLog, $configuration);
        } else {
            $this->forward('index', 'Generator');
        }
        $this->createRecord($newLog);

        /*
            Sends confirmation email to requesting user and to sales persons
         */
        $noReply = null;
        $configurations = $this->configurationRepository->findByConfigurationArray($configuration);
        $data = $this->getSku($configurations, $configuration);
        if ($this->settings['mail']['noReplyEmail'] && GeneralUtility::validEmail($this->settings['mail']['noReplyEmail']) && $this->settings['mail']['senderName']) {
            $noReply = [$this->settings['mail']['noReplyEmail'] => $this->settings['mail']['senderName']];
        }
        if ($this->settings['mail']['senderEmail'] && GeneralUtility::validEmail($this->settings['mail']['senderEmail']) && $this->settings['mail']['senderName']) {
            $sender = [$this->settings['mail']['senderEmail'] => $this->settings['mail']['senderName']];
        } else {
            $sender = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
        }
        $carbonCopyReceivers = [];
        if ($this->settings['mail']['carbonCopy']) {
            foreach (explode(',', $this->settings['mail']['carbonCopy']) as $carbonCopyReceiver) {
                $tokens = GeneralUtility::trimExplode(' ', $carbonCopyReceiver, true, 2);
                if (GeneralUtility::validEmail($tokens[0])) {
                    $carbonCopyReceivers[$tokens[0]] = $tokens[1];
                }
            }
        }
        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mailToSender */
        $mailToSender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
        $mailToSender->setContentType('text/html');

        /**
         * Email to sender
         */
        $mailToSender->setFrom($noReply ?: $sender)
            ->setTo([$newLog->getEmail() => "{$newLog->getFirstName()} {$newLog->getLastName()}"])
            ->setSender('noreply@ecom-ex.com', 'ecom instruments')
            ->setSubject($this->settings['mail']['senderSubject'] ?: LocalizationUtility::translate('mail.toSender.subject', 'ecom_config_code_generator', [$this->contentObject->getHeader()]))
            ->setBody($this->getStandAloneTemplate('Email/ToSender', [
                'title' => $data['title'],
                'configurationCode' => $data,
                'log' => $newLog
            ]))
            ->send();

        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mailToReceiver */
        $mailToReceiver = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
        $mailToReceiver->setContentType('text/html');

        /**
         * Email to receiver
         */
        $mailToReceiver->setFrom([$newLog->getEmail() => "{$newLog->getFirstName()} {$newLog->getLastName()}"])
            ->setCc($carbonCopyReceivers)
            ->setTo($sender)
            ->setSender('noreply@ecom-ex.com', 'ecom instruments')
            ->setSubject($this->settings['mail']['receiverSubject'] ?: LocalizationUtility::translate('mail.toReceiver.subject', 'ecom_config_code_generator', [$this->contentObject->getHeader()]))
            ->setBody($this->getStandAloneTemplate('Email/ToReceiver', [
                'title' => $data['title'],
                'configurationCode' => $data,
                'log' => $newLog,
                'marketingInformation' => SessionUtility::getMarketingInfos()
            ]))
            ->send();

        \S3b0\EcomSkuGenerator\Session\ManageConfiguration::resetConfiguration($this);
        $this->redirect('confirmation');
    }

}