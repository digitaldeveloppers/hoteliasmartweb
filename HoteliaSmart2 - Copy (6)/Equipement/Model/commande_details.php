<?php

class commande_details
{
    private $id;
    private $idCommande;
    private $reference;
    private $quantity;
    private $price;

    public function __construct($id, $idCommande, $reference, $quantity, $price)
    {
        $this->id = $id;
        $this->idCommande = $idCommande;
        $this->reference = $reference;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdCommande()
    {
        return $this->idCommande;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdCommande($idCommande)
    {
        $this->idCommande = $idCommande;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
}