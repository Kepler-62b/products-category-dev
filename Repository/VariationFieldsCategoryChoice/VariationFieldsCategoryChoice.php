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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\VariationFieldsCategoryChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity as ProductCategoryEntity;
use BaksDev\Products\Category\Type\Offers\Id\ProductCategoryOffersUid;
use BaksDev\Products\Category\Type\Offers\Variation\ProductCategoryVariationUid;
use Symfony\Contracts\Translation\TranslatorInterface;


final class VariationFieldsCategoryChoice implements VariationFieldsCategoryChoiceInterface
{

    private TranslatorInterface $translator;
    private ORMQueryBuilder $ORMQueryBuilder;


    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        TranslatorInterface $translator,
    )
    {

        $this->translator = $translator;
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }


    public function getVariationFieldType(ProductCategoryOffersUid $offer): ?ProductCategoryVariationUid
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->setParameter('local', new Locale($this->translator->getLocale()), Locale::TYPE);

        $select = sprintf('new %s(variation.id, trans.name, variation.reference)', ProductCategoryVariationUid::class);
        $qb->select($select);

        $qb->from(ProductCategoryEntity\Offers\ProductCategoryOffers::class, 'offer');

        $qb->join(
            ProductCategoryEntity\Offers\Variation\ProductCategoryVariation::class,
            'variation',
            'WITH',
            'variation.offer = offer.id'
        );

        $qb->leftJoin(
            ProductCategoryEntity\Offers\Variation\Trans\ProductCategoryVariationTrans::class,
            'trans',
            'WITH',
            'trans.variation = variation.id AND trans.local = :local'
        );

        $qb->where('offer.id = :offer');
        $qb->setParameter('offer', $offer, ProductCategoryOffersUid::TYPE);


        /* Кешируем результат ORM */
        return $qb->enableCache('products-product', 86400)->getOneOrNullResult();

    }

}