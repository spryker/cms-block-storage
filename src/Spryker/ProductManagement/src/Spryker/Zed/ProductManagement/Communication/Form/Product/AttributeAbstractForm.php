<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Form\Product;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\Library\Collection\Collection;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use Spryker\Zed\ProductManagement\Business\Attribute\AttributeInputManager;
use Spryker\Zed\ProductManagement\Communication\Form\AbstractSubForm;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\AbstractProductFormDataProvider;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider;
use Spryker\Zed\ProductManagement\Communication\Form\ProductFormAdd;
use Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AttributeAbstractForm extends AbstractSubForm
{

    const FIELD_NAME = 'name';
    const FIELD_VALUE = 'value';
    const FIELD_VALUE_HIDDEN_ID = 'value_hidden_id';

    const OPTION_ATTRIBUTE = 'option_attribute';

    const VALIDATION_GROUP_ATTRIBUTE_VALUE = 'validation_group_attribute_value';

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface
     */
    protected $productManagementQueryContainer;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $localeTransfer;

    /**
     * @var \Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider
     */
    protected $localeProvider;

    /**
     * @param string $name
     * @param \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface $productManagementQueryContainer
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     */
    public function __construct(
        $name,
        ProductManagementQueryContainerInterface $productManagementQueryContainer,
        LocaleProvider $localeProvider,
        LocaleTransfer $localeTransfer = null
    ) {
        parent::__construct($name);

        $this->productManagementQueryContainer = $productManagementQueryContainer;
        $this->localeProvider = $localeProvider;
        $this->localeTransfer = $localeTransfer;
    }

    /**
     * @param string $name
     * @param string $attributes
     *
     * @return array
     */
    protected function getValueFieldConfig($name, $attributes)
    {
        $isDisabled = $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_NAME_DISABLED];

        return [
            'read_only' => $isDisabled,
            'label' => false,
            'attr' => [
                'class' => 'attribute_metadata_value',
                'style' => '',
                'product_specific' => $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_PRODUCT_SPECIFIC],
                'id_attribute' => $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_ID]
            ],
            'constraints' => [
                new NotBlank([
                    'groups' => [self::VALIDATION_GROUP_ATTRIBUTE_VALUE]
                ]),
            ]
        ];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(self::OPTION_ATTRIBUTE);

        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $groups = [ProductFormAdd::VALIDATION_GROUP_ATTRIBUTE_ABSTRACT];
                $originalData = $form->getConfig()->getData();
                $submittedData = $form->getData();

                if ($submittedData[self::FIELD_NAME] && !$submittedData[self::FIELD_VALUE]) {
                    $groups[] = self::VALIDATION_GROUP_ATTRIBUTE_VALUE;
                }

                return $groups;
            },
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addCheckboxNameField($builder, $options)
            ->addValueField($builder, $options)
            ->addValueIdHiddenField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addCheckboxNameField(FormBuilderInterface $builder, array $options)
    {
        $attributes = $options[AttributeAbstractForm::OPTION_ATTRIBUTE];

        $name = $builder->getName();
        $label = $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_LABEL];
        $isDisabled = $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_NAME_DISABLED];
        $isProductSpecific = $attributes[$name][AbstractProductFormDataProvider::FORM_FIELD_PRODUCT_SPECIFIC];

        $builder
            ->add(self::FIELD_NAME, 'checkbox', [
                'label' => $label,
                'read_only' => $isDisabled,
                'disabled' => $isDisabled,
                'attr' => [
                    'class' => 'attribute_metadata_checkbox',
                    'product_specific' => $isProductSpecific,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addValueIdHiddenField(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_VALUE_HIDDEN_ID, 'hidden', []);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addValueField(FormBuilderInterface $builder, array $options = [])
    {
        $name = $builder->getName();
        $attributes = $options[self::OPTION_ATTRIBUTE];
        $attributeData = new Collection($attributes[$name]);

        $inputManager = new AttributeInputManager();
        $inputType = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_INPUT_TYPE);
        $allowInput = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_ALLOW_INPUT);
        $isMultiple = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_MULTIPLE);
        $isDisabled = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_NAME_DISABLED);
        $value = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_VALUE);
        $valueName = $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_NAME);
        $input = $inputManager->getSymfonyInputType($inputType, $value, $allowInput, $isMultiple);
        $config = $this->getValueFieldConfig($name, $attributes);

        $config['attr']['style'] .= ' width: 250px';
        $config['attr']['data-value'] = null;

        $idLocale = $this->localeProvider->getCurrentLocale()->getIdLocale();
        if ($this->localeTransfer instanceof LocaleTransfer) {
            $idLocale = $this->localeTransfer->getIdLocale();
        }

        $existingValue = $this->productManagementQueryContainer
            ->queryFindAttributeByValueOrTranslation(
                $attributeData->get(AbstractProductFormDataProvider::FORM_FIELD_ID),
                $idLocale,
                $value
            )->findOne();

        if (strtolower($input) === 'select2') {
            $input = new Select2ComboBoxType();
            $config['multiple'] = $isMultiple;
            $config['placeholder'] = '';
            $config['attr']['style'] .= ' width: 250px';
            $config['choices'] = [];
            if ($valueName) {
                $config['choices'] = $this->getChoiceList($name, $attributes[$name], $existingValue, $idLocale);
            }
            $config['attr']['tags'] = true;

            if ($allowInput) {

            }
            else {
                //$config['attr']['class'] .= ' ajax';
            }
        } else {
            if ($allowInput) {
                $config['attr']['class'] .= ' kv_attribute_autocomplete';
            }
        }

        if ($isDisabled) {
            $config = $this->getValueFieldConfig($name, $attributes);
            $config['read_only'] = true;
            $input = $inputManager->getSymfonyInputType(null, $value);
        }

        $builder->add(self::FIELD_VALUE, $input, $config);

        return $this;
    }

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return array
     */
    protected function getChoiceList($name, array $attributes, $existingValue, $idLocale)
    {
        $result = [];
        $attributeValue = $attributes[AbstractProductFormDataProvider::FORM_FIELD_VALUE];

        $valueCollection = $this->productManagementQueryContainer
            ->queryFindAttributeByValueOrTranslation(
                $attributes[AbstractProductFormDataProvider::FORM_FIELD_ID],
                $idLocale
            )->find();

        if (!$existingValue && isset($attributeValue)) {
            $result[null] = $attributeValue;
        }

        foreach ($valueCollection as $entity) {
            $data = $entity->toArray();
            $value = $data['value'];
            if (isset($data['translation'])) {
                $value = $data['translation'];
            }

            $result[$entity->getIdProductManagementAttributeValue()] = $value;
        }

        return $result;
    }

}
