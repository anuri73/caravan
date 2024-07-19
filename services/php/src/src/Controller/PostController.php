<?php

namespace App\Controller;

use App\DataProvider\DataProviderInterface;
use App\DataProvider\EntityId;
use App\DataProvider\GuidId;
use App\DataProvider\PostDataProvider;
use App\Form\PostFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PostController extends CrudController
{
    private PostDataProvider $postDataProvider;

    public function __construct(PostDataProvider $postDataProvider)
    {
        $this->postDataProvider = $postDataProvider;
    }

    protected function getDataProvider(): DataProviderInterface
    {
        return $this->postDataProvider;
    }

    protected function createFormType(Request $request, mixed $data = null, array $options = []): FormInterface
    {
        $form = $this->createForm(PostFormType::class, $data);

        $form->handleRequest($request);

        return $form;
    }

    protected function createEntityId(Request $request): EntityId
    {
        return new GuidId($request->get('id'));
    }
}