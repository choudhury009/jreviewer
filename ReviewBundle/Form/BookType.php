<?php

namespace Reviewer\ReviewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',
                array('attr' => array(
                    'placeholder' => 'Title',
                    'style' => 'width: 100px;'
                ),
                    'label' => false
                )
            )
            ->add('author', 'text',
                array('attr' => array(
                    'placeholder' => 'Author',
                    'style' => 'width: 100px;'
                ),
                    'label' => false
                )
            )
            ->add('summary', 'textarea',
                array('attr' => array(
                    'placeholder' => 'Summary',
                    'style' => 'width: 400px;height: 100px;'
                ),
                    'label' => false
                )
            )
            ->add('review', 'textarea',
                array('attr' => array(
                    'placeholder' => 'Review',
                    'style' => 'width: 400px;height: 200px;'
                ),
                    'label' => false
                )
            )
            ->add('media', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'engine'
                )
            )
            ->add('submit','submit',
                array('attr' => array(
                    'class' => 'btn btn-success',
                ))
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reviewer\ReviewBundle\Entity\Book',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reviewer_reviewbundle_book';
    }
}
