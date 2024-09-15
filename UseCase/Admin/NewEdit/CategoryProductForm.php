<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Products\Category\Repository\ParentCategoryChoiceForm\ParentCategoryChoiceInterface;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\CategoryProductOffersDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryProductForm extends AbstractType
{
    private ParentCategoryChoiceInterface $categoryParent;

    public function __construct(ParentCategoryChoiceInterface $categoryParent)
    {
        $this->categoryParent = $categoryParent;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**  Сортировка категории */
        $builder->add('sort', IntegerType::class);

        /** Обложка категории */
        $builder->add('cover', NewEdit\Cover\CategoryProductCoverForm::class);

        /** Неизменяемые свойства категории */
        $builder->add('info', NewEdit\Info\CategoryProductInfoForm::class);


        /** Идентификатор родительской категории */
        $builder->add(
            'parent',
            ChoiceType::class,
            [
                'label' => false,
                'required' => false,
                'choices' => $this->categoryParent->findAll(),
                'choice_value' => function(?ParentCategoryProductUid $type) {
                    return $type?->getValue();
                },
                'choice_label' => function(ParentCategoryProductUid $type) {
                    return ($type->isParent() ? ' - ' : '').$type->getOption();
                },
            ]
        );


        /** Настройки локали категории */
        $builder->add('translate', CollectionType::class, [
            'entry_type' => NewEdit\Trans\CategoryProductTransForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_translate__',
        ]);

        /** Настройки SEO категории */
        $builder->add('seo', CollectionType::class, [
            'entry_type' => NewEdit\Seo\CategoryProductSeoCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_seo__',
        ]);

        /** Посадочные блоки */
        $builder->add('landing', CollectionType::class, [
            'entry_type' => NewEdit\Landing\CategoryProductLandingCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_landing__',
        ]);

        /** Секции свойств продукта категории */
        $builder->add('section', CollectionType::class, [
            'entry_type' => NewEdit\Section\CategoryProductSectionCollectionForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_section__',
        ]);

        /** Торговые предложения */

        /*$builder->add('offer', CollectionType::class, [
            'entry_type' => NewEdit\Offers\ProductCategoryOffersForm::class,
            'entry_options' => ['label' => false],
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__category_offer__'
        ]);*/


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {

            /** @var CategoryProductDTO $data */
            $data = $event->getData();

            if($data->getOffer() === null)
            {
                $data->setOffer(new CategoryProductOffersDTO());
            }

        });


        /** Товары в категории с торговым предложением */
        $builder->add('offer', NewEdit\Offers\CategoryProductOffersForm::class, ['label' => false]);


        /* Сохранить ******************************************************/
        $builder->add(
            'Save',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => CategoryProductDTO::class,
                'method' => 'POST',
                'attr' => ['class' => 'w-100'],
            ]
        );
    }

}
