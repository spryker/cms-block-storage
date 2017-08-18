<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotion\Communication\Form;

use Generated\Shared\Transfer\DiscountPromotionTransfer;
use Spryker\Zed\Discount\Communication\Form\AbstractDiscountExtensionSubFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiscountPromotionFormType extends AbstractDiscountExtensionSubFormType
{

    const FORM_COLLECTOR_TYPE = 'promotion';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addAbstractSkuField($builder)
            ->addAbstractQuantityField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAbstractSkuField(FormBuilderInterface $builder)
    {
         $builder->add(
             DiscountPromotionTransfer::ABSTRACT_SKU,
             TextType::class,
             [
                 'label' => 'Abstract product sku',
                 'constraints' => [
                     new NotBlank(['groups' => static::FORM_COLLECTOR_TYPE]),
                 ],
             ]
         );

         return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAbstractQuantityField(FormBuilderInterface $builder)
    {
        $builder->add(
            DiscountPromotionTransfer::QUANTITY,
            TextType::class,
            [
                'label' => 'Quantity',
                'constraints' => [
                    new NotBlank(['groups' => static::FORM_COLLECTOR_TYPE]),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    protected function getTemplatePath()
    {
        return 'DiscountPromotion/Form/discount_promotion';
    }

}
