
# Optimisation d'Images
*Ce projet est conçu pour simplifier l'optimisation d'images en permettant la conversion vers le format WebP avec un facteur d'optimisation réglable de 0 à 100 %, ainsi que le redimensionnement des images à des tailles comprises entre 1 et 2000 pixels.

## Utilisation

1. **Placer les images à optimiser :** Les images à optimiser doivent être placées dans le dossier images. Vous pouvez organiser les images dans des sous-dossiers pour une meilleure gestion.

2. **Exécution du script :** Pour exécuter le processus d'optimisation, utilisez le script 'optimization.sh'. Ce script permet de paramétrer le niveau de compression pour la conversion en WebP et la taille maximale pour le redimensionnement des images.

```bash
#!/bin/bash

# Demander à l'utilisateur le niveau de compression
read -p "Convertir les images en WebP avec un taux de compression (entre 1 et 100) % : " compression

# Demander à l'utilisateur la largeur maximale
read -p "Redimensionner les images à une taille maximale de (entre 1 et 2000) px : " maxWidth

# Appeler le script PHP avec les variables
php optimization.php convert="$compression" resize="$maxWidth"

```

Vous pouvez également le faire par ligne de commande : 
```bash
#!/bin/bash

# Redimensionnement
php optimization.php resize="$maxWidth"

# Conversion / Facteur de compression
php optimization.php convert="$compression"

# Regroupement des 2 fonctions
php optimization.php convert="$compression" resize="$maxWidth"

```
3. **Recupérer les images optimisés :** Les images converties et redimensionnées seront enregistrées dans le dossier images-optimization. 


## Dépendances
Assurez-vous que la bibliothèque GD de PHP est installée sur votre système. Elle est nécessaire pour manipuler les images.
