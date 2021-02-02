<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Attendee;

use EE_Payment;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\commands\CommandInterface;
use EventEspresso\core\services\commands\CompositeCommandHandler;
use InvalidArgumentException;

/**
 * Class CreateAttendeeCommandHandler
 * generates and validates an Attendee
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 */
class ImportCommandHandler extends CompositeCommandHandler
{

    /**
     * @param CommandInterface $command
     * @return EE_Attendee
     * @throws InvalidArgumentException
     * @throws InvalidDataTypeException
     * @throws InvalidEntityException
     * @throws InvalidInterfaceException
     */
    public function handle(CommandInterface $command)
    {
        /** @var ImportCommand $command */
        if (!$command instanceof ImportCommand) {
            throw new InvalidEntityException(
                get_class($command),
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportCommand'
            );
        }
        // If the row only contains blanks, don't import anything.
        if ($command->rowIsOnlyBlanks()) {
            return null;
        }

        // No messages while importing thanks.
        add_filter(
            'FHEE__EED_Messages___maybe_registration__deliver_notifications',
            '__return_false',
            999
        );
        remove_all_filters('AHEE__EE_Payment_Processor__update_txn_based_on_payment');

        // WP User Add-on: please don't try to sync imported users to the current user
        if (method_exists('EED_WP_Users_SPCO', 'maybe_sync_existing_attendee')) {
            remove_filter(
                'FHEE_EE_Single_Page_Checkout__save_registration_items__find_existing_attendee',
                ['EED_WP_Users_SPCO','maybe_sync_existing_attendee']
            );
        }

        $transaction = $attendee = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportTransactionCommand',
                [
                    $command->getConfig()->getTicket(),
                    $command->inputData(),
                    $command->getConfig()->getModelConfigs()->get('Transaction')
                ]
            )
        );

        $line_item = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportLineItemCommand',
                [
                    $transaction,
                    $command->getConfig()->getTicket(),
                    $command->inputData(),
                    $command->getConfig()->getModelConfigs()->get('Line_Item')
                ]
            )
        );

        // Create a payment and payment-registration entries.
        $payment = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportPaymentCommand',
                [
                    $transaction,
                    $command->inputData(),
                    $command->getConfig()->getModelConfigs()->get('Payment')
                ]
            )
        );

        $registration = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportRegistrationCommand',
                [
                    $transaction,
                    $line_item,
                    $command->inputData(),
                    $command->getConfig()->getModelConfigs()->get('Registration')
                ]
            )
        );

        $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportAttendeeCommand',
                [
                    $registration,
                    $command->inputData(),
                    $command->getConfig()->getModelConfigs()->get('Attendee')
                ]
            )
        );

        // Import registration payments AFTER the attendees because there may be queries that join to the attendee
        // table, and if the attendee doesn't yet exist, no registrations will be found.
        if ($payment instanceof EE_Payment) {
            $this->commandBus()->execute(
                $this->commandFactory()->getNew(
                    'EventEspresso\AttendeeImporter\domain\services\commands\ImportRegistrationPaymentCommand',
                    [
                        $registration,
                        $payment,
                        $command->inputData(),
                        $command->getConfig()->getModelConfigs()->get('Registration_Payment')
                    ]
                )
            );
        }

        $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\AttendeeImporter\domain\services\commands\ImportAnswersCommand',
                [
                    $registration,
                    $command->inputData()
                ]
            )
        );

        return null;
    }
}
