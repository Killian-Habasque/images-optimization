<?php
ini_set('memory_limit', '256M');

function slugify($text)
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/\s+/', '-', $text);
    return $text;
}

function convertImages($directory, $webpDirectory, $compressionFactor)
{
    if (!is_dir($webpDirectory)) {
        mkdir($webpDirectory, 0777, true);
    }

    $handle = opendir($directory);
    $i = 0;

    while (false !== ($file = readdir($handle))) {
        if (in_array($file, array('.', '..'))) {
            continue;
        } elseif (is_dir($directory . '/' . $file)) {
            $i = 0;
            convertImages($directory . '/' . $file, $webpDirectory . '/' . $file, $compressionFactor);
        } elseif (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif'))) {
            $imagePath = $directory . '/' . $file;

            switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($imagePath);
                    break;
            }

            $newName = slugify(pathinfo($file, PATHINFO_FILENAME)) . '.webp';

            imagewebp($image, $webpDirectory . '/' . $newName, $compressionFactor);

            $i++;
            echo  "(" . $i . ")";
            echo  " - " . $directory;
            echo " - $newName \n";

            imagedestroy($image);
        }
    }

    closedir($handle);
}

function resizeImages($directory, $maxSize, $targetDirectory = null)
{
    $dir = opendir($directory);
    $number = 0;

    while (($file = readdir($dir)) !== false) {
        if (substr($file, 0, 1) == '.') {
            continue;
        }

        $filePath = $directory . '/' . $file;

        if (is_dir($filePath)) {
            $subTargetDirectory = $targetDirectory ? $targetDirectory . '/' . $file : null;
            resizeImages($filePath, $maxSize, $subTargetDirectory);
        } else {
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                $number++;
                $image = imagecreatefromstring(file_get_contents($filePath));
                $width = imagesx($image);
                $height = imagesy($image);
                $aspectRatio = $width / $height;

                if ($width > $maxSize || $height > $maxSize) {
                    $newWidth = ($width > $height) ? intval($maxSize) : intval($maxSize / $aspectRatio);
                    $newHeight = ($width > $height) ? intval($maxSize / $aspectRatio) : intval($maxSize);
                } else {
                    $newWidth = $width;
                    $newHeight = $height;
                }

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                $targetPath = $targetDirectory ? $targetDirectory . '/' . $file : $directory . '/' . $file;

                if (!is_dir(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0777, true);
                }

                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }

                if (preg_match('/\.webp$/i', $file)) {
                    imagewebp($newImage, $targetPath);
                } else {
                    imagejpeg($newImage, $targetPath);
                }

                imagedestroy($image);
                imagedestroy($newImage);

                echo "(" . $number . ")";
                echo " - " . ($targetDirectory ? $targetDirectory : $directory);
                echo " - $file \n";
            }
        }
    }

    closedir($dir);
}

$commands = [];
foreach ($argv as $arg) {
    $parts = explode('=', $arg);
    if (count($parts) === 2) {
        $commands[$parts[0]] = $parts[1];
    }
}

if (count($commands) > 0) {
    $imagesDirectory = 'images';
    $optimizedImagesDirectory = 'images-optimization';
    if (isset($commands['convert'])) {
        if ($commands['convert'] <= 100 && $commands['convert'] > 0) {
            $compressionFactor = intval($commands['convert']);
            echo "\n------------Convert images to webp with " . $compressionFactor . "% compression\n\n";
            convertImages($imagesDirectory, $optimizedImagesDirectory, $compressionFactor);
        } else {
            echo "Please specify a conversion rate between 1 and 100." . PHP_EOL;
        }
    }
    if (isset($commands['resize'])) {
        if ($commands['resize'] <= 2000 && $commands['resize'] > 0) {
            $maxSize = intval($commands['resize']);
            echo "\n------------Resize images to a maximum size of " . $maxSize . "px\n\n";
            resizeImages($imagesDirectory, $maxSize, $optimizedImagesDirectory);
        } else {
            echo "Please specify an image size between 1 and 2000." . PHP_EOL;
        }
    }
} else {
    echo "Please specify conversion/resize commands (convert=20 resize=800)." . PHP_EOL;
}
