<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping\coercion;

use EE_Base_Class;

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
class ImportFieldCoerceString implements ImportFieldCoercionInterface
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
        return (string) $inputProperty;
    }

    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        return 'string';
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
