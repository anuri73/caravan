<?php

namespace App\Controller;

use App\dataprovider\CategoryDataProvider;
use App\dataprovider\DataProviderInterface;
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
}
