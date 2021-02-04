<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Attendee;
use EE_Error;
use EE_Registration_Processor;
use EEM_Registration;
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
class ImportRegistrationCommandHandler extends CompositeCommandHandler
{


    /**
     * @type EE_Registration_Processor $registration_processor
     */
    private $registration_processor;

    /**
     * @var EEM_Registration
     */
    private $registration_model;

    public function __construct(
        EE_Registration_Processor $registration_processor,
        CommandBusInterface $command_bus,
        CommandFactoryInterface $command_factory,
        EEM_Registration $registration_model
    ) {
        parent::__construct($command_bus, $command_factory);
        $this->registration_processor = $registration_processor;
        $this->registration_model = $registration_model;
    }


    /**
     * @param CommandInterface|ImportRegistrationCommand $command
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
        $transaction = $command->getTransaction();

        // Create a registration
        $registration = $this->commandBus()->execute(
            $this->commandFactory()->getNew(
                'EventEspresso\core\services\commands\registration\CreateRegistrationCommand',
                [
                    $transaction,
                    $command->getLineItem(),
                    1,
                    null
                ]
            )
        );

        // Make sure we're using the registration from the entity map. If other code hooked into
        // `AHEE__EEM_Base__insert__end`, and then fetched the object, a different registration is in the entity map
        // than this one, which often creates problems later, so make sure we're using what's in the entity map.
        $registration = $this->registration_model->refresh_entity_map_from_db($registration->ID());

        // update registration based on other related details
        $this->registration_processor->toggle_incomplete_registration_status_to_default(
            $registration,
            false
        );
        $this->registration_processor->toggle_registration_status_for_default_approved_events(
            $registration,
            true
        );
        return $registration;
    }
}
