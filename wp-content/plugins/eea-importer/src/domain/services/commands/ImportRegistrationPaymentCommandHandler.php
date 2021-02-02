<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Attendee;
use EE_Error;
use EE_Payment;
use EE_Payment_Processor;
use EE_Registration_Processor;
use EventEspresso\core\exceptions\EntityNotFoundException;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\commands\CommandBusInterface;
use EventEspresso\core\services\commands\CommandFactoryInterface;
use EventEspresso\core\services\commands\CommandInterface;
use EventEspresso\core\services\commands\CompositeCommandHandler;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

/**
 * Class CreateAttendeeCommandHandler
 * generates and validates an Attendee
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 */
class ImportRegistrationPaymentCommandHandler extends CompositeCommandHandler
{
    /**
     * @var EE_Registration_Processor
     */
    private $registration_processor;
    /**
     * @var EE_Payment_Processor
     */
    private $payment_processor;


    /**
     * @param EE_Registration_Processor $registration_processor
     * @param EE_Payment_Processor $payment_processor
     * @param CommandBusInterface $command_bus
     * @param CommandFactoryInterface $command_factory
     */
    public function __construct(
        EE_Registration_Processor $registration_processor,
        EE_Payment_Processor $payment_processor,
        CommandBusInterface $command_bus,
        CommandFactoryInterface $command_factory
    ) {
        parent::__construct($command_bus, $command_factory);
        $this->registration_processor = $registration_processor;
        $this->payment_processor = $payment_processor;
    }


    /**
     * @param CommandInterface|ImportRegistrationPaymentCommand $command
     * @return EE_Attendee
     * @throws EE_Error
     * @throws EntityNotFoundException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function handle(CommandInterface $command)
    {

        $this->payment_processor->update_txn_based_on_payment(
            $command->getPayment()->transaction(),
            $command->getPayment()
        );

        $this->registration_processor->toggle_registration_status_if_no_monies_owing(
            $command->getRegistration(),
            true,
            [
                'payment_updates' => $command->getPayment() instanceof EE_Payment,
                'last_payment' => $command->getPayment(),
            ]
        );

        return null;
    }
}
