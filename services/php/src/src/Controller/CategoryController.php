<?php

namespace App\Controller;

use App\DataProvider\CategoryDataProvider;
use App\DataProvider\DataProviderInterface;
use App\DataProvider\EntityId;
use App\DataProvider\NameId;
use App\Form\CategoryFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends CrudController
{
    private CategoryDataProvider $categoryDataProvider;

    public function __construct(CategoryDataProvider $categoryDataProvider)
    {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    protected function getDataProvider(): DataProviderInterface
    {
        return $this->categoryDataProvider;
    }

    protected function createFormType(Request $request, mixed $data = null, array $options = []): FormInterface
    {
        $form = $this->createForm(CategoryFormType::class, $data);

        $form->handleRequest($request);

        return $form;
    }

    protected function createEntityId(Request $request): EntityId
    {
        return new NameId($request->get('name'));
    }

    protected function serializationGroups(): array
    {
        return [
            'list' => 'category_list',
            'show' => 'category_show',
        ];
    }
}
