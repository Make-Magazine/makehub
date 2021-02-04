<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Error;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportFieldMap;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\CollectionDetailsException;
use EventEspresso\core\services\collections\CollectionLoaderException;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class ImportSingleModelBase
 *
 * Base configuration for when we're importing a single model object from the input data.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportSingleModelBase extends ImportBaseCommand
{
    /**
     * @var ImportModelConfigBase
     */
    private $config;

    public function __construct(
        array $input_data,
        ImportModelConfigBase $config
    ) {
        parent::__construct($input_data);
        $this->config = $config;
    }

    /**
     * Gets an array where keys are model field names, and values are their coerced values fromthe input data.
     * @since 1.0.0.p
     * @return array
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     * @throws EE_Error
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getFieldsFromMappedData()
    {
        $fields_mapped = $this->config->mapping();
        $fields = [];
        foreach ($fields_mapped as $field_mapped) {
            $input_value = $this->valueFromInput(
                $field_mapped->sourceProperty()
            );
            if (is_null($input_value)) {
                continue;
            }
            $input_datum = $field_mapped->applyMap(
                $input_value
            );
            /* @var $field_mapped ImportFieldMap */
            $fields[ $field_mapped->destinationFieldName() ] = $input_datum;
        }
        return $fields;
    }
}
// End of file ImportSingleModelBase.php
// Location: EventEspresso\AttendeeImporter\domain\services\commands/ImportSingleModelBase.php
