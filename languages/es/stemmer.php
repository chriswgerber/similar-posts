<?php
/*
	Creado por Cesar Rodas para el proyecto Saddor.com
	Este Stemmer esta basado en el argoritmo de Snowball Stemmer.
	saddor@gmail.com
	Este programa esta bajo licencia GNU
*/
if ( ! defined( "SPANISHSTEMMER" ) ) {
	define( "vocal", 1, false );
	define( "consonante", 2, false );
	define( "SPANISHSTEMMER", 1, false );

	class PorterStemmer {
		var $R1;
		var $R2;
		var $RV;
		var $word;

		function Stem( $word ) {

			$this->word = $word;
			if ( strlen( $word ) < 2 ) {
				return;
			}


			$this->step_0();
			while ( $this->step_1() ) {
				;
			}
			$this->step_2();
			$this->step_3();

			return $this->word;
		}

		function step_0() {
			$this->splitword();
			$search = array(
				"me", "se", "sela", "selo", "selas", "selos", "la", "le", "lo", "les",
				"los", "nos"
			);

			$prefix = array(
				"i�ndo", "�ndo", "�r", "�r", "�r", /* primer caso */
				"iendo", "ando", "ar", "er", "ir", /* segundo caso*/
				"yendo"
			);

			foreach ( $prefix as $id => $pref ) {
				$return = false;
				if ( ( strstr( $this->RV, $pref ) != null ) or
					/* caso para yendo */
					( $pref == "yendo" && strstr( $this->word, "uyendo" ) )
				) {

					/*
						El prefijo fue encontrado, ahora buscar para borrar 
						el pronombre.
					*/
					foreach ( $search as $word ) {
						$len = strlen( $word );

						switch ( $id ) {

							case $id < 5: /* primer Caso*/
								if ( $word == substr( $this->RV, - 1 * $len, $len ) ) {
									$this->word = substr( $this->word, 0, strlen( $this->word ) - $len );
									$this->word = str_replace( $prefix[$id], $prefix[$id + 5], $this->word );
									$return     = true;
								}
								break;
							case $id < 10: /* segundo caso*/
								if ( $word == substr( $this->RV, - 1 * $len, $len ) ) {
									$this->word = substr( $this->word, 0, strlen( $this->word ) - $len );
									$return     = true;
								}
								break;
							case $id >= 10: /* tercer caso*/
								if ( $word == substr( $this->RV, - 1 * $len, $len ) ) {

									$this->word = substr( $this->word, 0, strlen( $this->word ) - $len );
									$return     = true;
								}
								break;
						}
					}
				}

			}
			unset( $prefix, $search, $word, $id, $pref, $len );

			return $return;
		}

		function step_1() {
			$return = false;
			$this->splitword();

			/* borrado de R2 */
			$search = array(
				"abilidades", "iblemente", "icaciones", "ablemente", "antemente", "ivamente", "atamente",
				"amientos", "icadoras", "icadores", "icancias", "imientos", "icamente",
				"osamente", "abilidad", "icidades", "ividades", "adamente", "icantes",
				"icancia", "imiemto", "icadora", "icaci�n", "amiento", "imiento", "aciones",
				"ativos", "ativas", "ividad", "idades", "icidad", "icante",
				"icador", "adoras", "adores", "ancias", "mente", "ables",
				"ismos", "anzas", "ativa", "ativo", "istas", "ibles",
				"aci�n", "antes", "adora", "ancia", "ismo", "anza",
				"icos", "ivas", "osos", "ivos", "ante", "osas",
				"ador", "ible", "ista", "idad", "able", "ico",
				"osa", "oso", "iva", "ica", "ica", "ivo",
			);

			for ( $i = 0; $i < count( $search ); $i ++ ) {
				if ( substr( $this->R2, strlen( $search[$i] ) * ( - 1 ), strlen( $search[$i] ) ) == $search[$i] ) {
					$this->word = substr( $this->word, 0, strlen( $this->word ) - strlen( $search[$i] ) );
					$return     = true;
					break;
				}
			}
			/* creo que esta mal, creo que hay que buscar en R1*/
			if ( $this->R1 == "amente" ) {
				$this->word = str_replace( "amente", "", $this->word );
			}

			$search  = array
			(
				"log�a", "log�as", /**/
				"uci�n", "uciones", /**/
				"encia", "encias"
			);
			$replace = array
			(
				"log", "log", "u", "u", "entre", "entre"
			);
			for ( $i = 0; $i < count( $search ); $i ++ ) {
				if ( substr( $this->R2, strlen( $search[$i] ) * ( - 1 ), strlen( $search[$i] ) ) == $search[$i] ) {
					$this->word = str_replace( $search[$i], $replace[$i], $this->word );
					$return     = true;
					break;
				}
			}
			unset( $i, $search, $replace );

			return $return;
		}

		function step_2() {
			$this->splitword();
			$return = false;
			$search = array(
				"ya", "ye", "yan", "yen", "yeron", "yendo", "yo", "y�", "yas", "yes", "yais", "yamos"
			);
			foreach ( $search as $word ) {
				if ( substr( $this->RV, strlen( $word ) * ( - 1 ), strlen( $word ) ) == $word ) {
					if ( substr( $this->word, - 1 * ( strlen( $word ) + 1 ), strlen( $word ) + 1 ) == "u" . $word ) {
						$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) + 1 ) );
						$return     = true;
					}
				}
			}

			if ( $return == false ) {
				$this->step_2b();
			}
			unset( $return, $search, $word );
		}

		function step_2b() {
			$this->splitword();
			$search = array(
				"en", "es", "�is", "emos"
			);

			foreach ( $search as $word ) {
				if ( substr( $this->RV, strlen( $word ) * ( - 1 ), strlen( $word ) ) == $word ) {
					if ( substr( $this->word, ( - 1 ) * ( strlen( $word ) + 2 ), strlen( $word ) + 2 ) == "gu" . $word ) {
						$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) + 1 ) );
						$return     = true;
					} /*
						This part was fix by Diego Enrique Finol <dfinol at cantv dot net>
						This was the email that Diego sent to me:
							Epa saludos, gracias por la clase de spanish stemmer, hab�a visto lo mismo
							en snowball pero me ahorraste el trabajo de convertirlo a php. S�lo not�
							que en las partes en la que hab�a que borrar cierto sufijo y, adem�s,
							borrar la "u" de si est� precedido por "gu" creo que no borra el sufijo si
							no est� precedido por esto. O sea, hay que borrar el afijo en ambos casos,
							y de paso si est� precedido por gu, tambi�n borrar la u, pero el algoritmo
							s�lo lo hace si est� precedido por gu, sino, no borra nada.

						Thanks Diego!.
					*/
					else {
						$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) ) );
						$return     = true;
					}
				}
				/*End of Diego fix*/
			}

			$search = array(
				"i�ramos", "ar�amos", "ir�amos", "i�semos", "er�amos", "er�ais", "eremos",
				"isteis", "ir�ais", "ierais", "iremos", "�bamos", "ieseis",
				"asteis", "�ramos", "�semos", "aremos", "ar�ais", "abais",
				"�amos", "arais", "ieses", "ar�an", "iesen", "ieron",
				"iendo", "ieras", "ir�is", "ar�as", "er�as", "aseis",
				"er�is", "er�an", "ir�an", "ar�is", "ir�as", "ieran",
				"ando", "amos", "aron", "asen", "aras", "ados",
				"�ais", "ases", "imos", "adas", "idas", "abas",
				"iste", "ir�n", "er�n", "ar�a", "er�a", "iera",
				"ir�s", "ir�a", "aran", "ar�s", "er�s", "aste",
				"iese", "aban", "ar�n", "�is", "ada", "ir�",
				"�an", "ir�", "er�", "aba", "ara", "ido",
				"ar�", "ar�", "ado", "er�", "ase", "�as",
				"ida", "�a", "er", "ar", "i�", "an",
				"ir", "as", "ad", "ed", "id", "�s",


			);

			foreach ( $search as $word ) {
				if ( substr( $this->RV, strlen( $word ) * ( - 1 ), strlen( $word ) ) == $word ) {
					$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) ) );
					$this->splitword();
				}
			}
			unset( $search, $word );

		}

		function step_3() {
			$this->splitword();
			$return = false;
			$search = array(
				"os", "a", "o", "�", "�", "�"
			);


			foreach ( $search as $word ) {
				if ( substr( $this->RV, strlen( $word ) * ( - 1 ), strlen( $word ) ) == $word ) {
					$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) ) );
					$return     = true;
				}
			}

			$search = array(
				"e", "�"
			);

			foreach ( $search as $word ) {
				if ( substr( $this->RV, strlen( $word ) * ( - 1 ), strlen( $word ) ) == $word ) {
					if ( substr( $this->RV, - 1 * ( strlen( $word ) + 2 ), strlen( $word ) + 2 ) == "gu" . $word ) {
						$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) + 1 ) );
						$return     = true;
					} else {
						$this->word = substr( $this->word, 0, strlen( $this->word ) - ( strlen( $word ) ) );
						$return     = true;
					}
				}
			}
			unset( $search, $word );
			$this->word = str_replace( "�", "a", $this->word );
			$this->word = str_replace( "�", "e", $this->word );
			$this->word = str_replace( "�", "i", $this->word );
			$this->word = str_replace( "�", "o", $this->word );
			$this->word = str_replace( "�", "u", $this->word );
			$this->word = str_replace( "�", "u", $this->word );

			return $return;
		}


		/* funciones utilizadas*/
		function saddorsort( $a, $b ) {
			if ( strlen( $a ) == strlen( $b ) ) {
				return 0;
			}

			return ( strlen( $a ) < strlen( $b ) ) ? 1 : - 1;
		}

		function splitword() {
			$flag1    = false;
			$flag2    = false;
			$this->R1 = "";
			$this->R2 = "";
			$this->RV = "";
			for ( $i = 1; $i < strlen( $this->word ); $i ++ ) {
				if ( $flag1 ) {
					$this->R1 .= $this->word[$i];
				}
				if ( $flag2 ) {
					$this->R2 .= $this->word[$i];
				}

				if ( $i + 1 >= strlen( $this->word ) ) {
					break;
				}

				if ( $this->char_is( $this->word[$i] ) == consonante &&
					$this->char_is( @$this->word[$i + 1] ) == vocal &&
					$flag1 == true && $flag2 == false
				) {
					$flag2 = true;
				}

				if ( $this->char_is( $this->word[$i] ) == consonante &&
					$this->char_is( $this->word[$i + 1] ) == vocal &&
					$flag1 == false
				) {
					$flag1 = true;
				}
			}


			/* Buscando RV*/
			$flag1 = false;
			if ( $this->char_is( $this->word[1] ) == consonante ) {
				for ( $i = 2; $i < strlen( $this->word ); $i ++ ) {
					if ( $this->char_is( $this->word[$i] ) == vocal ) {
						break;
					}
				}
				$i ++;
				$this->RV = substr( $this->word, $i );
			} else if ( $this->char_is( $this->word[1] ) == vocal && $this->char_is( $this->word[0] ) == vocal ) {
				for ( $i = 2; $i < strlen( $this->word ); $i ++ ) {
					if ( $this->char_is( $this->word[$i] ) == consonante ) {
						break;
					}
				}
				$i ++;
				$this->RV = substr( $this->word, $i );
			} else if ( strlen( $this->word ) > 2 ) {
				$this->RV = substr( $this->word, 3 );
			}

			unset( $flag1, $flag2, $i );
		}

		function char_is( $char ) {
			$char = strtolower( $char );
			if ( $char == "" ) {
				return;
			}
			$vowel     = "aeiou������";
			$consonant = "bcdfghijklmn�opqrsvtxwyz";
			if ( strstr( $vowel, $char ) ) {
				return vocal;
			}
			if ( strstr( $consonant, $char ) ) {
				return consonante;
			}
		}
	}
}

/*
	Stem caching added by Rob Marsh, SJ
	http://rmarsh.com
*/

$Stemmer   = new PorterStemmer();
$StemCache = array();

function stem( $word ) {
	global $Stemmer, $StemCache;
	if ( ! isset( $StemCache[$word] ) ) {
		$stemmedword      = $Stemmer->Stem( $word );
		$StemCache[$word] = $stemmedword;
	} else {
		$stemmedword = $StemCache[$word];
	}

	return $stemmedword;
}

?>