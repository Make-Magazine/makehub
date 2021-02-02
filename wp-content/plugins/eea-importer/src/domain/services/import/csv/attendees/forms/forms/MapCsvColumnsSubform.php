<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\forms;

use EE_Form_Section_Proper;
use EE_Model_Field_Base;
use EE_Select_Input;
use EEM_Answer;
use EEM_Attendee;
use EEM_Event;
use EEM_Question_Group;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportFieldMap;
use EventEspresso\core\domain\Domain;

/**
 * Class MapCsvColumnsSubform
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class MapCsvColumnsSubform extends EE_Form_Section_Proper
{

    /**
     * @var ImportCsvAttendeesConfig
     */
    protected $config;


    public function __construct($options_array = array(), ImportCsvAttendeesConfig $config)
    {
        $this->config = $config;
        $column_headers = $this->getColumnHeadersFromFile();
        $columns_inputs = [];
        $options = $this->initMapToOptions();
        foreach ($column_headers as $column_header) {
            $columns_inputs[ $column_header ] = new EE_Select_Input(
                $options,
                [
                    'default' => $this->getDefaultFor($column_header)
                ]
            );
        }
        $options_array = array_replace_recursive(
            [
                'subsections' =>  $columns_inputs
            ],
            $options_array
        );
        parent::__construct($options_array);
    }


    /**
     * Reads the current CSV file and finds its header columns
     * @since 1.0.0.p
     */
    protected function getColumnHeadersFromFile()
    {
        $file_obj = $this->config->getFileHandle();
        return $file_obj->fgetcsv();
    }

    /**
     * Gets the options to map columns to (eg attendee firstname, registration code, payment amount, etc).
     * @since 1.0.0.p
     * @return array
     * @throws EE_Error
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     */
    protected function initMapToOptions()
    {
        $options = [
            '' => [
                '' => ''
            ],
        ];
        foreach ($this->config->getModelConfigs() as $modelConfig) {
            if ($modelConfig->getModel() !== EEM_Attendee::instance()) {
                $item_name = $modelConfig->getModel()->item_name();
                $this_model_options = [];
                foreach ($modelConfig->mapping() as $mapped_field) {
                    $input_name = $this->getOptionValueForField($mapped_field->destinationField());
                    $this_model_options[ $input_name ] = $mapped_field->destinationField()->get_nicename();
                }
                if (! empty($this_model_options)) {
                    $options[ $item_name ] = $this_model_options;
                }
            }
        }
        // And add questions (group by question group).
        $question_groups_for_event = EEM_Question_Group::instance()->get_all(
            [
                [
                    'Event_Question_Group.EVT_ID'      => $this->config->getEventId(),
                    'QSG_deleted'                      => false,
                ]
            ]
        );
        foreach ($question_groups_for_event as $question_group) {
            foreach ($question_group->questions() as $question) {
                if ($question->is_system_question()) {
                    if ($question->system_ID() === 'state') {
                        $append = 'STA_ID';
                    } elseif ($question->system_ID() === 'country') {
                        $append = 'CNT_ISO';
                    } else {
                        $append = 'ATT_' . $question->system_ID();
                    }
                    $option_value = 'Attendee.' . $append;
                } else {
                    $option_value = 'Question.' . $question->ID();
                }
                $options[ $question_group->name() ][ $option_value ] = $question->admin_label();
            }
        }

        return $options;
    }

    /**
     * Gets the default form value for the CSV column from the config.
     * @since 1.0.0.p
     * @param $column_name
     * @return string
     * @throws EE_Error
     */
    protected function getDefaultFor($column_name)
    {
        foreach ($this->config->getModelConfigs() as $modelConfig) {
            $mapped_info = $modelConfig->getMappingInfoForInput($column_name);
            if ($mapped_info instanceof ImportFieldMap) {
                return $this->getOptionValueForField($mapped_info->destinationField());
            }
        }
        foreach ($this->config->getQuestionMapping() as $question_id => $column_for_question) {
            if ($column_name === $column_for_question) {
                return 'Question.' . $question_id;
            }
        }
    }

    /**
     * Gets the form option value for the field.
     * @since 1.0.0.p
     * @param EE_Model_Field_Base $field
     * @return string
     * @throws EE_Error
     */
    public function getOptionValueForField(EE_Model_Field_Base $field)
    {
        return $field->get_model_name() . '.' . $field->get_name();
    }

//    /**
//     * Generates the input options from the model and its list of fields to include.
//     * @since 1.0.0.p
//     * @param EEM_Base $model
//     * @param $fields_to_include
//     * @return array
//     */
//    protected function optionsFromModel(EEM_Base $model, array $fields_to_include)
//    {
//        $fields = array_intersect_key(
//            $model->field_settings(),
//            array_flip($fields_to_include)
//        );
//        $options = [];
//        foreach($fields as $field){
//            $options[$model->item_name()][$model->get_this_model_name() . '.' . $field->get_name()] = $field->get_nicename();
//        }
//        return $options;
//    }

    /**
     * When validating the form, make sure no two columns have the same value.
     * @since 1.0.0.p
     * @return bool|void
     * @throws EE_Error
     */
    protected function _validate()
    {
        parent::_validate();
        $valid_data = $this->valid_data();

        // Make sure no two CSV columns were mapped to the same EE data.
        // Also, make sure they've provided at least firstname and email.
        $found_firstname = false;
        $found_email = false;
        foreach ($valid_data as $input_name1 => $value1) {
            if ($value1 === 'Attendee.ATT_fname') {
                $found_firstname = true;
            } elseif ($value1 === 'Attendee.ATT_email') {
                $found_email = true;
            }
            foreach ($valid_data as $input_name2 => $value2) {
                if ($input_name1 !== $input_name2
                    && $value1 === $value2
                    && $value1 !== ''
                    && $value1 !== null
                ) {
                    $input1 = $this->get_input($input_name1);
                    $input2 = $this->get_input($input_name2);
                    $input2->add_validation_error(
                        sprintf(
                            esc_html__(
                                // translators: %1$s CSV column name, %2$s Event Espresso, %3$s Event Espresso Data name, %4$s CSV column name
                            // @codingStandardsIgnoreStart
                                'CSV file column "%1$s" maps to the same %2$s data (%3$s) as "%4$s". This is not allowed.',
                                // @codingStandardsIgnoreEnd
                                'event_espresso'
                            ),
                            $input1->html_label_text(),
                            Domain::brandName(),
                            $input1->pretty_value(),
                            $input1->html_label_text()
                        )
                    );
                }
            }
        }

        if (! $found_firstname) {
            $this->add_validation_error(
                esc_html__('The Attendee First Name must be mapped to a CSV column.', 'event_espresso')
            );
        }
        if (! $found_email) {
            $this->add_validation_error(
                esc_html__('The Attendee Email must be mapped to a CSV column.', 'event_espresso')
            );
        }
    }
}
// End of file MapCsvColumnsSubform.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\forms/MapCsvColumnsSubform.php
