<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Error;
use EE_Line_Item;
use EEH_Line_Item;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

/**
 * Class CreateAttendeeCommandHandler
 * Creates a transaction and line item tree from the inputted data.
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 */
class ImportLineItemCommandHandler extends CommandHandler
{

    /**
     * @param CommandInterface|ImportLineItemCommand $command
     * @return EE_Line_Item
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function handle(CommandInterface $command)
    {
        $total_line_item = $command->getTransaction()->total_line_item();
        $line_item = EEH_Line_Item::create_ticket_line_item($total_line_item, $command->getTicket());
        $total_line_item->recalculate_total_including_taxes();

        return $line_item;
    }
}
