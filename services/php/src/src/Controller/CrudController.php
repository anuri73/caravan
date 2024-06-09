<?php

namespace App\Controller;

use App\dataprovider\DataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class CrudController extends AbstractController
{
    abstract protected function getDataProvider(): DataProviderInterface;

    abstract protected function createEntity(Request $request);

    abstract protected function updateEntity($entity, Request $request);

    /**
     * @return Response
     */
    public function index(): Response
    {
        $offset = 0;
        $limit = 10;
        return $this->json($this->getDataProvider()->next($offset, $limit));
    }

    public function show(string $id): Response
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->json($entity);
    }

    public function create(Request $request): Response
    {
        $entity = $this->createEntity($request);

        $this->getDataProvider()->add($entity);

        return $this->json($entity, Response::HTTP_CREATED);
    }

    public function update(string $id, Request $request): Response
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        $newEntity = $this->updateEntity($entity, $request);

        $updatedEntity = $this->getDataProvider()->update($newEntity);

        return $this->json($updatedEntity);
    }

    public function delete(string $id): Response
    {
        $entity = $this->getDataProvider()->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $this->json($this->getDataProvider()->delete($entity), Response::HTTP_NO_CONTENT);
    }
}