<?php

namespace App\Controller;

use App\DataProvider\DataProviderInterface;
use App\DataProvider\ParameterDataProvider;
use App\Form\ParameterFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ParameterController extends CrudController
{
    private ParameterDataProvider $parameterDataProvider;

    public function __construct(ParameterDataProvider $parameterDataProvider)
    {
        $this->parameterDataProvider = $parameterDataProvider;
    }

    protected function getDataProvider(): DataProviderInterface
    {
        return $this->parameterDataProvider;
    }

    protected function createFormType(Request $request, mixed $data = null, array $options = []): FormInterface
    {
        $form = $this->createForm(ParameterFormType::class, $data);

        $form->handleRequest($request);

        return $form;
    }
}