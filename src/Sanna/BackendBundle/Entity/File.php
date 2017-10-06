<?php

namespace Sanna\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="name_folder", type="string", length=255)
     */
    private $nameFolder;


    /**
     * @var \User
     *
     * @ORM\ManyToMany(targetEntity="Sanna\UsuarioBundle\Entity\User",inversedBy="users")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    private $users;


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
     * Set file
     *
     * @param string $file
     * @return File
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set nameFolder
     *
     * @param string $nameFolder
     * @return File
     */
    public function setNameFolder($nameFolder)
    {
        $this->nameFolder = $nameFolder;

        return $this;
    }

    /**
     * Get nameFolder
     *
     * @return string 
     */
    public function getNameFolder()
    {
        return $this->nameFolder;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \Sanna\UsuarioBundle\Entity\User $users
     * @return File
     */
    public function addUser(\Sanna\UsuarioBundle\Entity\User $users)
    {	 
		if(!$this->users->contains($users)){
            $this->users[] = $users;
        }else{
            $this->users->remove($this->id);
        }
        return $this;
       
    }

    /**
     * Remove users
     *
     * @param \Sanna\UsuarioBundle\Entity\User $users
     */
    public function removeUser(\Sanna\UsuarioBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
