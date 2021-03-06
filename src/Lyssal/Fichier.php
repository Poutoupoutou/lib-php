<?php
namespace Lyssal;

/**
 * Classe permettant des traitements sur des fichiers.
 * 
 * @author Rémi Leclerc
 */
class Fichier
{
    /**
     * @var string Chemin du fichier
     */
    private $pathname;

    /**
     * @var \SplFileInfo SplFileInfo du fichier
     */
    private $splFileInfo = null;


    /**
     * Constructeur d'un fichier.
     *
     * @param string $pathname Chemin du fichier à traiter
     */
    public function __construct($pathname)
    {
        $this->pathname = $pathname;

        if (file_exists($pathname)) {
            $this->initSplFileInfo($pathname);
        }
    }


    /**
     * Initialise le SplFileInfo.
     *
     * @return void
     */
    private function initSplFileInfo($fichierPath)
    {
        $this->splFileInfo = new \SplFileInfo($fichierPath);
    }


    /**
     * Retourne le chemin du fichier.
     * 
     * @deprecated Use getPathname
     * @return string Chemin (pathname) du fichier
     */
    public function getChemin()
    {
        return $this->getPathname();
    }
    
    /**
     * Retourne le chemin du fichier / URL.
     * 
     * @return string Chemin (pathname) du fichier
     */
    public function getPathname()
    {
        return (null !== $this->splFileInfo ? $this->splFileInfo->getRealPath() : $this->pathname);
    }
    
    /**
     * Retourne le nom (filename) du fichier.
     * 
     * @deprecated Use getFilename
     * @return string Nom du fichier
     */
    public function getNom()
    {
        return $this->getFilename();
    }

    /**
     * Retourne le nom (filename) du fichier / URL.
     * 
     * @return string Nom du fichier
     */
    public function getFilename()
    {
        return (
            null !== $this->splFileInfo
                ? $this->splFileInfo->getFilename()
                : (
                    $this->isUrl()
                        ? (substr($this->pathname, strrpos($this->pathname, '/') + 1))
                        : (substr($this->pathname, strrpos($this->pathname, DIRECTORY_SEPARATOR) + 1))
                )
        );
    }
    
    /**
     * Retourne le nom du fichier / URL sans son extension.
     * 
     * @return string Nom du fichier sans extension
     */
    public function getFilenameWithoutExtension()
    {
        if (null !== $this->getExtension()) {
            return substr($this->getFilename(), 0, strlen($this->getFilename()) - strlen($this->getExtension()) - 1);
        }

        return $this->getFilename();
    }
    
    /**
     * Retourne l'extension du fichier / URL.
     * 
     * @return string Extension
     */
    public function getExtension()
    {
        return (
            null !== $this->splFileInfo
                ? $this->splFileInfo->getExtension()
                : substr($this->pathname, strrpos($this->pathname, '.') + 1)
        );
    }
    
    /**
     * Retourne le dossier du fichier.
     * 
     * @deprecated Use getPath
     * @return string Dossier
     */
    public function getDossier()
    {
        return $this->splFileInfo->getPath();
    }
    
    /**
     * Retourne le dossier du fichier / URL.
     * 
     * @return string Dossier
     */
    public function getPath()
    {
        return (
            null !== $this->splFileInfo
                ? $this->splFileInfo->getPath()
                : (
                    $this->isUrl()
                        ? (substr($this->pathname, 0, strrpos($this->pathname, '/')))
                        : (substr($this->pathname, 0, strrpos($this->pathname, DIRECTORY_SEPARATOR)))
                )
        );
    }
    
    /**
     * Retourne la taille du fichier en octets.
     * 
     * @return integer Size
     */
    public function getSize()
    {
        return $this->splFileInfo->getSize();
    }
    
    /**
     * Retourne le contenu du fichier.
     * 
     * @return string|NULL Contenu ou NULL si non lisible
     */
    public function getContent()
    {
        $contenu = file_get_contents($this->splFileInfo->getRealPath());
        if (false === $contenu)
            $contenu = null;
        
        return $contenu;
    }

    /**
     * Spécifie le contenu du fichier.
     *
     * @param string $contenu Nouveau contenu
     * @return void
     */
    public function setContent($contenu)
    {
        file_put_contents($this->splFileInfo->getRealPath(), $contenu);
    }

    /**
     * Retourne si le chemin du fichier est une URL.
     *
     * @return boolean VRAI si URL
     */
    public function isUrl()
    {
        return (false !== filter_var($this->pathname, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED));
    }

    /**
     * Déplace le fichier.
     *
     * @param string $nouveauChemin Nouveau chemin du fichier
     * @param boolean $remplaceSiExistant Si FAUX le nom du fichier pourra être modifié pour ne pas à avoir à remplacer un fichier existant
     * @return boolean VRAI si le déplacement a réussi
     */
    public function move($nouveauChemin, $remplaceSiExistant = false)
    {
        if ('' == $nouveauChemin)
        {
            throw new \Exception('Impossible de déplacer car chemin vide.');
        }

        if (false === $remplaceSiExistant)
            $nouveauChemin = self::getCheminLibre($nouveauChemin, '-');
    
        $deplacementEstReussi = false;
        
        if (is_uploaded_file($this->getPathname()))
            $deplacementEstReussi = move_uploaded_file($this->getPathname(), $nouveauChemin);
        else $deplacementEstReussi = rename($this->getPathname(), $nouveauChemin);
        
        if ($deplacementEstReussi)
            $this->initSplFileInfo($nouveauChemin);

        return $deplacementEstReussi;
    }
    
    /**
     * Copie le fichier.
     * 
     * @param string $chemin Chemin où sera copié le fichier
     * @param boolean $remplaceSiExistant Si FAUX le nom du fichier pourra être modifié pour ne pas à avoir à remplacer un fichier existant
     * @return \Lyssal\Fichier|NULL Fichier créé ou NIL si la copie a échoué
     */
    public function copy($chemin, $remplaceSiExistant = false)
    {
        if (false === $remplaceSiExistant)
            $chemin = self::getCheminLibre($chemin, '-');
    
        if (copy($this->getPathname(), $chemin))
            return new Fichier($chemin);
        return null;
    }
    
    /**
     * Modifie le nom du fichier en le minifiant. Ne pas donner l'extension.
     *
     * @param string $nouveauNom Nom nom (non minifié) du fichier
     * @param string $separateur Le séparateur remplaçant les caractères spéciaux
     * @param boolean $toutEnMinuscule VRAI ssi le nom doit être en minuscule
     * @param integer|NULL $longueurMaximale Longueur maximale du fichier (extension comprise)
     * @param boolean $remplaceSiExistant Si FAUX le nom du fichier pourra être modifié pour ne pas à avoir à remplacer un fichier existant
     * @return \Lyssal\Fichier Le fichier
     */
    public function setNomMinifie($nouveauNom, $separateur = '-', $toutEnMinuscule = true, $longueurMaximale = null, $remplaceSiExistant = false)
    {
        $chaineFichierNom = new Chaine($nouveauNom);
        $chaineFichierNom->minifie($separateur, $toutEnMinuscule);
        $fichierNom = $chaineFichierNom->getTexte();
        
        $longueurMaximaleSoustrait = strlen($this->getExtension()) + 1;
        // Réduire la longueur si le fichier existe déjà (à cause de l'ajout d'un suffixe)
        if ($remplaceSiExistant)
            $longueurMaximaleSoustrait += strlen(self::getCheminLibre($this->getPath().DIRECTORY_SEPARATOR.$fichierNom, $separateur)) - strlen($this->getPath().DIRECTORY_SEPARATOR.$fichierNom);
        
        if (null !== $longueurMaximale)
            $fichierNom = substr($fichierNom, 0, $longueurMaximale - $longueurMaximaleSoustrait);
        $fichierNom .= '.'.$this->getExtension();
        
        $this->move($this->getPath().DIRECTORY_SEPARATOR.$fichierNom, $remplaceSiExistant);
        
        return $this;
    }

    /**
     * Normalise les fins de ligne d'un fichier.
     * 
     * @return void
     */
    public function normalizeEndLines()
    {
        $contenu = $this->getContent();
        
        if (null === $contenu)
            throw new \Exception('Fichier non lisible.');
        
        $contenu = str_replace(array("\r", "\n"), "\r\n", $contenu);
        
        $this->setContent($contenu);
    }
    
    /**
    * Retourne le chemin du fichier s'il est libre, sinon un autre chemin libre
    *
    * @param string $fichier Chemin du fichier
    * @param string $separateur Le séparateur en cas de renommage du fichier
    * @return string Le chemin du fichier libre
    */
    public static function getCheminLibre($fichier, $separateur)
    {
        if (file_exists($fichier))
        {
            $fichierExtension = substr($fichier, strrpos($fichier, '.') + 1);
        
            $fichierMatch = array();
            if (false !== preg_match('/(.*)(\\'.$separateur.'){1}([0-9]+)([\.]){1}([a-zA-Z0-9]){1,5}/', $fichier, $fichierMatch))
            {
                if (count($fichierMatch) > 4)
                    return self::getCheminLibre($fichierMatch[1].$separateur.(intval($fichierMatch[3]) + 1).'.'.$fichierExtension, $separateur);
            }
            // Premier passage, sans le séparateur
            return self::getCheminLibre(substr($fichier, 0, strlen($fichier) - strlen($fichierExtension) - 1).$separateur.'1'.'.'.$fichierExtension, $separateur);
        }
        return $fichier;
    }

    /**
     * Retourne l'encodage du fichier.
     * 
     * @return string|NULL Encodage du fichier ou NULL si non trouvé
     */
    public function getEncodage()
    {
        $encodage = mb_detect_encoding(file_get_contents($this->splFileInfo->getRealPath(), null, null, 1), mb_list_encodings());
        if (false === $encodage)
            $encodage = null;

        return $encodage;
    }


    /**
     * Retourne l'extension d'un fichier.
     *
     * @param string $filename Nom du fichier
     * @return string|NULL Extension
     */
    public static function getExtensionFromFile($filename)
    {
        if (false !== strpos($filename, '.')) {
            return substr($filename, strrpos($filename, '.') + 1);
        }

        return null;
    }
}
