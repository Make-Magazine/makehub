<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping;

use EE_Base_Class;
use EE_Boolean_Field;
use EE_Error;
use EE_Foreign_Key_Int_Field;
use EE_Model_Field_Base;
use EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoerceString;
use EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoercionInterface;
use EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoercionStrategyFactory;
use EventEspresso\core\services\json\JsonSerializableAndUnserializable;
use stdClass;

/**
 * Class ImportFieldMap
 *
 * Describes how to map between a property of the source data to destination model field, and how to interpret the data.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportFieldMap implements JsonSerializableAndUnserializable
{
    protected $source_property;

    /**
     * @var EE_Model_Field_Base
     */
    protected $destination_field;

    /**
     * @var ImportFieldCoercionInterface
     */
    protected $coercion_strategy;
    /**
     * @var ImportFieldCoercionStrategyFactory
     */
    private $coercion_factory;

    /**
     * ImportFieldMap constructor.
     * @param $sourceProperty
     * @param EE_Model_Field_Base $destinationField
     * @param ImportFieldCoercionInterface $coercionStrategy
     */
    public function __construct(
        ImportFieldCoercionStrategyFactory $coercion_factory,
        EE_Model_Field_Base $destinationField,
        $sourceProperty = null
    ) {
        $this->source_property = $sourceProperty;
        $this->destination_field = $destinationField;
        $this->coercion_factory = $coercion_factory;
        if ($destinationField instanceof EE_Boolean_Field) {
            $this->coercion_strategy = $this->coercion_factory->create('boolean');
        }
        if ($destinationField instanceof \EE_Foreign_Key_Field_Base) {
            if (in_array('State', $destinationField->get_model_names_pointed_to())) {
                $this->coercion_strategy = $this->coercion_factory->create('state');
            } elseif (in_array('Country', $destinationField->get_model_names_pointed_to())) {
                $this->coercion_strategy = $this->coercion_factory->create('country');
            }
        }
        if (!$this->coercion_strategy instanceof ImportFieldCoercionInterface) {
            $this->coercion_strategy = $this->coercion_factory->create('string');
        }
    }

    /**
     * Gets the name of the model field that is mapped.
     * @since 1.0.0.p
     * @return string
     * @throws EE_Error
     */
    public function destinationFieldName()
    {
        return $this->destination_field->get_name();
    }

    /**
     * Gets the field the source property input maps to.
     * @since 1.0.0.p
     * @return EE_Model_Field_Base
     */
    public function destinationField()
    {
        return $this->destination_field;
    }

    /**
     * @since 1.0.0.p
     * @param $column
     */
    public function map($column)
    {
        $this->source_property = $column;
    }

    /**
     * Applies the established mapping to the input to get the value to set on the model object.
     * @since 1.0.0.p
     * @param $input
     * @param EE_Base_Class $destinationObject
     * @throws EE_Error
     * @throws \EventEspresso\core\exceptions\InvalidDataTypeException
     * @throws \EventEspresso\core\exceptions\InvalidInterfaceException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function applyMap($input)
    {
        return $this->coercion_strategy->coerce($input);
    }

    /**
     * Gets the name of the source's input property.
     * @since 1.0.0.p
     * @return string
     */
    public function sourceProperty()
    {
        return $this->source_property;
    }

    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        $simple_obj = new stdClass();
        $simple_obj->input = $this->source_property;
        return $simple_obj;
    }

    /**
     * Initializes this object from data
     * @since 1.0.0.p
     * @param mixed $data
     * @return boolean success
     */
    public function fromJsonSerializedData($data)
    {
        if ($data instanceof stdClass
            && property_exists($data, 'input')) {
            $this->map($data->input);
        }
    }
}
// End of file ImportFieldMap.php
// Location: EventEspresso\AttendeeImporter\application\services\import\mapping/ImportFieldMap.php
