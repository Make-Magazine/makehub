<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping\coercion;

use EE_Error;
use EEM_State;

/**
 * Class ImportFieldString
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportFieldCoerceState implements ImportFieldCoercionInterface
{

    /**
     * @var EEM_State
     */
    private $state_model;

    public function __construct(EEM_State $state_model)
    {
        $this->state_model = $state_model;
    }

    /**
     * Takes the input and converts
     * @since 1.0.0.p
     * @param $inputProperty
     * @return int
     * @throws EE_Error
     */
    public function coerce($inputProperty)
    {
        $inputProperty = (string) $inputProperty;
        return (int) $this->state_model->get_var(
            [
                [
                    'OR' => [
                        'STA_abbrev' => $inputProperty,
                        'STA_name' => $inputProperty,
                        'STA_ID' => $inputProperty
                    ]
                ],
                'limit' => 1
            ],
            'STA_ID'
        );
    }

    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        return 'state';
    }

    /**
     * Initializes this object from data
     * @since 1.0.0.p
     * @param mixed $data
     * @return boolean success
     */
    public function fromJsonSerializedData($data)
    {
    }
}
// End of file ImportFieldString.php
// Location: EventEspresso\AttendeeImporter\application\services\import\mapping\coercion/ImportFieldString.php
