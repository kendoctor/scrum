<?php

namespace Neblion\ScrumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Neblion\ScrumBundle\Entity\Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Neblion\ScrumBundle\Entity\ProjectRepository")
 */
class Project
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
     * @ORM\OneToOne(targetEntity="Neblion\ScrumBundle\Entity\Team", mappedBy="project", cascade={"remove"})
     */
    private $team;
    
    /**
     * @ORM\OneToMany(targetEntity="Neblion\ScrumBundle\Entity\ProjectRelease", mappedBy="project", cascade={"remove"})
     */
    private $releases;
    
    /**
     * @ORM\OneToMany(targetEntity="Neblion\ScrumBundle\Entity\Feature", mappedBy="project", cascade={"remove"})
     */
    private $features;
    
    /**
     * @ORM\OneToMany(targetEntity="Neblion\ScrumBundle\Entity\Story", mappedBy="project", cascade={"remove"})
     */
    private $stories;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\MaxLength(50)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * @var smallint $sprint_start_day
     *
     * @ORM\Column(name="sprint_start_day", type="smallint")
     */
    private $sprint_start_day;
    
    /**
     * @var smallint $sprint_duration
     *
     * @ORM\Column(name="sprint_duration", type="smallint")
     */
    private $sprint_duration;
    
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
    public function __construct()
    {
        $this->releases = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add releases
     *
     * @param Neblion\ScrumBundle\Entity\ProjectRelease $releases
     */
    public function addProjectRelease(\Neblion\ScrumBundle\Entity\ProjectRelease $releases)
    {
        $this->releases[] = $releases;
    }

    /**
     * Get releases
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReleases()
    {
        return $this->releases;
    }

    /**
     * Add features
     *
     * @param Neblion\ScrumBundle\Entity\Feature $features
     */
    public function addFeature(\Neblion\ScrumBundle\Entity\Feature $features)
    {
        $this->features[] = $features;
    }

    /**
     * Get features
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Add stories
     *
     * @param Neblion\ScrumBundle\Entity\Story $stories
     */
    public function addStory(\Neblion\ScrumBundle\Entity\Story $stories)
    {
        $this->stories[] = $stories;
    }

    /**
     * Get stories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getStories()
    {
        return $this->stories;
    }

    /**
     * Set team
     *
     * @param Neblion\ScrumBundle\Entity\Team $team
     */
    public function setTeam(\Neblion\ScrumBundle\Entity\Team $team)
    {
        $this->team = $team;
    }

    /**
     * Get team
     *
     * @return Neblion\ScrumBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Add releases
     *
     * @param Neblion\ScrumBundle\Entity\ProjectRelease $releases
     * @return Project
     */
    public function addRelease(\Neblion\ScrumBundle\Entity\ProjectRelease $releases)
    {
        $this->releases[] = $releases;
    
        return $this;
    }

    /**
     * Remove releases
     *
     * @param Neblion\ScrumBundle\Entity\ProjectRelease $releases
     */
    public function removeRelease(\Neblion\ScrumBundle\Entity\ProjectRelease $releases)
    {
        $this->releases->removeElement($releases);
    }

    /**
     * Remove features
     *
     * @param Neblion\ScrumBundle\Entity\Feature $features
     */
    public function removeFeature(\Neblion\ScrumBundle\Entity\Feature $features)
    {
        $this->features->removeElement($features);
    }

    /**
     * Add stories
     *
     * @param Neblion\ScrumBundle\Entity\Story $stories
     * @return Project
     */
    public function addStorie(\Neblion\ScrumBundle\Entity\Story $stories)
    {
        $this->stories[] = $stories;
    
        return $this;
    }

    /**
     * Remove stories
     *
     * @param Neblion\ScrumBundle\Entity\Story $stories
     */
    public function removeStorie(\Neblion\ScrumBundle\Entity\Story $stories)
    {
        $this->stories->removeElement($stories);
    }

    

    /**
     * Set sprint_start_day
     *
     * @param integer $sprintStartDay
     * @return Project
     */
    public function setSprintStartDay($sprintStartDay)
    {
        $this->sprint_start_day = $sprintStartDay;
    
        return $this;
    }

    /**
     * Get sprint_start_day
     *
     * @return integer 
     */
    public function getSprintStartDay()
    {
        return $this->sprint_start_day;
    }

    /**
     * Set sprintDuration
     *
     * @param integer $sprintDuration
     * @return Project
     */
    public function setSprintDuration($sprintDuration)
    {
        $this->sprint_duration = $sprintDuration;
    
        return $this;
    }

    /**
     * Get sprintDuration
     *
     * @return integer 
     */
    public function getSprintDuration()
    {
        return $this->sprint_duration;
    }
}