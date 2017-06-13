<?php


namespace Spryker\Zed\CmsGui\Communication\Form\Glossary;


use Spryker\Zed\CmsGui\Communication\Form\ArrayObjectTransformerTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsBlockGlossaryForm extends AbstractType
{
    const FIELD_GLOSSARY_PLACEHOLDERS = 'glossaryPlaceholders';
    const OPTION_DATA_CLASS_PLACEHOLDERS = 'data_class_glossary_placeholders';

    use ArrayObjectTransformerTrait;

    /**
     * @var \Symfony\Component\Form\FormTypeInterface
     */
    protected $cmsBlockGlossaryPlaceholderForm;

    public function __construct(CmsBlockGlossaryPlaceholderForm $cmsBlockGlossaryPlaceholderForm)
    {
        $this->cmsBlockGlossaryPlaceholderForm = $cmsBlockGlossaryPlaceholderForm;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_block_glossary';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCmsBlockGlossaryPlaceholderFormCollection($builder, $options);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(static::OPTION_DATA_CLASS_PLACEHOLDERS);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addCmsBlockGlossaryPlaceholderFormCollection(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_GLOSSARY_PLACEHOLDERS, CollectionType::class, [
            'type' => $this->cmsBlockGlossaryPlaceholderForm,
            'allow_add' => true,
            'entry_options' => [
                'data_class' => $options[static::OPTION_DATA_CLASS_PLACEHOLDERS],
            ],
        ]);

        $builder->get(static::FIELD_GLOSSARY_PLACEHOLDERS)
            ->addModelTransformer($this->createArrayObjectModelTransformer());

        return $this;
    }

}