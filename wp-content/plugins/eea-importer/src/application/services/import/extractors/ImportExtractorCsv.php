<?php

namespace EventEspresso\AttendeeImporter\application\services\import\extractors;

use EventEspresso\core\exceptions\InvalidFilePathException;
use LogicException;
use RuntimeException;
use SplFileObject;

/**
 * Class ImportExtractorCsv
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportExtractorCsv extends ImportExtractorBase
{
    /**
     * @var string
     */
    protected $filepath;

    /**
     * @var SplFileObject
     */
    protected $fileObject;

    /**
     * @var int
     */
    protected $size = null;

    /**
     * Sets what source to extract data from. Eg filepath of uploaded CSV, database table names, request parameter, etc.
     * It's up to the extractor to interpret what was provided in order to get items.
     * @since 1.0.0.p
     * @param string $source
     * @return void
     * @throws InvalidFilePathException
     * @throws LogicException
     * @throws RuntimeException
     */
    public function setSource($source)
    {
        $this->filepath = (string) $source;

        $this->fileObject = new SplFileObject($this->filepath, 'r');
        $this->fileObject->setFlags(SplFileObject::READ_CSV);
        if ($this->fileObject->eof()) {
            throw new InvalidFilePathException(
                $source,
                esc_html__('No comma-separated data was retrieved from the CSV file provided.', 'event_espresso')
            );
        }
    }

    /**
     * Gets an array of the raw data from the source (eg a row from the CSV, a JSON object,
     * @since 1.0.0.p
     * @return array
     */
    public function getItemAt($offset)
    {
        if ($offset >= $this->countItems()) {
            return null;
        }
        $this->fileObject->seek($offset);
        return $this->fileObject->current();
    }

    /**
     * Gets the next row.
     * @since 1.0.0.p
     * @return array|null if all done.
     */
    public function getNextItem()
    {
        if ($this->fileObject->eof()) {
            return null;
        }
        return $this->fileObject->fgetcsv();
    }

    /**
     * Counts the number of items to import
     * @since 1.0.0.p
     * @return int
     */
    public function countItems()
    {
        if ($this->size === null) {
            $this->fileObject->seek(PHP_INT_MAX);
            $this->size = $this->fileObject->key() + 1;
        }
        return $this->size;
    }
}
// End of file ImportExtractorCsv.php
// Location: EventEspresso\AttendeeImporter\application\services\import\extractors/ImportExtractorCsv.php
