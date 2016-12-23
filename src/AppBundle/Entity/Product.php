<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 */
class Product
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     * @Assert\Range(min = 1, max = 2)
     */
    private $categoryId;

    /**
     * @var int
     * @Assert\GreaterThanOrEqual(0)
     */
    private $quantityStock;

    /**
     * @var int
     * @Assert\GreaterThanOrEqual(0)
     */
    private $quantityMin;

    /**
     * @var int
     * @Assert\GreaterThanOrEqual(0)
     */
    private $price;

    /**
     * @var \DateTime
     * @Assert\Date
     */
    private $expirationDate;

    /**
     * @var string
     * @Assert\Image
     */
    private $photo;


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
     * Set reference
     *
     * @param string $reference
     *
     * @return Product
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @param string $description
     *
     * @return Product
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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return Product
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set quantityStock
     *
     * @param integer $quantityStock
     *
     * @return Product
     */
    public function setQuantityStock($quantityStock)
    {
        $this->quantityStock = $quantityStock;

        return $this;
    }

    /**
     * Get quantityStock
     *
     * @return int
     */
    public function getQuantityStock()
    {
        return $this->quantityStock;
    }

    /**
     * Set quantityMin
     *
     * @param integer $quantityMin
     *
     * @return Product
     */
    public function setQuantityMin($quantityMin)
    {
        $this->quantityMin = $quantityMin;

        return $this;
    }

    /**
     * Get quantityMin
     *
     * @return int
     */
    public function getQuantityMin()
    {
        return $this->quantityMin;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return Product
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Product
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    /**
     * @var boolean
     */
    private $isVisible;


    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return Product
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible
     *
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }
    /**
     * @var integer
     */
    private $iDcategory;


    /**
     * Set iDcategory
     *
     * @param integer $iDcategory
     *
     * @return Product
     */
    public function setIDcategory($iDcategory)
    {
        $this->iDcategory = $iDcategory;

        return $this;
    }

    /**
     * Get iDcategory
     *
     * @return integer
     */
    public function getIDcategory()
    {
        return $this->iDcategory;
    }
}
