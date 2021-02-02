<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Attendee;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\commands\CommandInterface;
use EventEspresso\core\services\commands\CompositeCommandHandler;
use InvalidArgumentException;

/**
 * Class CreateAttendeeCommandHandler
 * Creates a transaction and line item tree from the inputted data.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 */
class ImportTransactionCommandHandler extends CompositeCommandHandler
{

    /**
     * @param CommandInterface $command
     * @return EE_Attendee
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command)
    {
        // Create a transaction
        /*
         * @var $transaction EE_Transaction
         */
        $transaction = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\core\services\commands\transaction\CreateTransactionCommand',
                [
                    null,
                    []
                ]
            )
        );
        // Mark the transaction as complete eh.
        $transaction->save();
        return $transaction;
    }
}
