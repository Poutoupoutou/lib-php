# Librairie PHP générale de Lyssal

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4e86aafd-eadd-4fc7-8433-da8f3605db49/small.png)](https://insight.sensiolabs.com/projects/4e86aafd-eadd-4fc7-8433-da8f3605db49)


## Texte (classe abstraite)

Permet de traiter du texte.

* `getTexte()` : Récupère le texte
* `replace()` : Remplace dans le texte
* `encodeHtml()` : Encode la chaîne en HTML

### Chaine (étend Texte)

Permet de traiter une chaîne de caractères.

* `supprimeAccents()` : Supprime les accents de la chaîne de caractères
* `minifie($separateur, $toutEnMinuscule)` : Simplifie la chaîne de caractères (pour une URL par exemple)
* `hasLettre()` : Retourne si la chaîne contient au moins une lettre
* `hasChiffre()` : Retourne si la chaîne contient au moins un chiffre
* `br2nl()` : Remplace les <br> en \n

### HTML (étend Texte)

* `makeClickableLinks()` : Transforme les URL en liens HREF
* `makeClickableEmails()` : Transforme les adresses électroniques en liens MAILTO


## Csv

Permet de traiter des fichiers CSV.

* `setSeparateur($separateur)` : Spécifie le séparateur de colonne
* `setEncadrementTexte($encadrementTexte)` : Spécifie le caractère d'encadrement de texte
* `setEnTetes(array $enTetes)` : Spécifie les en-têtes du CSV
* `getLignes()` : Retourne les lignes du CSV
* `addLigne(array $ligne)` : Ajoute une ligne au CSV
* `changeCharset($charsetSource = null, $charsetTarget = null)` : Change le jeu de caractères
* `importe($aHeader = false)` : Lit le fichier CSV
* `exporte()` : Télécharge le fichier CSV généré


## Fichier

Permet de traiter des fichiers ou d'obtenir certaines informations s'il s'agit d'une URL.

* `getPathname()` : Retourne le chemin du fichier / URL
* `getFilename()` : Retourne le nom du fichier / URL
* `getFilenameWithoutExtension()` : Retourne le nom du fichier / URL sans son extension
* `getExtension()` : Retourne l'extension du fichier / URL
* `getPath()` : Retourne le dossier du fichier / URL
* `getSize()` : Retourne la taille du fichier en octets
* `getContent()` : Retourne le contenu du fichier
* `setContent($contenu)` : Spécifie le contenu du fichier
* `isUrl()` : Retourne si le chemin du fichier est une URL
* `move($nouveauChemin, $remplaceSiExistant)` : Déplace le fichier
* `copy($chemin, $remplaceSiExistant)` : Copie le fichier
* `setNomMinifie($nouveauNom, $separateur, $toutEnMinuscule, $longueurMaximale, $remplaceSiExistant)` : Minifie le nom du fichier
* `getCheminLibre($fichier, $separateur)` : Retourne un chemin libre (aucun fichier existant) pour ce fichier
* `normalizeEndLines()` : Normalise les fins de ligne d'un fichier
* `getEncodage()` : Retourne l'encodage du fichier

* `Lyssal::getExtensionFromFile($filename)` : Retourne l'extension d'un fichier



## Image

Permet de traiter des images (étend Fichier).

* `redimensionne($nouvelleLargeur, $nouvelleHauteur, $conserveProportions)` : Redimensionne l'image


## Couleur

Permet de manipuler des couleurs.

* `lighten($pourcentage)` : Éclaircit une couleur
* `darken($pourcentage)` : Obscurcit une couleur


## Cryptage

Permet de crypter / décrypter du texte.

### Méthodes statiques

* `base64UrlEncode($texte)` : Encode en 64 bits pour une URL
* `base64UrlDecode($texte)` : Decode en 64 bits pour une URL


## Systeme

Classe permettant des traitements sur le système.

### Méthodes statiques

* `setMemoryLimit($memoryLimit)` : Détermine la mémoire limite (en octet par défaut) qu'un script est autoriser à allouer
* `setTimeLimitIllimite()` : Fixe le temps d'exécution d'un script à illimité
* `redirect($url)` : Redirige l'internaute
* `getFileUploadMaxSize()` : Retourne la taille maximale en octet que peut avoir un fichier chargé par l'utilisateur
* `getPhpSizeInBytes($phpSize)` : Retourne le nombre d'octets d'une taille en PHP
