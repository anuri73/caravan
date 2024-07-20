<?php

namespace App\Controller;

use App\DataProvider\DataProviderInterface;
use App\DataProvider\EntityId;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

abstract class CrudController extends AbstractFOSRestController
{
    abstract protected function getDataProvider(): DataProviderInterface;

    abstract protected function createFormType(Request $request): FormInterface;

    abstract protected function createEntityId(Request $request): EntityId;

    abstract protected function serializationGroups(): array;

    public function index(Request $request): Response
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        $data = $this->getDataProvider()->next($offset, $limit);

        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            [
                AbstractNormalizer::GROUPS => $this->serializationGroups()['list'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
            ]
        );
    }

    public function show(Request $request): JsonResponse
    {
        $entity = $this->getDataProvider()->find($this->createEntityId($request));

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }
        return $this->json(
            $entity,
            Response::HTTP_OK,
            [],
            [
                AbstractNormalizer::GROUPS => $this->serializationGroups()['show'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
            ]
        );
    }

    public function create(Request $request): JsonResponse
    {
        $form = $this->createFormType($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->getDataProvider()->add($form->getData());

            return $this->json(
                $entity,
                Response::HTTP_CREATED,
                [],
                [
                    AbstractNormalizer::GROUPS => $this->serializationGroups()['show'],
                ]
            );
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

            return $this->json(
                $entity,
                Response::HTTP_OK,
                [],
                [
                    AbstractNormalizer::GROUPS => $this->serializationGroups()['show'],
                ]
            );
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
}