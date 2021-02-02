<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Error;
use EE_Payment;
use EEM_Payment;
use EEM_Payment_Method;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\CollectionDetailsException;
use EventEspresso\core\services\collections\CollectionLoaderException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class CreateAttendeeCommandHandler
 * generates and validates an Payment
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 */
class ImportPaymentCommandHandler extends CommandHandler
{
    /**
     * @var EEM_Payment_Method
     */
    private $payment_method_model;

    public function __construct(EEM_Payment_Method $payment_method)
    {
        $this->payment_method_model = $payment_method;
    }

    /**
     * @param CommandInterface|ImportPaymentCommand $command
     * @return EE_Payment|null
     * @throws EE_Error
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function handle(CommandInterface $command)
    {
        $payment_data = $command->getFieldsFromMappedData();
        // Don't make a payment if they didn't specify an amount.
        if (empty($payment_data)) {
            return null;
        }

        $payment_data['TXN_ID'] = $command->getTransaction()->ID();
        $payment_data['STS_ID'] = EEM_Payment::status_id_approved;
        $payment_data['PAY_timestamp'] = $command->getTransaction()->get_DateTime_object('TXN_timestamp');
        $payment_data['PAY_source'] = esc_html__('Imported', 'event_espresso');
        $payment_data['PMD_ID'] = $this->payment_method_model->get_var(
            [
                [
                    'PMD_slug' => 'other'
                ]
            ],
            'PMD_ID'
        );
        $payment = EE_Payment::new_instance($payment_data);
        $payment->save();
        return $payment;
    }
}
