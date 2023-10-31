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

namespace BaksDev\Products\Category\Entity\Trans;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Перевод Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_trans')]
class ProductCategoryTrans extends EntityState
{
	public const TABLE = 'product_category_trans';
	
	/** Связь на событие */
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: ProductCategoryEvent::class, inversedBy: "translate")]
	#[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
	private readonly ProductCategoryEvent $event;
	
	/** Локаль */
	#[ORM\Id]
	#[ORM\Column(type: Locale::TYPE, length: 2)]
	private readonly Locale $local;
	
	/** Название */
	#[ORM\Column(type: Types::STRING, length: 100)]
	private string $name;
	
	/** Описание */
	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description = null;
	
	public function __construct(ProductCategoryEvent $event)
	{
		$this->event = $event;
	}

    public function __toString(): string
    {
        return (string) $this->event;
    }
	
	
	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof ProductCategoryTransInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto): mixed
	{
		if($dto instanceof ProductCategoryTransInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function getNameByLocale(Locale $locale) : ?string
	{
		if($this->local->getLocalValue() === $locale->getLocalValue())
		{
			return $this->name;
		}
		
		return null;
	}

}
