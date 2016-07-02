<?php

namespace AdminBundle\Api;

use AppBundle\ServerData\ServerDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Annotation\Action;

class TasksApi
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
     * @var ServerDataInterface
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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Construct
     *
     * @param \Twig_Environment $twig
     * @param ServerDataInterface $serverData
     * @param EntityManagerInterface $em ,
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param RequestStack $requestStack
     */
    public function __construct(
        \Twig_Environment $twig,
        ServerDataInterface $serverData,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->dataHandler = $serverData;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->requestStack = $requestStack;
    }

    /**
     * @Action ("tasks.list")
     */
    public function tasksList()
    {

    }
}