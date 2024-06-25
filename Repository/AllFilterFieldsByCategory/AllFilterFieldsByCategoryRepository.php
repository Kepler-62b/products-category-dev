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

namespace BaksDev\Products\Category\Repository\AllFilterFieldsByCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Section\Field\CategoryProductSectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\CategoryProductSectionFieldTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;

final class AllFilterFieldsByCategoryRepository implements AllFilterFieldsByCategoryInterface
{
    private ?CategoryProductUid $category = null;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает все свойства, участвующие в фильтре
     */
    public function category(CategoryProduct|CategoryProductUid|string $category): self
    {
        if($category instanceof CategoryProduct)
        {
            $category = $category->getId();
        }

        if(is_string($category))
        {
            $category = new CategoryProductUid($category);
        }

        $this->category = $category;

        return $this;
    }

    public function findAll(): array
    {

        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->from(CategoryProduct::TABLE, 'category');


        if($this->category)
        {
            $qb
                ->where('category.id = :category')
                ->setParameter('category', $this->category, CategoryProductUid::TYPE);
        }

        $qb
            ->leftJoin(
                'category',
                CategoryProductSection::TABLE,
                'category_section',
                'category_section.event = category.event'
            );

        $qb
            ->select('category_section_field.id')
            ->addSelect('category_section_field.const')
            ->addSelect('category_section_field.type')
            ->leftJoin(
                'category_section',
                CategoryProductSectionField::class,
                'category_section_field',
                'category_section_field.section = category_section.id AND category_section_field.filter = TRUE'
            );


        $qb
            ->addSelect('category_section_field_trans.name')
            ->leftJoin(
                'category_section_field',
                CategoryProductSectionFieldTrans::TABLE,
                'category_section_field_trans',
                'category_section_field_trans.field = category_section_field.id AND category_section_field_trans.local = :local'
            );


        $qb->orderBy('category_section.sort, category_section_field.sort');

        return $qb->enableCache('products-category')->fetchAllAssociative();
    }

}
