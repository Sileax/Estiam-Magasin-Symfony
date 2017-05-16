<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Products
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductsRepository")
 */
class Products
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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @var int
     *
     * @ORM\Column(name="idCategory", type="integer", nullable=true)
     */
    private $idCategory;

    /**
     * @var float
     *
     * @ORM\Column(name="buyingPrice", type="float")
     */
    private $buyingPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="sellingPrice", type="float")
     */
    private $sellingPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float")
     */
    private $vat;


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
     * Set slug
     *
     * @param string $slug
     *
     * @return Products
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Products
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set idCategory
     *
     * @param integer $idCategory
     *
     * @return Products
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * Get idCategory
     *
     * @return int
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * Set buyingPrice
     *
     * @param float $buyingPrice
     *
     * @return Products
     */
    public function setBuyingPrice($buyingPrice)
    {
        $this->buyingPrice = $buyingPrice;

        return $this;
    }

    /**
     * Get buyingPrice
     *
     * @return float
     */
    public function getBuyingPrice()
    {
        return $this->buyingPrice;
    }

    /**
     * Set sellingPrice
     *
     * @param float $sellingPrice
     *
     * @return Products
     */
    public function setSellingPrice($sellingPrice)
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
    }

    /**
     * Get sellingPrice
     *
     * @return float
     */
    public function getSellingPrice()
    {
        return $this->sellingPrice;
    }

    /**
     * Set vat
     *
     * @param float $vat
     *
     * @return Products
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }
}

