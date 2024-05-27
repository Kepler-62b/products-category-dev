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

namespace BaksDev\Products\Category\Entity;

use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\ORM\Mapping as ORM;

/* Категории продуктов */

#[ORM\Entity()]
#[ORM\Table(name: 'product_category')]
class ProductCategory
{
	
	public const TABLE = 'product_category';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: ProductCategoryUid::TYPE)]
	private ProductCategoryUid $id;
	
	/** ID События */
	#[ORM\Column(type: ProductCategoryEventUid::TYPE, unique: true, nullable: false)]
	private ?ProductCategoryEventUid $event = null;
	
	
	public function __construct()
	{
		$this->id = new ProductCategoryUid();
	}
	
	
	public function getId() : ProductCategoryUid
	{
		return $this->id;
	}
	
	
	public function restore(ProductCategoryUid $id) : void
	{
		$this->id = $id;
	}
	
	
	public function getEvent() : ?ProductCategoryEventUid
	{
		return $this->event;
	}
	
	
	public function setEvent(ProductCategoryEvent|ProductCategoryEventUid $event) : void
	{
		$this->event = $event instanceof ProductCategoryEvent ? $event->getId() : $event;
	}
	
}