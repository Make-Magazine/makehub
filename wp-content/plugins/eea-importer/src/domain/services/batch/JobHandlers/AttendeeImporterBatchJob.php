<?php

namespace EventEspresso\AttendeeImporter\domain\services\batch\JobHandlers;

use Exception;
use EE_Error;
// Import Infusionsoft. We'll check the add-on is active before trying to use it.
use EED_Infusionsoft;
use EventEspresso\AttendeeImporter\domain\services\import\csv\attendees\config\ImportCsvAttendeesConfig;
use EventEspresso\AttendeeImporter\domain\services\import\managers\ImportCsvAttendeesManager;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidFilePathException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\loaders\LoaderFactory;
use EventEspresso\core\services\options\JsonWpOptionManager;
use EventEspressoBatchRequest\Helpers\BatchRequestException;
use EventEspressoBatchRequest\Helpers\JobParameters;
use EventEspressoBatchRequest\Helpers\JobStepResponse;
use EventEspressoBatchRequest\JobHandlerBaseClasses\JobHandler;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use EEH_File;

/**
 * Class AttendeeImporterBatchJob
 *
 * Takes care of breaking up the often big job of importing a CSV file into the DB into smaller steps.
 * Offloads the actual work though to command objects.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class AttendeeImporterBatchJob extends JobHandler
{

    /**
     * @var ImportCsvAttendeesConfig
     */
    private $config;
    /**
     * @var JsonWpOptionManager
     */
    private $option_manager;
    /**
     * @var ImportCsvAttendeesManager
     */
    private $manager;

    public function __construct(
        ImportCsvAttendeesConfig $config,
        JsonWpOptionManager $option_manager,
        ImportCsvAttendeesManager $manager
    ) {
        $this->config = $config;
        $this->option_manager = $option_manager;
        $this->option_manager->populateFromDb($config);
        $this->manager = $manager;
    }


    /**
     * Performs any necessary setup for starting the job. This is also a good
     * place to setup the $job_arguments which will be used for subsequent HTTP requests
     * when continue_job will be called
     * @param JobParameters $job_parameters
     * @return JobStepResponse
     * @throws LogicException
     * @throws RuntimeException
     */
    public function create_job(JobParameters $job_parameters)
    {
        $this->manager->getExtractor()->setSource($this->config->getFile());

        // Get the header row
        $csv_row = $this->manager->getExtractor()->getNextItem();
        if (empty($csv_row)) {
            // The file's totally empty. That's whack.
            $job_parameters->set_status(JobParameters::status_error);
            return new JobStepResponse(
                $job_parameters,
                esc_html__('No comma-separated data was retrieved from the CSV file provided.', 'event_espresso')
            );
        }
        $job_parameters->set_extra_data(
            [
                'headers' => $csv_row
            ]
        );
        $job_parameters->set_job_size($this->manager->getExtractor()->countItems() - 1);
        return new JobStepResponse(
            $job_parameters,
            esc_html__('Beginning import...', 'event_espresso')
        );
    }

    /**
     * Performs another step of the job
     * @param JobParameters $job_parameters
     * @param int $batch_size
     * @return JobStepResponse
     * @throws InvalidDataTypeException
     * @throws InvalidFilePathException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     */
    public function continue_job(JobParameters $job_parameters, $batch_size = 50)
    {
        $this->manager->getExtractor()->setSource($this->config->getFile());

        $command_bus = LoaderFactory::getLoader()->getShared('EventEspresso\core\services\commands\CommandBus');
        // grab the line from the file
        $processed_this_batch = 0;
        $column_headers = $job_parameters->extra_datum('headers');
        // Importing can be pretty expensive, so let's slow it down a bit.
        $batch_size /= 2;

        // Importing with Infusionsoft is more expensive yet, so let's slow it down some more.
        try {
            if (class_exists('EED_Infusionsoft')
                && EED_Infusionsoft::infusionsoft_connection()) {
                $batch_size /= 4;
            }
        } catch (Exception $e) {
            // Infusionsoft connection didn't work. No need to slow it down any more then.
        }

        // At least try to import one at a time.
        $batch_size = max($batch_size, 1);
        while ($processed_this_batch < $batch_size) {
            $csv_row = $this->manager->getExtractor()->getItemAt(
                $job_parameters->units_processed() + 1 + $processed_this_batch
            );
            if (! $csv_row) {
                break;
            }
            if (is_array($csv_row) && count($csv_row) === count($column_headers)) {
                $command_bus->execute(
                    $this->manager->getImportCommand(
                        array_combine(
                            $column_headers,
                            $csv_row
                        )
                    )
                );
            }
            $processed_this_batch++;
        };
        $job_parameters->mark_processed($processed_this_batch);
        if ($job_parameters->units_processed() >= $job_parameters->job_size()) {
            $job_parameters->set_status(JobParameters::status_complete);
        }

        return new JobStepResponse(
            $job_parameters,
            sprintf(
                esc_html__('%1$s rows imported.', 'event_espresso'),
                $processed_this_batch
            )
        );
    }

    /**
     * Performs any clean-up logic when we know the job is completed
     * @param JobParameters $job_parameters
     * @return JobStepResponse
     * @throws BatchRequestException
     * @throws EE_Error
     */
    public function cleanup_job(JobParameters $job_parameters)
    {
        EEH_File::delete(dirname($this->config->getFile()), true, 'd');
        return new JobStepResponse(
            $job_parameters,
            esc_html__('Summarizing import...', 'event_espresso')
        );
    }
}
// End of file AttendeeImporterBatchJob.php
// Location: ${NAMESPACE}/AttendeeImporterBatchJob.php
