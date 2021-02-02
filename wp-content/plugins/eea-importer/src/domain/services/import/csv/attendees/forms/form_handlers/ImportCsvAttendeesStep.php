<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers;

use DomainException;
use EE_Registry;
use EEH_Template;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepForm;
use EventEspresso\core\services\options\JsonWpOptionManager;
use InvalidArgumentException;

/**
 * Class ImportCsvAttendeesStep
 *
 * Base class for importing attendees from a CSV file.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
abstract class ImportCsvAttendeesStep extends SequentialStepForm
{
    /**
     * @var ImportCsvAttendeesConfig
     */
    protected $config;

    /**
     * @var JsonWpOptionManager
     */
    protected $option_manager;

    /**
     * Indicates whether there is a help tab for this step.
     * @var bool
     */
    protected $has_help_tab = false;

    /**
     * UploadCsv constructor
     *
     * @param $order
     * @param $name
     * @param $longer_name
     * @param $slug
     * @param $form_action
     * @param $form_config
     * @param EE_Registry $registry
     * @param ImportCsvAttendeesConfig $config
     * @param JsonWpOptionManager $option_manager
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     */
    public function __construct(
        $order,
        $name,
        $longer_name,
        $slug,
        $form_action,
        $form_config,
        EE_Registry $registry,
        ImportCsvAttendeesConfig $config,
        JsonWpOptionManager $option_manager
    ) {
        $this->config = $config;
        $this->option_manager = $option_manager;
        parent::__construct(
            $order,
            $name,
            $longer_name,
            $slug,
            $form_action,
            $form_config,
            $registry
        );
    }

    /**
     * Indicates whether there is an admin help tab for this step or not.
     * @since 1.0.0.p
     * @return bool
     */
    public function hasHelpTab()
    {
        return $this->has_help_tab;
    }

    /**
     * @since 1.0.0.p
     * @return string
     */
    public function getHelpTabLink()
    {
        return EEH_Template::get_help_tab_link(
            'importer_import_' . $this->slug(),
            ATTENDEE_IMPORTER_PG_SLUG,
            'show_import_step'
        );
    }
}
// End of file ImportCsvAttendeesStep.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers/ImportCsvAttendeesStep.php
