<?php
namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\forms;

use EE_Error;
use EE_Form_Section_HTML;
use EE_Form_Section_HTML_From_Template;
use EE_Form_Section_Proper;
use EE_Model_Field_Base;
use EE_Select_Input;
use EEH_HTML;
use EEM_Answer;
use EEM_Attendee;
use EEM_Base;
use EEM_Payment;
use EEM_Registration;
use EEM_Transaction;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportFieldMap;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\loaders\LoaderFactory;
use EventEspresso\core\domain\Domain;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class ColumnMappingForm
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class MapCsvColumnsForm extends EE_Form_Section_Proper
{

    public function __construct($options_array = array())
    {
        $options_array = array_replace_recursive(
            [
                'subsections' => [
                    'header' => new EE_Form_Section_HTML(
                        EEH_HTML::h2(
                            sprintf(
                                esc_html__('Map CSV Columns to %s Data', 'event_espresso'),
                                Domain::brandName()
                            )
                            . $options_array['help_tab_link']
                        )
                    ),
                    'instructions' => new EE_Form_Section_HTML_From_Template(
                        wp_normalize_path(dirname(dirname(dirname(__FILE__))) . '/templates/ee_importer_mapping_instructions.template.php')
                    ),
                    'columns' => LoaderFactory::getLoader()->getNew('EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\forms\forms\MapCsvColumnsSubform'),
                ],
            ],
            $options_array
        );
        parent::__construct($options_array);
    }
}
// End of file ColumnMappingForm.php
// Location: ${NAMESPACE}/ColumnMappingForm.php
