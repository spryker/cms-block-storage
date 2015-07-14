<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Gui\Communication\Table;

class TableConfiguration
{

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $sortable;

    private $pageLength;

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @todo Zed Translation in Template
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        if ($this->isAssoc($headers) === true) {
            $this->headers = $headers;
        }
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isAssoc(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @return array
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param array $sortable
     */
    public function setSortable(array $sortable)
    {
        $this->sortable = array_intersect(
            $sortable,
            array_keys($this->headers)
        );
    }

    public function getPageLength()
    {
        return $this->pageLength;
    }

    public function setPageLength($length)
    {
        $this->pageLength = $length;
    }


}
