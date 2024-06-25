<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Repository\PropertyFieldsCategoryChoice\ModificationCategoryProductSectionField;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\Offers\Variation\CategoryProductVariation;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\Trans\CategoryProductModificationTrans;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationUid;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;

final class ModificationCategoryProductSectionFieldRepository implements ModificationCategoryProductSectionFieldInterface
{
    private ?CategoryProductVariationUid $variation = null;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function variation(CategoryProductVariation|CategoryProductVariationUid|string $variation): self
    {
        if($variation instanceof CategoryProductVariation)
        {
            $variation = $variation->getId();
        }

        if(is_string($variation))
        {
            $variation = new CategoryProductVariationUid($variation);
        }

        $this->variation = $variation;
        return $this;
    }


    public function findAllCategoryProductSectionField(): ?CategoryProductSectionFieldUid
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        $dbal->from(CategoryProductModification::class, 'category_modification');

        if($this->variation)
        {
            $dbal
                ->where('category_modification.variation = :variation')
                ->setParameter(
                    'variation',
                    $this->variation,
                    CategoryProductVariationUid::TYPE
                );
        }

        $dbal->leftJoin(
            'category_modification',
            CategoryProductModificationTrans::class,
            'category_modification_trans',
            'category_modification_trans.modification = category_modification.id 
            AND category_modification_trans.local = :local',
        );

        /** Параметры конструктора объекта гидрации */
        $dbal->select('category_modification.id AS value');
        $dbal->addSelect('category_modification.variation AS const');
        $dbal->addSelect('category_modification_trans.name AS attr');

        return $dbal->fetchHydrate(CategoryProductSectionFieldUid::class);
    }
}
