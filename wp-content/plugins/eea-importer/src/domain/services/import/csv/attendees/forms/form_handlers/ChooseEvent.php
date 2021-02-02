<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers;

use DomainException;
use EE_Error;
use EE_Form_Section_HTML;
use EE_Form_Section_Proper;
use EE_Registry;
use EE_Select_Ajax_Model_Rest_Input;
use EEH_HTML;
use EEM_Ticket;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFormSubmissionException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\libraries\form_sections\form_handlers\FormHandler;
use EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepForm;
use EventEspresso\core\services\options\JsonWpOptionManager;
use InvalidArgumentException;
use LogicException;
use ReflectionException;

/**
 * Class ChooseEvent
 *
 * Step for uploading the CSV file to import.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ChooseEvent extends ImportCsvAttendeesStep
{

    /**
     * ChooseEvent constructor
     *
     * @param EE_Registry $registry
     * @param ImportCsvAttendeesConfig $config
     * @param JsonWpOptionManager $option_manager
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     */
    public function __construct(
        EE_Registry $registry,
        ImportCsvAttendeesConfig $config,
        JsonWpOptionManager $option_manager
    ) {
        $this->setDisplayable(true);
        $this->has_help_tab = true;
        parent::__construct(
            1,
            esc_html__('Choose Event', 'event_espresso'),
            esc_html__('"Choose Event" Attendee Importer Step', 'event_espresso'),
            'choose-event',
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
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    public function generate()
    {
        $this->option_manager->populateFromDb($this->config);
        return new EE_Form_Section_Proper(
            [
                'name' => 'event',
                'subsections' => [
                    'header' => new EE_Form_Section_HTML(
                        EEH_HTML::h2(
                            esc_html__('Select Event', 'event_espresso')
                            . $this->getHelpTabLink()
                        )
                    ),
                    'event' => new EE_Select_Ajax_Model_Rest_Input(
                        [
                            'model_name' => 'Event',
                            'required' => true,
                            'html_label_text' => esc_html__('Event', 'event_espresso'),
                            'html_help_text' => esc_html__('The event data should be imported to.', 'event_espresso'),
                            'query_params' => [
                                'order_by' => ['EVT_ID' => 'DESC']
                            ]
                        ]
                    )
                ]
            ]
        );
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
     * @throws InvalidInterfaceException
     * @throws LogicException
     * @throws ReflectionException
     */
    public function process($form_data = array())
    {
        try {
            $valid_data = (array) parent::process($form_data);
        } catch (InvalidFormSubmissionException $e) {
            // Don't die. Admin code knows how to handle invalid forms...
            return false;
        }
        $this->config->setEventId($valid_data['event']);

        // If there is only one ticket for this event, we can set the default ticket now and skip that step.
        $tickets = EEM_Ticket::instance()->get_all(
            [
                [
                    'Datetime.EVT_ID' => $this->config->getEventId()
                ]
            ]
        );
        if (count($tickets) === 1) {
            $ticket = reset($tickets);
            $this->config->setTicketId($ticket->ID());
            $this->setRedirectTo(SequentialStepForm::REDIRECT_TO_OTHER);
            $this->addRedirectArgs(
                [
                    'ee-form-step' => 'upload'
                ]
            );
            EE_Error::add_success(
                esc_html__(
                    'Ticket Selection step skipped because there is only one ticket for the event selected.',
                    'event_espresso'
                )
            );
        } else {
            $this->setRedirectTo(SequentialStepForm::REDIRECT_TO_NEXT_STEP);
        }
        $this->option_manager->saveToDb($this->config);

        return true;
    }
}
// End of file ChooseEvent.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers/ChooseEvent.php
