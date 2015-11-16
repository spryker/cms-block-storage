<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Acl\Communication\Form;

use Orm\Zed\Acl\Persistence\Map\SpyAclRuleTableMap;
use SprykerFeature\Zed\Gui\Communication\Form\AbstractForm;

class RulesetForm extends AbstractForm
{

    const BUNDLE = 'bundle';
    const CONTROLLER = 'controller';
    const ACTION = 'action';
    const TYPE = 'type';

    /**
     * @return self
     */
    protected function buildFormFields()
    {
        $this->addBundleName()
            ->addControllerName()
            ->addActionName()
            ->addPermissionSelect();

        return $this;
    }

    /**
     * @return self
     */
    protected function addBundleName()
    {
        $this->addText(
            self::BUNDLE,
            [
                'label' => 'Bundle',
                'constraints' => [
                    $this->getConstraints()->createConstraintNotBlank(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return self
     */
    protected function addControllerName()
    {
        $this->addText(
            self::CONTROLLER,
            [
                'label' => 'Controller',
                'constraints' => [
                    $this->getConstraints()->createConstraintNotBlank(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return self
     */
    protected function addActionName()
    {
        $this->addText(
            self::ACTION,
            [
                'label' => 'Action',
                'constraints' => [
                    $this->getConstraints()->createConstraintNotBlank(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return self
     */
    protected function addPermissionSelect()
    {
        $this->addSelect(
            self::TYPE,
            [
                'label' => 'Permission',
                'choices' => $this->getPermissionSelectChoices(),
                'placeholder' => false,
            ]
        );

        return $this;
    }

    /**
     * @return array
     */
    protected function getPermissionSelectChoices()
    {
        return array_combine(
            SpyAclRuleTableMap::getValueSet(SpyAclRuleTableMap::COL_TYPE),
            SpyAclRuleTableMap::getValueSet(SpyAclRuleTableMap::COL_TYPE)
        );
    }

    /**
     * Set the values for fields
     *
     * @return array
     */
    protected function populateFormFields()
    {
    }

    /**
     * @return string
     */
    protected function getFormName()
    {
        return 'ruleset_form';
    }

}
