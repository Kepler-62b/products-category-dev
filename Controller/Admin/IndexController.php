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

namespace BaksDev\Products\Category\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Repository\AllCategory\AllCategoryInterface;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCT_CATEGORY')]
final class IndexController extends AbstractController
{
    #[Route('/admin/product/categorys/{cat}/{page<\d+>}', name: 'admin.index', // defaults: ['cat' => null],
        methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllCategoryInterface $allCategory,
        #[MapEntity] ?ProductCategory $cat = null,
        int $page = 0,
    ): Response {
        // Поиск
        $search = new SearchDTO();
        $searchForm = $this->createForm(SearchForm::class, $search);
        $searchForm->handleRequest($request);

        // Получаем список
        $parent = $cat ? new ProductParentCategoryUid($cat->getId()) : null;
        $query = $allCategory->fetchProductParentAllAssociative($search, $parent);

        return $this->render(
            [
                'query' => $query,
                'search' => $searchForm->createView(),
                'parent' => null, // ($cat ? $getParentCategory->get($cat) : null), /* Получаем корневую директорию */
                'parent_id' => $parent,
            ]
        );
    }
}
