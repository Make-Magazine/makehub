<?php

namespace EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config;

use EE_Base_Class;
use EEM_Ticket;
use EventEspresso\AttendeeImporter\application\services\import\config\ImportConfigBase;
use EventEspresso\AttendeeImporter\application\services\import\config\ImportModelConfigInterface;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\CollectionDetails;
use EventEspresso\core\services\collections\CollectionDetailsException;
use EventEspresso\core\services\collections\CollectionInterface;
use EventEspresso\core\services\collections\CollectionLoader;
use EventEspresso\core\services\collections\CollectionLoaderException;
use InvalidArgumentException;
use LogicException;
use ReflectionException;
use RuntimeException;
use SplFileObject;
use stdClass;

/**
 * Class ImportCsvAttendeesConfig
 *
 * Stores all the input from the user about how to perform the import.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportCsvAttendeesConfig extends ImportConfigBase
{
    /**
     * @var string
     */
    protected $file;


    protected $event_id;

    protected $ticket_id;

    /**
     * @var array keys are question IDs, values are the CSV columns they map to.
     * Maybe we'll think of a more elegant solution than this, but this at least works.
     */
    protected $question_mapping = array();

    /**
     * @var EEM_Ticket
     */
    private $ticket_model;

    public function __construct(EEM_Ticket $ticket_model)
    {
        $this->ticket_model = $ticket_model;
    }

    /**
     * Gets the filepath to read.
     * @since 1.0.0.p
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the filepath of the CSV file.
     * @since 1.0.0.p
     * @param $file_path
     */
    public function setFile($file_path)
    {
        $this->file = $file_path;
    }

    /**
     * @since 1.0.0.p
     * @return SplFileObject
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getFileHandle()
    {
        return new SplFileObject($this->file, 'r');
    }

    /**
     * Gets the name of the WordPress option where this JSON data will be stored.
     * @since 1.0.0.p
     * @return string
     */
    public function getWpOptionName()
    {
        return 'ee_import_csv_attendees_config';
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @param int $event_id
     */
    public function setEventId($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return int
     */
    public function getTicketId()
    {
        return $this->ticket_id;
    }

    /**
     * @since 1.0.0.p
     * @return EE_Ticket
     */
    public function getTicket()
    {
        return $this->ticket_model->get_one_by_ID($this->ticket_id);
    }

    /**
     * @param int $ticket_id
     */
    public function setTicketId($ticket_id)
    {
        $this->ticket_id = $ticket_id;
    }

    /**
     * @return array keys are question IDs, values are CSV column names
     */
    public function getQuestionMapping()
    {
        return $this->question_mapping;
    }

    /**
     * @param array $question_mapping keys are question IDs, values are CSV column names
     */
    public function setQuestionMapping(array $question_mapping)
    {
        $this->question_mapping = [];
        foreach ($question_mapping as $question_id => $column) {
            $this->question_mapping[ (int) $question_id ] = $column;
        }
    }

    public function clearMapping()
    {
        foreach ($this->model_configs as $model_config) {
            $model_config->clearMapping();
        }
        $this->question_mapping = [];
    }

    /**
     * @since 1.0.0.p
     * @return CollectionInterface|ImportModelConfigInterface[]
     * @throws CollectionDetailsException
     * @throws CollectionLoaderException
     */
    protected function initializeModelConfigCollection()
    {
        $loader = new CollectionLoader(
            new CollectionDetails(
                // collection name
                'import_csv_attendees_config_model_configs',
                // collection interface
                'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigBase',
                // FQCNs for classes to add (all classes within that namespace will be loaded)
                [
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportAttendeeConfig',
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportPaymentConfig',
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportRegistrationConfig',
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportRegistrationPaymentConfig',
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportTransactionConfig',
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportLineItemConfig'
                ],
                [],
                '',
                CollectionDetails::ID_CALLBACK_METHOD,
                'getModelName'
            )
        );
        return $loader->getCollection();
    }



    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        $simple_obj = parent::toJsonSerializableData();
        $simple_obj->file = $this->getFile();
        $simple_obj->event_id = $this->getEventId();
        $simple_obj->ticket_id = $this->getTicketId();
        $simple_obj->question_mapping = $this->question_mapping;
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
        parent::fromJsonSerializedData($data);
        if ($data instanceof stdClass) {
            $filepath = isset($data->file) ? $data->file : '';
            $event_id = isset($data->event_id) ? $data->event_id : 0;
            $ticket_id = isset($data->ticket_id) ? $data->ticket_id : 0;
            $this->setFile($filepath);
            $this->setEventId($event_id);
            $this->setTicketId($ticket_id);
            if (isset($data->question_mapping)) {
                $this->setQuestionMapping((array) $data->question_mapping);
            }
            return true;
        }
        return false;
    }
}
// End of file ImportCsvAttendeesConfig.php
// Location: EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config/ImportCsvAttendeesConfig.php
