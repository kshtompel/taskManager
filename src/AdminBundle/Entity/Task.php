<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Entity\Repository\TaskRepository")
 * @ORM\Table(
 *      name="tasks"
 * )
 */
class Task
{
    const STATUS_NEW = 1;
    const STATUS_PENDING = 2;
    const STATUS_FINISHED = 3;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512, nullable=true)
     */
    private $description;

    /**
     * @var integer
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Assert\DateTime()
     * @Serializer\SerializedName(value="createdAt")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime")
     * 
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Serializer\SerializedName(value="startedAt")
     */
    private $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Assert\Expression("this.getStartedAt() < this.getFinishedAt()", message="Start date should be less than finish date")
     * @Serializer\SerializedName(value="finishedAt")
     */
    private $finishedAt;

    /**
     * Task constructor.
     * @param $name
     * @param \DateTime $started
     * @param \DateTime $finished
     */
    public function __construct($name, \DateTime $started, \DateTime $finished)
    {
        $this->name = $name;
        $this->createdAt = new \DateTime();
        $this->setStartedAt($started);
        $this->setFinishedAt($finished);
        $this->status = self::STATUS_NEW;
    }

    /**
     * Get Task id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Task name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Task name.
     *
     * @param string $name
     * @return Task
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Task description.
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get Task started time.
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set Taskk started time.
     *
     * @param \DateTime $startedAt
     * @return Task
     */
    public function setStartedAt(\DateTime $startedAt)
    {
        $startedAt->setTime(0, 0, 0);
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get Task finished time.
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set Task finished time.
     *
     * @param \DateTime $finishedAt
     * @return Task
     */
    public function setFinishedAt(\DateTime $finishedAt)
    {
        $finishedAt->setTime(23, 59, 59);
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Set task as new.
     *
     * @return Task
     */
    public function isNew()
    {
        $this->status = self::STATUS_NEW;

        return $this;
    }

    /**
     * Set task as pending.
     *
     * @return Task
     */
    public function isPending()
    {
        $this->status = self::STATUS_PENDING;

        return $this;
    }

    /**
     * Set task as finished.
     *
     * @return Task
     */
    public function isFinished()
    {
        $this->status = self::STATUS_FINISHED;

        return $this;
    }

    /**
     * Check expired task.
     *
     * @return bool
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("expired")
     *
     */
    public function isExpired()
    {
        $date = new \DateTime();

        return $this->status == self::STATUS_NEW && $date > $this->getFinishedAt();
    }
}