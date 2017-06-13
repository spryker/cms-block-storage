<?php


namespace Spryker\Zed\CmsGui\Communication\Form\Glossary;


use Generated\Shared\Transfer\CmsBlockGlossaryPlaceholderTranslationTransfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsBlockGlossaryPlaceholderTranslationForm extends AbstractType
{
    const FIELD_FK_LOCALE = 'fkLocale';
    const FIELD_TRANSLATION = 'translation';
    const FIELD_LOCALE_NAME = 'localeName';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addLocaleField($builder)
            ->addTranslationField($builder)
            ->addLocaleNameField($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsBlockGlossaryPlaceholderTranslationTransfer::class,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    public function addLocaleField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FK_LOCALE, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    public function addLocaleNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_LOCALE_NAME, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addTranslationField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_TRANSLATION, TextareaType::class, [
            'label' => 'Content',
            'attr' => [
                'class' => 'html-editor',
            ],
            'required' => false,
        ]);

        return $this;
    }

}