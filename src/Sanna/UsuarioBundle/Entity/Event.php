<?php

namespace Sanna\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title_event", type="string", length=255, nullable=false)
     */
    private $titleEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=false)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var Date
     *
     * @ORM\Column(name="start", type="date", nullable=false)
     */
    private $start;

    /**
     * @var Date
     *
     * @ORM\Column(name="end", type="date")
     */
    private $end;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")
     * })
     */
    private $user;


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
     * Set titleEvent
     *
     * @param string $titleEvent
     * @return Skill
     */
    public function setTitleEvent($titleEvent)
    {
        $this->titleEvent = $titleEvent;

        return $this;
    }

    /**
     * Get titleEvent
     *
     * @return string 
     */
    public function getTitleEvent()
    {
        return $this->titleEvent;
    }

    /**
     * Set Site
     *
     * @param string $site
     * @return Skill
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return Skill
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set start
     *
     * @param string $start
     * @return Skill
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return string 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param string $end
     * @return Skill
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return string 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set user
     *
     * @param \Sanna\UsuarioBundle\Entity\User $user
     * @return Event
     */
    public function setUser(\Sanna\UsuarioBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Sanna\UsuarioBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
