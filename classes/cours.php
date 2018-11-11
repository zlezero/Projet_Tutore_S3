<?php
class Cours
{
  private $_horaire_debut;
  private $_horaire_fin;
  private $_groupe;
  private $_nom;
  private $_professeur;
  private $_salle;
  private $_jour;
  private $_rawweeks;
        
  public function __construct($horaire_debut, $horaire_fin, $groupe, $nom, $professeur, $salle, $jour, $rawweeks) {
	
    $this->_horaire_debut = $horaire_debut;
    $this->_horaire_fin = $horaire_fin;
    $this->_groupe = $groupe;
    $this->_nom = $nom;
    $this->_professeur = $professeur;
    $this->_salle = $salle;
    $this->_jour = $jour;
    $this->_rawweeks = $rawweeks;
  }
  
  public function getHoraireDebut() {
	  return $this->_horaire_debut;
  }

  public function getHoraireFin() {
    return $this->_horaire_fin;
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

  public function getJour() {
    return $this->_jour;
  }

  public function getRawWeeks() {
    return $this->_rawweeks;
  }
  
   public function __toString() {
	return "L'horaire est : ".$this->_horaire_debut." - ".$this->_horaire_fin."</br>Le groupe est : ".$this->_groupe."</br>Le nom est : ".$this->_nom."</br>Le professeur est : ".$this->_professeur."</br>La salle est : ".$this->_salle;
  }
}