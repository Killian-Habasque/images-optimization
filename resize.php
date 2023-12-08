<?php
// Définir le chemin du dossier racine
$path = "images-webp";

// Définir la taille maximale des images redimensionnées (en pixels)
$max_size = 700;

// Fonction récursive pour parcourir les dossiers et sous-dossiers
function recursiveResize($directory, $max_size) {
    // Ouvrir le dossier
    $dir = opendir($directory);
    $number =  0;
    
    // Parcourir les fichiers dans le dossier
    while(($file = readdir($dir)) !== false) {

        // Ignorer les fichiers cachés
        if(substr($file, 0, 1) == '.') continue;
       
        // Construire le chemin complet du fichier
        $filepath = $directory.'/'.$file;

        // Vérifier si le fichier est un dossier
        if(is_dir($filepath)) {
            // Si oui, appeler la fonction récursive sur ce dossier
            recursiveResize($filepath, $max_size);
        } else {
            // Si c'est un fichier, vérifier s'il s'agit d'une image
            if(preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {

                $number =  $number + 1;

                // Ouvrir l'image avec GD
                $image = imagecreatefromstring(file_get_contents($filepath));

                // Récupérer la largeur et la hauteur de l'image
                $width = imagesx($image);
                $height = imagesy($image);

                // Calculer le rapport de largeur/hauteur
                $aspect_ratio = $width / $height;

                // Calculer les nouvelles dimensions de l'image
                if($width > $max_size || $height > $max_size) {
                    if($width > $height) {
                        $new_width = intval($max_size);
                        $new_height = intval($max_size / $aspect_ratio);
                    } else {
                        $new_width = intval($max_size);
                        $new_height = intval($max_size * $aspect_ratio);
                    }
                } else {
                    $new_width = $width;
                    $new_height = $height;
                }

                // Créer une nouvelle image avec les nouvelles dimensions
                $new_image = imagecreatetruecolor($new_width, $new_height);

                // Copier et redimensionner l'image d'origine dans la nouvelle image
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                // Enregistrer la nouvelle image dans le même dossier avec le même nom de fichier
                if (preg_match('/\.webp$/i', $file)) {
                    imagewebp($new_image, $filepath);
                } else {
                    imagejpeg($new_image, $filepath);
                }

                // Libérer la mémoire utilisée par les images
                imagedestroy($image);
                imagedestroy($new_image);


                echo  "(" . $number . ")";
                echo  " - " . $directory;
                echo " - $file \n";
            }
        }
    }

    // Fermer le dossier
    closedir($dir);
}

// Appeler la fonction récursive sur le dossier racine
recursiveResize($path, $max_size);
?>
