<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Library\Twig\Loader;

use Zend\Filter\Word\CamelCaseToDash;

class Filesystem extends \Twig_Loader_Filesystem
{

    /**
     * @param array $paths
     * @param string $namespace
     */
    public function setPaths($paths, $namespace = self::MAIN_NAMESPACE)
    {
        $this->paths = array();
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * @param string $path
     * @param string $namespace
     */
    public function addPath($path, $namespace = self::MAIN_NAMESPACE)
    {
        // invalidate the cache
        $this->cache = array();
        $this->paths[] = rtrim($path, '/\\');
    }

    /**
     * @param string $path
     * @param string $namespace
     */
    public function prependPath($path, $namespace = self::MAIN_NAMESPACE)
    {
        // invalidate the cache
        $this->cache = array();

        $path = rtrim($path, '/\\');

        if (empty($this->paths)) {
            $this->paths[] = $path;
        } else {
            array_unshift($this->paths, $path);
        }
    }

    /**
     * @param string $bundle
     *
     * @return array
     */
    protected function getPathsForBundle($bundle)
    {
        $paths = [];
        foreach ($this->paths as $path) {
            $path = sprintf($path, $bundle);
            if (strpos($path, '*') !== false) {
                $path = glob($path);
                if (count($path) > 0) {
                    $paths[] = $path[0];
                }
            } else {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate($name)
    {
        $name = (string) $name;

        // normalize name
        $name = str_replace(array('///','//','\\'), '/', $name);

        $nameParts = explode('/', $name);
        $templateName = array_pop($nameParts);
        $filter = new CamelCaseToDash();
        $templateName = strtolower($filter->filter($templateName));
        array_push($nameParts, $templateName);
        $name = implode('/', $nameParts);

        if (isset($this->cache[$name])) {
            if ($this->cache[$name] !== false) {
                return $this->cache[$name];
            } else {
                throw new \Twig_Error_Loader(sprintf('Unable to find template "%s" (cached).', $name));
            }
        }

        $this->validateName($name);

        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                $this->cache[$name] = false;
                throw new \Twig_Error_Loader(sprintf('Malformed bundle template name "%s" (expecting "@bundle/template_name").', $name));
            }
            $bundle = ucfirst(substr($name, 1, $pos - 1));
            $templateName = substr($name, $pos + 1);

            return $this->load($name, $bundle, $templateName);
        }

        $name = '/' . ltrim($name, '/');
        if (false === $pos = strpos(ltrim($name, '/'), '/')) {
            $this->cache[$name] = false;
            throw new \Twig_Error_Loader(sprintf('Malformed bundle template name "%s" (expecting "@bundle/template_name").', $name));
        }
        $bundle = ucfirst(substr($name, 1, $pos));
        $templateName = substr($name, $pos + 2);
        return $this->load($name, $bundle, $templateName);
    }

    /**
     * @param $name
     * @param $bundle
     * @param $templateName
     * @return string
     * @throws \Twig_Error_Loader
     */
    protected function load($name, $bundle, $templateName)
    {
        $paths = $this->getPathsForBundle($bundle);
        foreach ($paths as $path) {
            if (is_file($path . '/' . $templateName)) {
                return $this->cache[$name] = $path . '/' . $templateName;
            }
        }

        $this->cache[$name] = false;
        throw new \Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $templateName, implode(', ', $paths)));
    }
}
