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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation;

use BaksDev\Core\Type\Field\InputField;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Offers\Variation\ProductCategoryVariationInterface;
use BaksDev\Products\Category\Type\Offers\Variation\ProductCategoryVariationUid;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductCategoryVariation */
final class ProductCategoryVariationDTO implements ProductCategoryVariationInterface
{
	
	#[Assert\Uuid]
	private ?ProductCategoryVariationUid $id = null;
	
	/** Флаг, то что торговое предложение имеет множественные варианты */
	private bool $variation = false;
	
	/** Справочник */
	//private ?string $reference = null;
	
	/** Справочник */
	private ?InputField $reference = null;
	
	/** Загрузка пользовательских изображений */
	private bool $image = false;
	
	/** Торговое предложение с ценой */
	private bool $price = false;
	
	/** Количественный учет */
	private bool $quantitative = false;
	
	/** Множественный вариант с артикулом */
	private bool $article = false;

    /** Множественный вариант  с постфиксом */
    private bool $postfix = false;
	
	/** Настройки локали торгового предложения */
	#[Assert\Valid]
	private ArrayCollection $translate;
	
	
	/** Множественные варианты торговых предложений  */
	private Modification\ProductCategoryModificationDTO $modification;
	
	public function __construct()
	{
		$this->translate = new ArrayCollection();
		$this->modification = new Modification\ProductCategoryModificationDTO();
	}


    /**
     * Id
     */
    public function getId(): ?ProductCategoryVariationUid
    {
        return $this->id;
    }
    

	/** Справочник */

	public function getReference() : ?InputField
	{
		return $this->reference?->getType() ? $this->reference : null;
	}

	public function setReference(?InputField $reference) : void
	{
		$this->reference = $reference;
	}

	/** Загрузка пользовательских изображений */
	
	public function getImage() : bool
	{
		return $this->image;
	}
	
	public function setImage(bool $image) : void
	{
		$this->image = $image;
	}
	
	/** Торговое предложение с ценой */
	
	
	public function getPrice() : bool
	{
		return $this->price;
	}
	
	public function setPrice(bool $price) : void
	{
		$this->price = $price;
	}
	
	/** Настройки локали торгового предложения */
	
	public function getTranslate() : ArrayCollection
	{
		if(!$this->translate->isEmpty())
		{
			$this->variation = true;
		}
		
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->translate) as $locale)
		{
			$OffersTransDTO = new Trans\ProductCategoryVariationTransDTO();
			$OffersTransDTO->setLocal($locale);
			$this->addTranslate($OffersTransDTO);
		}
		
		return $this->translate;
	}
	
	public function addTranslate(Trans\ProductCategoryVariationTransDTO $trans) : void
	{
        if(empty($trans->getLocal()->getLocalValue()))
        {
            return;
        }

		if(!$this->translate->contains($trans))
		{
			$this->translate->add($trans);
		}
	}
	
	public function removeTranslate(Trans\ProductCategoryVariationTransDTO $trans) : void
	{
		$this->translate->removeElement($trans);
	}
	
	/** Вариант с артикулом */
	
	public function getArticle() : bool
	{
		return $this->article;
	}
	
	public function setArticle(bool $article) : void
	{
		$this->article = $article;
	}
	
	
	/** Количественный учет */
	
	public function getQuantitative() : bool
	{
		return $this->quantitative;
	}
	
	public function setQuantitative(bool $quantitative) : void
	{
		$this->quantitative = $quantitative;
	}
	
	/** Флаг, то что торговое предложение имеет множественные варианты */
	
	public function isVariation() : bool
	{
		return $this->variation;
	}
	
	public function setVariation(bool $variation) : void
	{
		$this->variation = $variation;
	}
	
	

	public function getModification() : Modification\ProductCategoryModificationDTO
	{
		return $this->modification;
	}
	

	public function setModification(Modification\ProductCategoryModificationDTO $modification) : void
	{
		$this->modification = $modification;
	}

    /** Множественный вариант  с постфиксом */

    public function isPostfix(): bool
    {
        return $this->postfix;
    }

    public function setPostfix(bool $postfix): void
    {
        $this->postfix = $postfix;
    }

}