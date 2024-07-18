<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Parameter;
use App\Entity\ParameterValidation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class AppFixtures extends Fixture
{
    private ParameterBagInterface $params;
    private LoggerInterface $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
    }

    public function load(ObjectManager $manager): void
    {
        $filesystem = new Filesystem();
        $jsonDecoder = new JsonDecode();

        try {
            $filePath = $this->params->get('kernel.project_dir') . '/src/DataFixtures/data/settings.json';
            $fileContents = $filesystem->readFile($filePath);
            $data = $jsonDecoder->decode($fileContents, JsonEncoder::FORMAT, ['json_decode_associative' => true]);

            $this->import($data, $manager);
            $manager->flush();
        } catch (IOExceptionInterface $exception) {
            $this->logger->error("An error occurred while reading file at " . $exception->getPath());
        } catch (Exception $e) {
            $this->logger->error("An error occurred: " . $e->getMessage());
        }
    }

    private function import(array $data, ObjectManager $manager): Category
    {
        $category = new Category();
        $category->setName($data['url']);
        if (array_key_exists('priority', $data)) {
            $category->setPriority($data['priority']);
        }
        if (array_key_exists('parameters', $data)) {
            foreach ($data['parameters'] as $parameterData) {
                $this->importParameter($category, $parameterData, $manager);
            }
        }

        if (array_key_exists('children', $data)) {
            foreach ($data['children'] as $node) {
                if (array_key_exists('file', $node)) {
                    $filesystem = new Filesystem();
                    $jsonDecoder = new JsonDecode();
                    $filePath = $this->params->get('kernel.project_dir') . $node['file'];
                    $fileContents = $filesystem->readFile($filePath);
                    $nodeData = $jsonDecoder->decode($fileContents, JsonEncoder::FORMAT, ['json_decode_associative' => true]);
                    $childCategory = $this->import($nodeData, $manager);
                    $category->addChild($childCategory);
                } else {
                    $childCategory = $this->import($node, $manager);
                    $category->addChild($childCategory);
                }
            }
        }
        $manager->persist($category);
        return $category;
    }

    public function importParameter(Category $category, mixed $data, ObjectManager $manager, ?Parameter $parent = null): void
    {
        $parameter = new Parameter();
        $parameter->setCategory($category);
        $parameter->setName($data['name']);
        $parameter->setPostForm($data['post_form']);
        $parameter->setParent($parent);
        if (array_key_exists('search_form', $data)) {
            $parameter->setSearchForm($data['search_form']);
        }
        if (array_key_exists('search_priority', $data)) {
            $parameter->setSearchPriority($data['search_priority']);
        }
        if (array_key_exists('priority', $data)) {
            $parameter->setPriority($data['priority']);
        }
        if (array_key_exists('regulations', $data)) {
            foreach ($data['regulations'] as $regulationData) {
                $parameterValidation = new ParameterValidation();
                $parameterValidation->setParameter($parameter);
                $parameterValidation->setConstraintClass($regulationData['class']);
                $manager->persist($parameterValidation);
            }
        }
        $manager->persist($parameter);
        if (array_key_exists('parameters', $data)) {
            foreach ($data['parameters'] as $child) {
                $this->importParameter($category, $child, $manager, $parameter);
            }
        }
    }
}