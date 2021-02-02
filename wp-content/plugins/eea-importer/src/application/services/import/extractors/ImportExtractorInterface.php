<?php
namespace EventEspresso\AttendeeImporter\application\services\import\extractors;

/**
 * Class Extractor
 *
 * Interface for extractors. Extractors grab and count data from source.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
interface ImportExtractorInterface
{
    /**
     * Sets what source to extract data from. Eg filepath of uploaded CSV, database table names, request parameter, etc.
     * It's up to the extractor to interpret what was provided in order to get items.
     * @since 1.0.0.p
     * @param mixed $source
     * @return void
     */
    public function setSource($source);
    /**
     * Gets an array of the raw data from the source (eg a row from the CSV, a JSON object) at the specified index.
     * @since 1.0.0.p
     * @return array
     */
    public function getItemAt($offset);

    /**
     * Gets the next item after the previously returned item.
     * @since 1.0.0.p
     * @return array
     */
    public function getNextItem();

    /**
     * Counts the number of items to import
     * @since 1.0.0.p
     * @return int
     */
    public function countItems();
}
// End of file Extractor.php
// Location: ${NAMESPACE}/Extractor.php
