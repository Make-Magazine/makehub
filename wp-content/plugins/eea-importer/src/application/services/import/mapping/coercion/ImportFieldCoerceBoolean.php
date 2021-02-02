<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping\coercion;

use EE_Base_Class;

/**
 * Class ImportFieldBoolean
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportFieldCoerceBoolean implements ImportFieldCoercionInterface
{


    /**
     * Takes the input and converts
     * @since 1.0.0.p
     * @param $inputProperty
     * @param EE_Base_Class $destinationObject only used when the value of one field affects the value of another.
     * @return mixed
     */
    public function coerce($inputProperty)
    {
        return filter_var($inputProperty, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        return 'bool';
    }

    /**
     * Initializes this object from data
     * @since 1.0.0.p
     * @param mixed $data
     * @return boolean success
     */
    public function fromJsonSerializedData($data)
    {
        // TODO: Implement fromJsonSerializedData() method.
    }
}
// End of file ImportFieldBoolean.php
// Location: EventEspresso\AttendeeImporter\application\services\import\mapping\coercion/ImportFieldBoolean.php
