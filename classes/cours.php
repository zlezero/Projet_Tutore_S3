<?php
class Cours
{
  private $_dateDebut;
  private $_dateFin;
  private $_groupe;
  private $_nom;
  private $_professeur;
  private $_salle;
  private $_couleur;
  private $_remarque;
        
  public function __construct($dateDebut, $dateFin, $groupe, $nom, $professeur, $salle, $couleur, $remarque) {
    $this->_dateDebut = $dateDebut;
    $this->_dateFin = $dateFin;
    $this->_groupe = $groupe;
    $this->_nom = $nom;
    $this->_professeur = $professeur;
    $this->_salle = $salle;
    $this->_couleur = $couleur;
    $this->_remarque = $remarque;
  }
  
  public function getDateDebut() {
	  return $this->_dateDebut;
  }

  public function getDateFin() {
    return $this->_dateFin;
  }

  public function getGroupe() {
    return $this->_groupe;
  }

  public function getNom() {
    return $this->_nom;
  }

  public function getProfesseur() {
    return $this->_professeur;
  }

  public function getSalle() {
    return $this->_salle;
  }
  
  public function getCouleur() {
	  return $this->_couleur;
  }
  
  public function getRemarque() {
    return $this->_remarque;
  }

  public function setDateDebut($dateDebut) {
	  $this->_dateDebut = $dateDebut;
  }

  public function setDateFin($dateFin) {
    $this->_dateFin = $dateFin;
  }

  public function setGroupe($groupe) {
    $this->_groupe = $groupe;
  }

  public function setNom($nom) {
    $this->_nom = $nom;
  }

  public function setProfesseur($professeur) {
    $this->_professeur = $professeur;
  }

  public function setSalle($salle) {
    $this->_salle = $salle;
  }
  
  public function setCouleur($couleur) {
	  $this->_couleur = $couleur;
  }

  public function setRemarque($remarque) {
    $this->_remarque = $remarque;
  }
  
  public function __toString() {
	  return "L'horaire est : ".$this->_dateDebut." - ".$this->_dateFin."</br>Le groupe est : ".$this->_groupe."</br>Le nom est : ".$this->_nom."</br>Le professeur est : ".$this->_professeur."</br>La salle est : ".$this->_salle;
  }
}