<?php

namespace Sprykerfeature\Zed\Library\Propel\Builder;

class QueryBuilder extends \Propel\Generator\Builder\Om\QueryBuilder
{

    /**
     * Change default propel behaviour
     *
     * @param $objName
     * @param $clsName
     * @return string
     */
    public function buildObjectInstanceCreationCode($objName, $clsName)
    {
        $bundle = $this->getBundleName();

        return "
            /* @var \$locator \\Generated\\Zed\\Ide\\AutoCompletion */
            \$locator = \\SprykerEngine\\Zed\\Kernel\\Locator::getInstance();
            $objName = \$locator->"
        . lcfirst($bundle)
        . "()->entity"
        . str_replace('Child', '', $this->getObjectClassName()) . "();";
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getBundleName()
    {
        if (preg_match('/Zed.(.*?).Persistence/', $this->getClasspath(), $matches)) {
            return $matches[1];
        }

        throw new \Exception('Could not extract bundle name!');
    }

    protected function addFindPkSimple(&$script)
    {
        $table = $this->getTable();

        // this method is not needed if the table has no primary key
        if (!$table->hasPrimaryKey()) {
            return '';
        }

        $platform = $this->getPlatform();
        $tableMapClassName = $this->getTableMapClassName();
        $ARClassName = $this->getObjectClassName();
        $this->declareClassFromBuilder($this->getStubObjectBuilder());
        $this->declareClasses('\PDO');
        $selectColumns = array();
        foreach ($table->getColumns() as $column) {
            if (!$column->isLazyLoad()) {
                $selectColumns [] = $this->quoteIdentifier($column->getName());
            }
        }
        $conditions = array();
        foreach ($table->getPrimaryKey() as $index => $column) {
            $conditions [] = sprintf('%s = :p%d', $this->quoteIdentifier($column->getName()), $index);
        }
        $query = sprintf(
            'SELECT %s FROM %s WHERE %s',
            implode(', ', $selectColumns),
            $this->quoteIdentifier($table->getName()),
            implode(' AND ', $conditions)
        );
        $pks = array();
        if ($table->hasCompositePrimaryKey()) {
            foreach ($table->getPrimaryKey() as $index => $column) {
                $pks [] = "\$key[$index]";
            }
        } else {
            $pks [] = "\$key";
        }

        $pkHashFromRow = $this->getTableMapBuilder()->getInstancePoolKeySnippet($pks);
        $script .= "
    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed \$key Primary key to use for the query
     * @param     ConnectionInterface \$con A connection object
     *
     * @throws \\Propel\\Runtime\\Exception\\PropelException
     *
     * @return $ARClassName A model object, or null if the key is not found
     */
    protected function findPkSimple(\$key, ConnectionInterface \$con)
    {
        \$sql = '$query';
        try {
            \$stmt = \$con->prepare(\$sql);";
        if ($table->hasCompositePrimaryKey()) {
            foreach ($table->getPrimaryKey() as $index => $column) {
                $script .= $platform->getColumnBindingPHP($column, "':p$index'", "\$key[$index]", '            ');
            }
        } else {
            $pk = $table->getPrimaryKey();
            $column = $pk[0];
            $script .= $platform->getColumnBindingPHP($column, "':p0'", "\$key", '            ');
        }
        $script .= "
            \$stmt->execute();
        } catch (Exception \$e) {
            Propel::log(\$e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', \$sql), 0, \$e);
        }
        \$obj = null;
        if (\$row = \$stmt->fetch(\PDO::FETCH_NUM)) {";

        if ($table->getChildrenColumn()) {
            $script .= "
            \$cls = {$tableMapClassName}::getOMClass(\$row, 0, false);
            /** @var $ARClassName \$obj */
            " . $this->buildObjectInstanceCreationCode('$obj', '$cls') . "
            ;";
        } else {
            $script .= "
            /** @var $ARClassName \$obj */
            " . $this->buildObjectInstanceCreationCode('$obj', '$cls') . "
            ";
        }
        $script .= "
            \$obj->hydrate(\$row);
            {$tableMapClassName}::addInstanceToPool(\$obj, $pkHashFromRow);
        }
        \$stmt->closeCursor();

        return \$obj;
    }
";
    }
}
