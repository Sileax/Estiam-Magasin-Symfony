<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockRepository")
 */
class Stock
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
     * @var int
     *
     * @ORM\Column(name="refProduct", type="integer", unique=true)
     */
    private $refProduct;

    /**
     * @var int
     *
     * @ORM\Column(name="availableProducts", type="integer")
     */
    private $availableProducts;


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
     * Set refProduct
     *
     * @param integer $refProduct
     *
     * @return Stock
     */
    public function setRefProduct($refProduct)
    {
        $this->refProduct = $refProduct;

        return $this;
    }

    /**
     * Get refProduct
     *
     * @return int
     */
    public function getRefProduct()
    {
        return $this->refProduct;
    }

    /**
     * Set availableProducts
     *
     * @param integer $availableProducts
     *
     * @return Stock
     */
    public function setAvailableProducts($availableProducts)
    {
        $this->availableProducts = $availableProducts;

        return $this;
    }

    /**
     * Get availableProducts
     *
     * @return int
     */
    public function getAvailableProducts()
    {
        return $this->availableProducts;
    }
}

