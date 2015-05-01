<?php
namespace SprykerFeature\Shared\Library\Filter;

class FilterChain implements FilterInterface
{
    /**
     * @var FilterInterface[]|Callable[]
     */
    private $filters = [];

    /**
     * @param string $string
     *
     * @return string
     *
     * @throws \LogicException
     */
    public function filter($string)
    {
        foreach ($this->filters as $filter) {
            if ($filter instanceof FilterInterface) {
                $string = $filter->filter($string);
            } elseif (is_callable($filter)) {
                $string = call_user_func($filter, $string);
            } else {
                throw new \LogicException('The filter is neither a FilterInterface nor a callable.');
            }
        }

        return $string;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function __invoke($string)
    {
        return $this->filter($string);
    }

    /**
     * @param FilterInterface|Callable $filter
     *
     * @return $this
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;

        return $this;
    }
}
