<?php

namespace App\Controller;

use App\DataProvider\DataProviderInterface;
use App\DataProvider\EntityId;
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

    abstract protected function createEntityId(Request $request): EntityId;

    public function index(Request $request): Response
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return $this->jsonData($this->getDataProvider()->next($offset, $limit), Response::HTTP_OK);
    }

    public function show(Request $request): JsonResponse
    {
        $entity = $this->getDataProvider()->find($this->createEntityId($request));

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->jsonData($entity, Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $form = $this->createFormType($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->getDataProvider()->add($form->getData());

            return $this->jsonData($entity, Response::HTTP_CREATED);
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

    public function update(Request $request): Response
    {
        $entity = $this->getDataProvider()->find($this->createEntityId($request));

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        $form = $this->createFormType($request, $entity);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->getDataProvider()->update($form->getData());

            return $this->jsonData($entity, Response::HTTP_OK);
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

    public function delete(Request $request): Response
    {
        $entity = $this->getDataProvider()->find($this->createEntityId($request));

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->json($this->getDataProvider()->delete($entity), Response::HTTP_NO_CONTENT);
    }

    public function jsonData($entity, $httpStatus): JsonResponse
    {
        return $this->json($entity,
            $httpStatus,
            [],
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    if (method_exists($object, 'getId')) {
                        return $object->getId();
                    }
                    if (method_exists($object, 'getName')) {
                        return $object->getName();
                    }
                    return null;
                }
            ]
        );
    }
}