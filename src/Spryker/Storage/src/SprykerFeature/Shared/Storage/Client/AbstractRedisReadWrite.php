<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Shared\Storage\Client;

abstract class AbstractRedisReadWrite extends AbstractRedisRead implements ReadWriteInterface
{

    /**
     * @param string $key
     * @param mixed $value
     * @param string $prefix
     * @return mixed
     * @throws \Exception
     */
    public function set($key, $value, $prefix = self::KV_PREFIX)
    {
        $key = $this->getKeyName($key, $prefix);
        $result = $this->getResource()->set($key, $value);
        $this->addWriteAccessStats($key);
        if (!$result) {
            throw new \Exception(
                'could not set redisKey: "' . $key.'" with value: "' . json_encode($value) . '"'
            );
        }

        return $result;
    }

    /**
     * @param array $items
     * @param string $prefix
     * @return bool|mixed
     * @throws \Exception
     */
    public function setMulti(array $items, $prefix = self::KV_PREFIX)
    {
        $data = [];

        foreach ($items as $key => $value) {
            $dataKey = $this->getKeyName($key, $prefix);

            if (!is_scalar($value)) {
                $value = json_encode($value);
            }

            $data[$dataKey] = $value;
        }

        if (count($data) == 0) {
            return false;
        }

        $result = $this->getResource()->mset($data);
        $this->addMultiWriteAccessStats($data);

        if (!$result) {
            throw new \Exception(
                'could not set redisKeys for items: "[' . implode(',', array_keys($items)).']" with values: "[' . implode(',', array_values($items)).']"'
            );
        }

        return $result;
    }

    /**
     * @param string $key
     * @param null|string $prefix
     * @return int
     */
    public function delete($key, $prefix = self::KV_PREFIX)
    {
        $key = $this->getKeyName($key, $prefix);
        $result = $this->getResource()->del([$key]);
        $this->addDeleteAccessStats($key);

        return $result;
    }

    /**
     * @param array $keys
     * @return void
     */
    public function deleteMulti(array $keys)
    {
        $this->getResource()->del($keys);
        $this->addMultiDeleteAccessStats($keys);
    }

    /**
     * @return int
     */
    public function deleteAll()
    {
        $keys = $this->getAllKeys();
        $deleteCount = count($keys);
        $this->deleteMulti($keys);

        return $deleteCount;
    }
}
