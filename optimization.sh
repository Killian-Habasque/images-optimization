#!/bin/bash

# Demander à l'utilisateur le niveau de compression
read -p "Convert images to webp with (between 1 and 100) % : " compression

# Demander à l'utilisateur la largeur maximale
read -p "Resize images to a maximum size of (between 1 and 2000) px : " maxWidth

# Appeler le script PHP avec les variables
php optimization.php convert="$compression" resize="$maxWidth"
