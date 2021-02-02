<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers;

use DomainException;
use EE_Admin_Page;
use EE_Error;
use EE_Event;
use EE_Form_Section_HTML;
use EE_Form_Section_HTML_From_Template;
use EE_Form_Section_Proper;
use EE_Hidden_Input;
use EE_Registry;
use EE_Select_Ajax_Model_Rest_Input;
use EE_Ticket;
use EED_Attendee_Importer;
use EEH_HTML;
use EEH_URL;
use EEM_Attendee;
use EEM_Event;
use EEM_Question_Group;
use EEM_Ticket;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportFieldMap;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\AttendeeImporter\domain\services\import\managers\ui\ImportCsvAttendeesUiManager;
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
 * Class Verify
 *
 * Step for verifying all the setup is correct.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class Verify extends ImportCsvAttendeesStep
{
    /**
     * @var ImportCsvAttendeesUiManager
     */
    private $attendeesUiManager;
    /**
     * @var EEM_Event
     */
    private $event_model;
    /**
     * @var EEM_Ticket
     */
    private $ticket_model;
    /**
     * @var EEM_Attendee
     */
    private $attendee_model;
    /**
     * @var EEM_Question_Group
     */
    private $question_group_model;

    /**
     * Verify constructor
     *
     * @param EE_Registry $registry
     * @param ImportCsvAttendeesConfig $config
     * @param JsonWpOptionManager $option_manager
     * @param ImportCsvAttendeesUiManager $attendeesUiManager
     * @param EEM_Event $event_model
     * @param EEM_Ticket $ticket_model
     * @param EEM_Attendee $attendee_model
     * @param EEM_Question_Group $question_group_model
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     */
    public function __construct(
        EE_Registry $registry,
        ImportCsvAttendeesConfig $config,
        JsonWpOptionManager $option_manager,
        ImportCsvAttendeesUiManager $attendeesUiManager,
        EEM_Event $event_model,
        EEM_Ticket $ticket_model,
        EEM_Attendee $attendee_model,
        EEM_Question_Group $question_group_model
    ) {
        $this->setDisplayable(true);
        $this->setFormConfig(FormHandler::ADD_FORM_TAGS_ONLY);
        parent::__construct(
            5,
            esc_html__('Verify', 'event_espresso'),
            esc_html__('"Verify" Attendee Importer Step', 'event_espresso'),
            'verify',
            '',
            FormHandler::ADD_FORM_TAGS_AND_SUBMIT,
            $registry,
            $config,
            $option_manager
        );
        $this->attendeesUiManager = $attendeesUiManager;
        $this->event_model = $event_model;
        $this->ticket_model = $ticket_model;
        $this->attendee_model = $attendee_model;
        $this->question_group_model = $question_group_model;
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

        $form = new EE_Form_Section_Proper(
            [
                'name' => 'verify',
                'subsections' => [
                    'instructions' => new EE_Form_Section_HTML(
                        EEH_HTML::p(
                            // @codingStandardsIgnoreStart
                            esc_html__('Please verify the data has been mapped correctly. If not, please use your browser’s back button to correct it.', 'event_espresso')
                            // @codingStandardsIgnoreEnd
                        )
                    ),
                    'data' => new EE_Form_Section_HTML_From_Template(
                        dirname(dirname(__DIR__)) . '/templates/importer_verify_info.template.php',
                        [

                            // Let's add the event and ticket for starters
                            'event' => $this->event_model->get_one_by_ID($this->config->getEventId()),
                            'ticket' => $this->ticket_model->get_one_by_ID($this->config->getTicketId()),
                            'table_rows' => $this->getTableRows()
                        ]
                    ),
                    'hidden' => new EE_Hidden_Input(),
                    'notice' => new EE_Form_Section_HTML(
                        EEH_HTML::p(
                            esc_html__(
                            // @codingStandardsIgnoreStart
                                'The import will start after this step. Please wait for it to complete before closing this window, turning off your computer, or navigating away.',
                                // @codingStandardsIgnoreEnd
                                'event_espresso'
                            )
                        )
                        . EEH_HTML::p(
                            esc_html__(
                            // @codingStandardsIgnoreStart
                                'Please ensure you have a database backup so you can easily revert the import if it doesn’t work as expected.',
                                // @codingStandardsIgnoreEnd
                                'event_espresso'
                            )
                        )
                    )
                ]
            ]
        );
        // Make our own submit button, with different text.
        $form->add_subsections(
            [
                $this->slug() . '-submit-btn' => $this->generateSubmitButton(
                    esc_html__('I have a database backup. Begin Import', 'event_espresso')
                )
            ],
            null,
            false
        );
        return $form;
    }

    /**
     * Gets the data to put into the HTML table when displaying this step.
     * This includes both data from the model configs and the custom questions config.
     * @since 1.0.0.p
     * @return array
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws ReflectionException
     */
    protected function getTableRows()
    {
        $extractor = $this->attendeesUiManager->getImportType()->getExtractor();
        $extractor->setSource($this->config->getFile());
        $csv_headers = $extractor->getItemAt(0);
        $row1 = $extractor->getItemAt(1);
        $row2 = $extractor->getItemAt(2);
        $table_rows = $this->getModelFieldsTableRows($csv_headers, $row1, $row2);
        $table_rows = array_merge(
            $table_rows,
            $this->getCustomQuestionTableRows($csv_headers, $row1, $row2)
        );
        return $table_rows;
    }

    /**
     * @since 1.0.0.p
     * @param $csv_headers
     * @param $row1
     * @param $row2
     * @return array
     */
    protected function getModelFieldsTableRows($csv_headers, $row1, $row2)
    {
        $table_rows = [];
        foreach ($this->config->getModelConfigs() as $modelConfig) {
            if ($modelConfig->getModel() === $this->attendee_model) {
                continue;
            }
            $item_name = $modelConfig->getModel()->item_name();
            $options[ $item_name ] = [];
            $model_table_rows = [];
            foreach ($modelConfig->mapping() as $mapped_field) {
                $input_column = array_search(
                    $mapped_field->sourceProperty(),
                    $csv_headers,
                    true
                );

                $value = $value2 = null;
                if ($input_column) {
                    if (isset($row1[ $input_column ])) {
                        $value = $row1[ $input_column ];
                    }
                    if (isset($row2[ $input_column ])) {
                        $value2 = $row2[ $input_column ];
                    }
                }
                $model_table_rows[] = [
                    $mapped_field->destinationField()->get_nicename(),
                    $mapped_field->sourceProperty(),
                    $value,
                    $value2
                ];
            }
            // Only show the header if there were rows to show for it.
            if (! empty($model_table_rows)) {
                $table_rows[] = $modelConfig->getModel()->item_name();
                $table_rows = array_merge($table_rows, $model_table_rows);
            }
        }
        return $table_rows;
    }

    /**
     * @since 1.0.0.p
     * @param $csv_headers
     * @param $row1
     * @param $row2
     * @return array
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws ReflectionException
     */
    protected function getCustomQuestionTableRows($csv_headers, $row1, $row2)
    {
        $attendee_config = $this->config->getModelConfigs()->get('Attendee');
        // And add questions (group by question group).
        $question_groups_for_event = $this->question_group_model->get_all(
            [
                [
                    'Event_Question_Group.EVT_ID' => $this->config->getEventId(),
                    'QSG_deleted' => false,
                ]
            ]
        );
        $question_ids_to_column_names = $this->config->getQuestionMapping();
        $table_rows = [];
        foreach ($question_groups_for_event as $question_group) {
            $question_rows = [];
            foreach ($question_group->questions() as $question) {
                // Try to find the column, assuming its a custom question.
                $column_name = null;
                $question_id = (int) $question->ID();
                if (array_key_exists($question_id, $question_ids_to_column_names)) {
                    $column_name = $question_ids_to_column_names[ $question_id ];
                } else {
                    $attendee_field = $this->attendee_model->get_attendee_field_for_system_question(
                        $question->system_ID()
                    );
                    $mapping_info = $attendee_config->getMappingInfoForField($attendee_field);
                    if ($mapping_info instanceof ImportFieldMap) {
                        $column_name = $mapping_info->sourceProperty();
                    }
                }

                $input_column = null;
                if ($column_name) {
                    $input_column = array_search(
                        $column_name,
                        $csv_headers,
                        true
                    );
                }

                // Ok if either of those worked, find the values for the first two rows.
                $value = $value2 = null;
                if ($input_column !== false) {
                    if (isset($row1[ $input_column ])) {
                        $value = $row1[ $input_column ];
                    }
                    if (isset($row2[ $input_column ])) {
                        $value2 = $row2[ $input_column ];
                    }
                }

                $question_rows[] = [
                    $question->admin_label(),
                    $column_name,
                    $value,
                    $value2
                ];
            }

            if (! empty($question_rows)) {
                $table_rows[] = $question_group->name(true);
                $table_rows = array_merge($table_rows, $question_rows);
            }
        }
        return $table_rows;
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
     */
    public function process($form_data = array())
    {
        try {
            $valid_data = (array) parent::process($form_data);
        } catch (InvalidFormSubmissionException $e) {
            // Don't die. Admin code knows how to handle invalid forms...
            return false;
        }
        $this->setRedirectTo(SequentialStepForm::REDIRECT_TO_OTHER);
        $this->removeRedirectArgs(
            [
                'ee-form-step'
            ]
        );
        $this->setRedirectUrl(
            EE_Admin_Page::add_query_args_and_nonce(
                array(
                    'page' => 'espresso_batch',
                    'batch' => 'job',
                    'job_handler' => urlencode(get_class($this->attendeesUiManager->getBatchJobHandler())),
                    'return_url' => urlencode(
                        add_query_arg(
                            array(
                                'ee-form-step' => 'complete',
                            ),
                            EEH_URL::current_url_without_query_paramaters(
                                array(
                                    'ee-form-step',
                                    'return',
                                )
                            )
                        )
                    ),
                )
            )
        );

        return true;
    }
}
// End of file Verify.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\form_handlers/Verify.php
