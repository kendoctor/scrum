<?php

namespace Neblion\ScrumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Neblion\ScrumBundle\Entity\Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Neblion\ScrumBundle\Entity\TaskRepository")
 */
class Task
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Neblion\ScrumBundle\Entity\Story", inversedBy="tasks")
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    private $story;
    
    /**
     * @ORM\ManyToOne(targetEntity="Neblion\ScrumBundle\Entity\Member", inversedBy="tasks")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;
    
    /**
     * @ORM\ManyToOne(targetEntity="Neblion\ScrumBundle\Entity\ProcessStatus", inversedBy="tasks")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;
    
    /**
     * @ORM\OneToMany(targetEntity="Neblion\ScrumBundle\Entity\Hour", mappedBy="task", cascade={"remove"})
     */
    private $hours;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\MaxLength(150)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var smallint $hours
     *
     * @ORM\Column(name="hour", type="smallint")
     * @Assert\NotBlank()
     * @Assert\Min(limit = "1")
     */
    private $hour;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;


    
    public function __construct()
    {
        $this->hours = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set hour
     *
     * @param smallint $hour
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    /**
     * Get hour
     *
     * @return smallint 
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set story
     *
     * @param Neblion\ScrumBundle\Entity\Story $story
     */
    public function setStory(\Neblion\ScrumBundle\Entity\Story $story)
    {
        $this->story = $story;
    }

    /**
     * Get story
     *
     * @return Neblion\ScrumBundle\Entity\Story 
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set member
     *
     * @param Neblion\ScrumBundle\Entity\Member $member
     */
    public function setMember(\Neblion\ScrumBundle\Entity\Member $member)
    {
        $this->member = $member;
    }

    /**
     * Get member
     *
     * @return Neblion\ScrumBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set status
     *
     * @param Neblion\ScrumBundle\Entity\ProcessStatus $status
     */
    public function setStatus(\Neblion\ScrumBundle\Entity\ProcessStatus $status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return Neblion\ScrumBundle\Entity\ProcessStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add hours
     *
     * @param Neblion\ScrumBundle\Entity\Hour $hours
     */
    public function addHour(\Neblion\ScrumBundle\Entity\Hour $hours)
    {
        $this->hours[] = $hours;
    }

    /**
     * Get hours
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Remove hours
     *
     * @param Neblion\ScrumBundle\Entity\Hour $hours
     */
    public function removeHour(\Neblion\ScrumBundle\Entity\Hour $hours)
    {
        $this->hours->removeElement($hours);
    }
}