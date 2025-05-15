<?php

class equipement
{
    private $reference;
    private $nom;
    private $prix;
    private $quantite;
    private $type;
    private $image;
    private $guide;

    public function __construct($reference, $nom, $prix, $quantite, $type, $image = null, $guide = null)
    {
        $this->reference = $reference;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->quantite = $quantite;
        $this->type = $type;
        $this->image = $image;
        $this->guide = $guide;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getGuide()
    {
        return $this->guide;
    }

    public function setGuide($guide)
    {
        $this->guide = $guide;
    }

}
?>