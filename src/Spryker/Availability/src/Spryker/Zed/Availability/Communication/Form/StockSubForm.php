<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Communication\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StockSubForm extends AbstractType
{
    const FIELD_QUANTITY = 'quantity';
    const FIELD_STOCK_TYPE = 'stockType';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_QUANTITY, 'text', [
            'label' => 'Current Stock'
        ])
        ->add(self::FIELD_STOCK_TYPE, 'text', [
            'label' => 'Stock Type',
            'disabled' => true
        ]);
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'stock_form';
    }
}
