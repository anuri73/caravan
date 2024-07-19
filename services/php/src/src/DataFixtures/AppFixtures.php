<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Parameter;
use App\Entity\ParameterValidation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class AppFixtures extends Fixture
{
    private ParameterBagInterface $params;
    private LoggerInterface $logger;
    private Filesystem $filesystem;
    private JsonDecode $jsonDecoder;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->params = $params;
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
        $this->jsonDecoder = new JsonDecode();
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        try {
            $this->importFromFile('/src/DataFixtures/data/settings.json', $manager);
            $this->importUsers($manager);
            $manager->flush();
        } catch (Exception $e) {
            $this->logger->error("An error occurred: " . $e->getMessage());
        }
    }

    private function importCategory(array $data, ObjectManager $manager): Category
    {
        $category = new Category();
        $category->setName($data['url']);
        $category->setPriority($data['priority'] ?? null);

        $this->importParameters($category, $data['parameters'] ?? [], $manager);
        $this->importChildCategories($category, $data['children'] ?? [], $manager);

        $manager->persist($category);
        return $category;
    }

    private function importParameters(Category $category, array $parameters, ObjectManager $manager, ?Parameter $parent = null): void
    {
        foreach ($parameters as $data) {
            $parameter = new Parameter();
            $parameter->setCategory($category);
            $parameter->setName($data['name']);
            $parameter->setPostForm($data['post_form']);
            $parameter->setParent($parent);
            $parameter->setSearchForm($data['search_form'] ?? null);
            $parameter->setSearchPriority($data['search_priority'] ?? null);
            $parameter->setPriority($data['priority'] ?? null);

            $this->importRegulations($parameter, $data['regulations'] ?? [], $manager);
            $manager->persist($parameter);

            $this->importParameters($category, $data['parameters'] ?? [], $manager, $parameter);
        }
    }

    private function importRegulations(Parameter $parameter, array $regulations, ObjectManager $manager): void
    {
        foreach ($regulations as $regulationData) {
            $parameterValidation = new ParameterValidation();
            $parameterValidation->setParameter($parameter);
            $parameterValidation->setConstraintClass($regulationData['class']);
            $manager->persist($parameterValidation);
        }
    }

    private function importChildCategories(Category $category, array $children, ObjectManager $manager): void
    {
        foreach ($children as $child) {
            $childCategory = array_key_exists('file', $child)
                ? $this->importFromFile($child['file'], $manager)
                : $this->importCategory($child, $manager);

            $category->addChild($childCategory);
        }
    }

    private function importFromFile(string $file, ObjectManager $manager): Category
    {
        $filePath = $this->params->get('kernel.project_dir') . $file;
        $fileContents = $this->filesystem->readFile($filePath);
        $data = $this->jsonDecoder->decode($fileContents, JsonEncoder::FORMAT, ['json_decode_associative' => true]);

        return $this->importCategory($data, $manager);
    }

    private function importUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("urmat.zhenaliev@gmail.com");
        $user->setPasswordHash($this->passwordHasher->hashPassword(
            $user,
            "oZ+\@z£t6J<w}c2B1="
        ));
        $manager->persist($user);
        $manager->flush();
    }
}