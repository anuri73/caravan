<?php

namespace App\Controller;

use App\dataprovider\DataProviderInterface;
use App\Entity\Category;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class CrudController extends AbstractFOSRestController
{
    abstract protected function getDataProvider(): DataProviderInterface;

    abstract protected function createFormType(Request $request): FormInterface;

    public function index(Request $request): Response
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return $this->jsonCategory($this->getDataProvider()->next($offset, $limit), Response::HTTP_OK);
    }

    public function show(string $id): JsonResponse
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->jsonCategory($entity, Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $form = $this->createFormType($request);

        if ($form->isValid()) {

            $entity = $this->getDataProvider()->add($form->getData());

            return $this->jsonCategory($entity, Response::HTTP_CREATED);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    public function update(string $id, Request $request): Response
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        $form = $this->createFormType($request, $entity);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->getDataProvider()->update($form->getData());

            return $this->jsonCategory($entity, Response::HTTP_OK);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    public function delete(string $id): Response
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->json($this->getDataProvider()->delete($entity), Response::HTTP_NO_CONTENT);
    }

    public function jsonCategory($entity, $httpStatus): JsonResponse
    {
        return $this->json($entity,
            $httpStatus,
            [],
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (Category $obj) {
                    return [
                        $obj->getName()
                    ];
                }
            ]
        );
    }
}