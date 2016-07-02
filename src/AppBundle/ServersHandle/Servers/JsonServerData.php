<?php

namespace AppBundle\ServersHandle\Servers;

use AdminBundle\Entity\Task;

use AppBundle\ServersHandle\Servers\ServerDataInterface;
use AppBundle\ServersHandle\Servers\ServerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class JsonServerData implements ServerDataInterface, ServerInterface
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
     * @param EntityManagerInterface $em ,
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isAllowed(SfRequest $request)
    {
        $contentType = $request->headers->get('CONTENT_TYPE');

        return in_array($contentType, ['application/json']);
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported(SfRequest $request)
    {
        $contentType = $request->headers->get('CONTENT_TYPE');

        return in_array($contentType, ['application/json']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handle(SfRequest $request)
    {
        $response = new JsonResponse();
        $data = [];
        try {
            $data = $this->getData($request);
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
     * {@inheritDoc}
     */
    public function getData(SfRequest $request)
    {
        if (!$this->isAllowed($request)) {
            throw new \Exception(sprintf("The content type is %s not allowed", $request->headers->get('CONTENT_TYPE')));
        }

        if ($request->getMethod() === SfRequest::METHOD_POST) {
            // Process method
            return $this->processPostMethod($request);
        } elseif ($request->getMethod() === SfRequest::METHOD_GET) {
            return $this->processGetMethod($request->query->all());
        }

        throw new MethodNotAllowedHttpException(['GET', 'POST'], sprintf(
            'The method "%s" not allowed.',
            $request->getMethod()
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * Process API method
     *
     * @param SfRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    private function processPostMethod(SfRequest $request)
    {
        // Try parse JSON
        $content = $request->getContent();

        if (!$content) {
            throw new \Exception('Missing HTTP content.');
        }

        $postContent = @json_decode($content, true);

        if (false === $postContent) {
            throw new \Exception(sprintf("Error while parse JSON with error number %s", json_last_error()));
        }

        return $postContent;
    }

    /**
     * Process api query
     *
     * @param array $query
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    private function processGetMethod(array $query)
    {
        $query += array(
            'params' => array(),
        );

        if (!is_array($query['params'])) {
            throw new \Exception('Input parameters must be a array.');
        }

        return $query;
    }

    /**
     * Prepare response
     *
     * @param Response $response
     * @param Request $request
     */
    protected function prepareResponse(Response $response, Request $request)
    {
        $contentType = $this->getContentType();

        if ($contentType && !$response->headers->get('Content-Type')) {
            $response->headers->set('Content-Type', $contentType, true);
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
}
