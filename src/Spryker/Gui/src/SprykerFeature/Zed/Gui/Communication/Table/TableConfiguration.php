<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Gui\Communication\Table;

class TableConfiguration
{

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $footer;

    /**
     * @var int
     */
    private $pageLength;

    /**
     * @var array
     */
    private $searchableFields;

    /**
     * @var array
     */
    private $sortableFields;

    /**
     * @var string
     */
    private $defaultSortColumnIndex = 0;

    /**
     * @var string
     */
    private $defaultSortDirection = self::SORT_ASC;

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @todo Zed Translation in Template
     *
     * @param array $header Provide php names for table columns
     *   if you are goin to user Propel Query as data population
     *
     * @return void
     */
    public function setHeader(array $header)
    {
        if ($this->isAssoc($header)) {
            $this->header = $header;
        }
    }

    /**
     * @return array
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param array $footer
     *
     * @return void
     */
    public function setFooter(array $footer)
    {
        $this->footer = $footer;
    }

    /**
     * @return self
     */
    public function setFooterFromHeader()
    {
        if (empty($this->getHeader()) === true) {
            return $this;
        }

        $headerKeys = array_keys($this->getHeader());
        $this->setFooter($headerKeys);

        return $this;
    }

    /**
     * @return array
     */
    public function getSortable()
    {
        return $this->sortableFields;
    }

    /**
     * @param array $sortable
     *
     * @return void
     */
    public function setSortable(array $sortable)
    {
        $this->sortableFields = array_intersect($sortable, array_keys($this->header));
    }

    /**
     * @return array
     */
    public function getSearchable()
    {
        return !empty($this->searchableFields) ? $this->searchableFields : array_keys($this->header);
    }

    /**
     * @param array $searchable
     *
     * @return void
     */
    public function setSearchable(array $searchable)
    {
        $this->searchableFields = $searchable;
    }

    /**
     * @return int
     */
    public function getPageLength()
    {
        return $this->pageLength;
    }

    /**
     * @param $length
     *
     * @return void
     */
    public function setPageLength($length)
    {
        $this->pageLength = $length;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param int $columnIndex
     *
     * @return void
     */
    public function setDefaultSortColumnIndex($columnIndex)
    {
        $this->defaultSortColumnIndex = $columnIndex;
    }

    /**
     * @return string
     */
    public function getDefaultSortColumnIndex()
    {
        return $this->defaultSortColumnIndex;
    }

    /**
     * @param string $direction
     *
     * @return void
     */
    public function setDefaultSortDirection($direction)
    {
        $this->defaultSortDirection = $direction;
    }

    /**
     * @return string
     */
    public function getDefaultSortDirection()
    {
        return $this->defaultSortDirection;
    }

    /**
     * @param array $arr
     *
     * @return bool
     */
    private function isAssoc(array $arr)
    {
        return (array_values($arr) !== $arr);
    }

}
