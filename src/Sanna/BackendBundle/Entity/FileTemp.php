<?php

namespace Sanna\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileTemp
 *
 * @ORM\Table(name="filetemp")
 * @ORM\Entity
 */
class FileTemp
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
     * @ORM\Column(name="dir_email", type="string", length=255)
     */
    private $dirEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="name_file", type="string", length=255)
     */
    private $nameFile;


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
     * Set dirEmail
     *
     * @param string $dirEmail
     * @return FileTemp
     */
    public function setDirEmail($dirEmail)
    {
        $this->dirEmail = $dirEmail;

        return $this;
    }

    /**
     * Get dirEmail
     *
     * @return string 
     */
    public function getDirEmail()
    {
        return $this->dirEmail;
    }

    /**
     * Set nameFile
     *
     * @param string $nameFile
     * @return FileTemp
     */
    public function setNameFile($nameFile)
    {
        $this->nameFile = $nameFile;

        return $this;
    }

    /**
     * Get nameFile
     *
     * @return string 
     */
    public function getNameFile()
    {
        return $this->nameFile;
    }
}
