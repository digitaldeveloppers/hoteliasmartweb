<?php

class commande
{
    private $idCommande;
    private $date;
    private $nomClient;
    private $prenomClient;
    private $adresseClient;
    private $mailClient;
    private $modePaiment;
    private $total;

    public function __construct($idCommande, $date, $nomClient, $prenomClient, $adresseClient, $mailClient, $modePaiment, $total)
    {
        $this->idCommande = $idCommande;
        $this->date = $date;
        $this->nomClient = $nomClient;
        $this->prenomClient = $prenomClient;
        $this->adresseClient = $adresseClient;
        $this->mailClient = $mailClient;
        $this->modePaiment = $modePaiment;
        $this->total = $total;
    }

    public function getIdCommande()
    {
        return $this->idCommande;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getNomClient()
    {
        return $this->nomClient;
    }

    public function getPrenomClient()
    {
        return $this->prenomClient;
    }

    public function getAdresseClient()
    {
        return $this->adresseClient;
    }

    public function getMailClient()
    {
        return $this->mailClient;
    }

    public function getmodePaiment()
    {
        return $this->modePaiment;
    }

    public function getTotal()
    {
        return $this->total;  
    }

    public function setIdCommande($idCommande)
    {
        $this->idCommande = $idCommande;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setNomClient($nomClient)
    {
        $this->nomClient = $nomClient;
    }

    public function setPrenomClient($prenomClient)
    {
        $this->prenomClient = $prenomClient;
    }

    public function setAdresseClient($adresseClient)
    {
        $this->adresseClient = $adresseClient;
    }

    public function setMailClient($mailClient)
    {
        $this->mailClient = $mailClient;
    }

    public function setmodePaiment($modePaiment)
    {
        $this->modePaiment = $modePaiment;
    }

    public function setTotal($total)
    {
        $this->total = $total;  
    }

}



?>