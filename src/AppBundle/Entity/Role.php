<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="roleType", type="string", length=255, unique=true)
     */
    private $roleType;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set roleType
     *
     * @param string $roleType
     *
     * @return Role
     */
    public function setRoleType($roleType)
    {
        $this->roleType = $roleType;

        return $this;
    }

    /**
     * Get roleType
     *
     * @return string
     */
    public function getRoleType()
    {
        return $this->roleType;
    }
}

