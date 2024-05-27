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

namespace BaksDev\Products\Category\Entity\Info;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Неизменяемые данные Категории */


#[ORM\Entity]
#[ORM\Table(name: 'product_category_info')]
#[ORM\Index(columns: ['active'])]
#[ORM\Index(columns: ['url'])]
class ProductCategoryInfo extends EntityState
{
	public const TABLE = 'product_category_info';
	
	/** Связь на событие */
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'info', targetEntity: ProductCategoryEvent::class, fetch: 'EAGER')]
	#[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
	private ?ProductCategoryEvent $event;
	
	/** Семантическая ссылка на раздел */
	#[ORM\Column(type: Types::STRING)]
	private string $url;
	
	/** Статус активности раздела */
	#[ORM\Column(type: Types::BOOLEAN)]
	private bool $active = true;
	
	/** Количество товаров в разделе */
	#[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
	private int $counter = 0;
	
	
	public function __construct(ProductCategoryEvent $event)
	{
		$this->event = $event;
	}

    public function __toString(): string
    {
        return (string) $this->event;
    }
	
	public function getCategory() : ProductCategoryUid
	{
		return $this->event->getCategory();
	}

	public function getEvent() : ?ProductCategoryEvent
	{
		return $this->event;
	}
	
	public function getUrl(): string { return $this->url; }
	
	
	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof ProductCategoryInfoInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function setEntity($dto): mixed
	{
		if($dto instanceof ProductCategoryInfoInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function add() : void
	{
		++$this->counter;
	}
	
	public function sub() : void
	{
		--$this->counter;
	}
	
}
