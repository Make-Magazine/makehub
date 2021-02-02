<?php

namespace EventEspresso\AttendeeImporter\domain\services\commands;

use EE_Payment;
use EE_Registration;
use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase;

/**
 * Class CreateAttendeeCommand
 * DTO for passing data to a ImportRegistrationPaymentCommandHandler
 *
 * @package       Event Espresso
 * @author        Michael Nelson
 */
class ImportRegistrationPaymentCommand extends ImportSingleModelBase
{
    /**
     * an existing registration
     *
     * @var EE_Registration $registration
     */
    protected $registration;

    /**
     * @var EE_Payment
     */
    private $payment;


    /**
     * CreateAttendeeCommand constructor.
     *
     * @param EE_Registration $registration
     * @param EE_Payment $payment
     * @param array $csv_row
     * @param ImportModelConfigBase $config
     */
    public function __construct(
        EE_Registration $registration,
        EE_Payment $payment,
        array $csv_row,
        ImportModelConfigBase $config
    ) {
        parent::__construct($csv_row, $config);
        $this->registration = $registration;
        $this->payment = $payment;
    }

    /**
     * @return EE_Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @return EE_Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
