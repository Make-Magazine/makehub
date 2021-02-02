<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping\coercion;

use EE_Base_Class;
use EventEspresso\core\services\json\JsonSerializableAndUnserializable;

/**
 * Class ImportFieldMappingStrategyInterface
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
interface ImportFieldCoercionInterface extends JsonSerializableAndUnserializable
{
    /**
     * Takes the input and converts to the appropriate type.
     * @since 1.0.0.p
     * @param string $inputProperty
     * @return mixed
     */
    public function coerce($inputProperty);
}
// End of file ImportFieldMappingStrategyInterface.php
// Location: EventEspresso\AttendeeImporter\application\services\import\mapping\strategies/ImportFieldMappingStrategyInterface.php
