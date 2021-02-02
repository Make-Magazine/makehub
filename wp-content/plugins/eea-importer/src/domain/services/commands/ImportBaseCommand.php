<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\services\commands\Command;
use EventEspresso\core\services\commands\CommandRequiresCapCheckInterface;

/**
 * Class ModelObjFromCsvRowCommand
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportBaseCommand extends Command implements CommandRequiresCapCheckInterface
{
    /**
     * @var array $input_data
     */
    protected $input_data;


    /**
     * CreateAttendeeCommand constructor.
     *
     * @param array           $input_data
     */
    public function __construct(array $input_data)
    {
        $this->input_data = $input_data;
    }


    /**
     * @return CapCheckInterface
     * @throws InvalidDataTypeException
     */
    public function getCapCheck()
    {
        return new CapCheck('ee_import', 'ee_attendee_import');
    }

    /**
     * Returns the array from the CSV row, where keys are CSV columns names, values are their values.
     * @since 1.0.0.p
     * @return array
     */
    public function inputData()
    {
        return $this->input_data;
    }

    /**
     * Gets the raw value from the CSV file at the given column
     * @since 1.0.0.p
     * @param $column_name
     * @return string|null
     */
    public function valueFromInput($column_name)
    {
        return isset($this->input_data[ $column_name ]) ? $this->input_data[ $column_name ] : null;
    }
}
// End of file ModelObjFromCsvRowCommand.php
// Location: EventEspresso\AttendeeImporter\domain\services\commands/ModelObjFromCsvRowCommand.php
