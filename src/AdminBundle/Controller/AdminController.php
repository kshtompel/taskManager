<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Task;
use AppBundle\ServersHandle\Servers\ServerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AdminController
 * @package AdminBundle\Controller
 *
 * @TODO Create exception classes with proper codes and messages.
 */
class AdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ServerInterface
     */
    private $dataHandler;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Construct
     *
     * @param \Twig_Environment $twig
     * @param ServerInterface $serverData
     * @param EntityManagerInterface $em ,
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Twig_Environment $twig,
        ServerInterface $serverData,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->dataHandler = $serverData;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $content = $this->twig->render('AdminBundle:Admin:index.html.twig', []);

        return new Response($content);
    }

    /**
     * Task list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function taskList(Request $request)
    {
        $response = new JsonResponse();
        $data = ['page' => 1, 'limit' => 10];
        try {
            $data = $this->dataHandler->getData($request);
            $data += $data;
            $taskRepository = $this->em->getRepository(Task::class);

            $tasks = $taskRepository->getPagedTask($data['limit'], $data['page']);

            $data['paged'] = $tasks;
            $data['total'] = count($tasks);
            $data['status'] = true;
            $this->prepareResponse($response, $request);
            $response->setJson($this->serializer->serialize($data, 'json'));
        } catch (\Exception $e) {
            $this->prepareErrorResponse($response, $e, $data);
        }

        return $response;
    }

    /**
     * Create task.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function taskCreate(Request $request)
    {
        $response = new JsonResponse();
        $data = [];
        try {
            $data = $this->dataHandler->getData($request);
            $task = $this->createTaskFromData($data);
            $errors = $this->validateTask($task);
            if (!empty($errors)) {
                $data = $errors;
                throw new \Exception('Data not valid.');
            }
            $this->em->persist($task);
            $this->em->flush();

            $this->prepareResponse($response, $request);
            $response->setData([
                'status' => true,
                'data' => [
                    'id' => $task->getId()
                ]
            ]);

        } catch (\Exception $e) {
            $this->prepareErrorResponse($response, $e, $data);
        }

        return $response;
    }

    /**
     * Find task.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function taskView(Request $request)
    {
        $response = new JsonResponse();
        $data = [];
        try {
            $data = $this->dataHandler->getData($request);

            if (empty($data['id'])) {
                throw new \Exception('Request not valid id not provided.');
            }

            $taskRepository = $this->em->getRepository(Task::class);
            $task = $taskRepository->find($data['id']);
            if (!$task) {
                throw new \Exception('Task not found with id '.$data['id']);
            }


            $this->prepareResponse($response, $request);
            $data = [
                'status' => true,
                'data' => [
                    'task' => $task
                ]
            ];

            $response->setJson($this->serializer->serialize($data, 'json'));

        } catch (\Exception $e) {
            $this->prepareErrorResponse($response, $e, $data);
        }

        return $response;
    }

    /**
     * Edit task.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function taskEdit(Request $request)
    {
        $response = new JsonResponse();
        $data = [];
        try {
            $data = $this->dataHandler->getData($request);

            $taskRepository = $this->em->getRepository(Task::class);
            $task = $taskRepository->find($data['id']);
            if (!$task) {
                throw new \Exception('Task not found with id '.$data['id']);
            }

            $this->updateTaskFromData($task, $data);
            $this->em->persist($task);
            $this->em->flush();

            $this->prepareResponse($response, $request);
            $response->setJson($this->serializer->serialize([
                'status' => true,
                'data' => $task
            ], 'json'));

        } catch (\Exception $e) {
            $this->prepareErrorResponse($response, $e, $data);
        }

        return $response;
    }

    /**
     * Remove task by id.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function taskRemove(Request $request)
    {
        $response = new JsonResponse();
        $data = [];
        try {
            $data = $this->dataHandler->getData($request);
            $taskRepository = $this->em->getRepository(Task::class);
            $task = $taskRepository->find($data['id']);
            if (!$task) {
                throw new \Exception('Task not found with id '.$data['id']);
            }

            $this->em->remove($task);
            $this->em->flush();

            $this->prepareResponse($response, $request);
            $response->setData([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            $this->prepareErrorResponse($response, $e, $data);
        }

        return $response;
    }

    private function validateTask(Task $task)
    {
        $errors = $this->validator->validate($task);
        $data = [];

        if ($errors->count()) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $data[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        return $data;
    }

    /**
     * Validate request data
     * @param array $data
     * @return bool
     *
     * @TODO move it to separate handler.
     */
    private function dataValidate(array $data)
    {
        return true;
    }

    /**
     * Create new Task from data array.
     *
     * @param array $data
     * @return Task
     * @throws \Exception
     */
    private function createTaskFromData(array $data)
    {
        if ($this->dataValidate($data)) {
            $name = isset($data['name']) ? $data['name'] : '';
            $startDate = isset($data['startedAt']) ? new \DateTime($data['startedAt']) : new \DateTime();
            $finishDate = isset($data['finishedAt']) ? new \DateTime($data['finishedAt']) : new \DateTime();
            $description = isset($data['description']) ? $data['description'] : '';
            $task = new Task($name, $startDate, $finishDate);
            $task->setDescription($description);

            return $task;
        } else {
            throw new \Exception('Data not valid');
        }
    }

    /**
     * Update new Task from data array.
     *
     * @param array $data
     * @return Task
     * @throws \Exception
     */
    private function updateTaskFromData(Task $task, array $data)
    {
        if ($this->dataValidate($data)) {
            $name = isset($data['name']) ? $data['name'] : '';
            $startDate = isset($data['startedAt']) ? new \DateTime($data['startedAt']) : new \DateTime();
            $finishDate = isset($data['finishedAt']) ? new \DateTime($data['finishedAt']) : new \DateTime();
            $description = isset($data['description']) ? $data['description'] : '';
            $task->setName($name);
            $task->setStartedAt($startDate);
            $task->setFinishedAt($finishDate);
            $task->setDescription($description);

            switch ($data['status']) {
                case '1':
                    $task->isNew();
                    break;
                case '2':
                    $task->isPending();
                    break;
                case '3':
                    $task->isFinished();
                    break;
            }

            return $task;
        } else {
            throw new \Exception('Data not valid');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareErrorResponse(Response $response, \Exception $e, array $data = [])
    {
        $json = [
            'status' => false,
            'error'  => [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ];

        if ($data) {
            $json['error']['data'] = $data;
        }

        /** @var JsonResponse $response */
        $response->setData($json);
    }

    /**
     * Prepare response
     *
     * @param Response $response
     * @param Request $request
     */
    protected function prepareResponse(Response $response, Request $request)
    {
        $contentType = $this->dataHandler->getContentType();

        if ($contentType && !$response->headers->get('Content-Type')) {
            $response->headers->set('Content-Type', $contentType, true);
        }
    }
}
