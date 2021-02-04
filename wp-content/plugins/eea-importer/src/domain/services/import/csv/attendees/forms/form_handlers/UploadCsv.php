<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers;

use EE_Error;
use EE_Form_Section_Proper;
use EE_Registry;
use EED_Attendee_Importer;
use EEH_File;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\forms\UploadCsvForm;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFormSubmissionException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\libraries\form_sections\form_handlers\FormHandler;
use EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepForm;
use EventEspresso\core\services\options\JsonWpOptionManager;
use InvalidArgumentException;
use LogicException;

/**
 * Class UploadCsv
 *
 * Step for uploading the CSV file to import.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class UploadCsv extends ImportCsvAttendeesStep
{

    /**
     * UploadCsv constructor
     *
     * @param EE_Registry $registry
     * @param ImportCsvAttendeesConfig $config
     * @param JsonWpOptionManager $option_manager
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws \DomainException
     */
    public function __construct(EE_Registry $registry, ImportCsvAttendeesConfig $config, JsonWpOptionManager $option_manager)
    {
        $this->setDisplayable(true);
        $this->has_help_tab = true;
        parent::__construct(
            3,
            esc_html__('Upload', 'event_espresso'),
            esc_html__('"Upload" Attendee Importer Step', 'event_espresso'),
            'upload',
            '',
            FormHandler::ADD_FORM_TAGS_AND_SUBMIT,
            $registry,
            $config,
            $option_manager
        );
    }


    /**
     * creates and returns the actual form
     *
     * @return EE_Form_Section_Proper
     * @throws EE_Error
     */
    public function generate()
    {
        $this->option_manager->populateFromDb($this->config);
        $form = new UploadCsvForm(
            [
                'help_tab_link' => $this->getHelpTabLink()
            ]
        );
        return $form;
    }

    /**
     * handles processing the form submission
     * returns true or false depending on whether the form was processed successfully or not
     *
     * @param array $form_data
     * @return bool
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws EE_Error
     * @throws InvalidFormSubmissionException
     * @throws InvalidInterfaceException
     * @throws LogicException
     */
    public function process($form_data = array())
    {
        try {
            $valid_data = (array) parent::process($form_data);
        } catch (InvalidFormSubmissionException $e) {
            return false;
        }
        if (empty($valid_data)) {
            return false;
        }
        $new_filepath = wp_normalize_path(
            EVENT_ESPRESSO_UPLOAD_DIR
            . 'attendee-importer'
            . DS
            . 'csv-uploads'
            . DS
            . wp_generate_password(15, false)
            . '/'
            .  $valid_data['file']->getName()
        );
        EEH_File::copy(
            $valid_data['file']->getTmpFile(),
            $new_filepath
        );
        // Config was already populated from the DB during generate().
        $this->config->setFile($new_filepath);
        $this->option_manager->saveToDb($this->config);
        $this->setRedirectTo(SequentialStepForm::REDIRECT_TO_NEXT_STEP);
        return true;
    }
}
// End of file UploadCsv.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers/UploadCsv.php
