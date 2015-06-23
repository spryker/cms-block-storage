<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Library\Import\Reader;

use SprykerFeature\Zed\Library\Import\Input;
use SprykerFeature\Zed\Library\Import\ReaderInterface;
use SprykerFeature\Zed\Library\Import\Exception;

class CsvFileReader implements ReaderInterface
{
    /**
     * @var string
     */
    private $delimiter = ';';

    /**
     * @var string
     */
    private $enclosure = '"';

    /**
     * @var string
     */
    private $escape = '\\';

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * @param mixed $filepath
     * @return Input
     * @throws Exception\SourceNotReadableException
     */
    public function read($filepath)
    {
        if (!is_string($filepath)) {
            throw new Exception\SourceNotReadableException('This is not a valid filename.');
        }

        if (!stream_is_local($filepath)) {
            throw new Exception\SourceNotReadableException(sprintf('This is not a local file "%s".', $filepath));
        }

        if (!file_exists($filepath)) {
            throw new Exception\SourceNotReadableException(sprintf('File "%s" not found.', $filepath));
        }

        try {
            $file = new \SplFileObject($filepath, 'rb');
        } catch (\RuntimeException $e) {
            throw new Exception\SourceNotReadableException(sprintf('Error opening file "%s".', $filepath), 0, $e);
        }

        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

        $data = [];

        $headerRow = null;
        foreach ($file as $row) {
            if (!$row) {
                continue;
            }
            if (null === $headerRow) {
                $headerRow = $row;
                continue;
            }
            
            if (count($headerRow) !== count($row)) {
                throw new Exception\SourceNotReadableException('Some rows contain different count of fields than the header.');
            }

            $data[] = array_combine($headerRow, $row);
        }

        return new Input($filepath, $data);
    }
}
