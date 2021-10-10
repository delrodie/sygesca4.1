<?php
	
	namespace App\Utilities;
	
	class GestionAdhesion
	{
		/**
		 * Fonction pour arrondir au supérieur
		 *
		 * @param $nombre
		 * @param $arrondi
		 * @return float|int
		 */
		public function arrondiSuperieur($nombre, $arrondi)
		{
			return ceil($nombre / $arrondi) * $arrondi;
		}
		
		/**
		 * fonction verification des valeurs
		 *
		 * @param $donnee
		 * @return string
		 */
		public function validForm($donnee)
		{
			$result = htmlspecialchars(stripslashes(trim($donnee)));
			
			return $result;
		}
	}
