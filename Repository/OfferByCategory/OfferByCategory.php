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

namespace BaksDev\Products\Category\Repository\OfferByCategory;

use BaksDev\Products\Category\Entity\Offers\ProductCategoryOffers;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\ORM\EntityManagerInterface;

final class OfferByCategory implements OfferByCategoryInterface
{

    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Метод получает идентификатор настройки торгового предложения продукта в категории
     */
    public function findProductCategoryOffer(ProductCategoryUid $category): ?ProductCategoryOffers
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('offer');
        $qb->from(ProductCategory::class, 'category');
        $qb->leftJoin(ProductCategoryOffers::class, 'offer', 'WITH', 'offer.event = category.event');
        $qb->where('category.id = :category');
        $qb->setParameter('category', $category, ProductCategoryUid::TYPE);

        return $qb->getQuery()->getOneOrNullResult();
    }
}