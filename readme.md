
# Sur Windows avec XAMPP ou WAMP :
Si vous utilisez XAMPP ou WAMP, la bibliothèque GD est généralement incluse par défaut. Cependant, vous pouvez vérifier si elle est activée en accédant au fichier php.ini et en recherchant la ligne suivante :

extension=gd

Assurez-vous que cette ligne n'est pas commentée (sans point-virgule ; au début de la ligne). Si elle est commentée, retirez le ;, enregistrez le fichier et redémarrez le serveur Apache.

Après avoir installé ou activé la bibliothèque GD, vous devriez pouvoir utiliser les fonctionnalités GD dans votre script PHP sans problème.