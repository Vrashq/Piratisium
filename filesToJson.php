<?php

// Modifier en conséquence entre ici
$jsonAssetsUrl = "assets.json"; // Source du fichier json dans lequel on écrit la liste des assets
$assetsBaseUrl = "assets"; // Chemin relatif vers le dossier des assets

$excludeFolders = array(".", "..", "font", "src"); // Dossiers à exclure de la lecture (dossiers des sources .psd par exemple)
$type = array('png', 'json'); // Types de fichiers autorisés à récupérer
// Et ici !

$mostRecentFile = 0; // Date de modification du dplus récent fichier modifié

function readFolder($baseUrl)
{
	global $type, $excludeFolders, $mostRecentFile;
	$fileList = array();

	if(!is_file($baseUrl))
	{
		if ($handler = opendir($baseUrl))
		{
			while (false !== ($entry = readdir($handler)))
			{
				if(!in_array($entry, $excludeFolders))
				{
					$fileExplode = explode(".",$entry);
					if(count($fileExplode) > 1)
					{
						if(in_array(explode(".",$entry)[1], $type))
						{
							$fileList[] = $baseUrl . "/" . $entry;
							if(filemtime($baseUrl) > $mostRecentFile)
								$mostRecentFile = filemtime($baseUrl);
						}
					}
					else
						$fileList[$entry] = readFolder($baseUrl . "/" . $entry);
				}
			}
			closedir($handler);
		}
	}
	else
		$fileList[] = $baseUrl;
	return $fileList;
}

// Retourne un tableau avec les dossiers et fichiers dans l'ordre de l'architecture
$filesList = readFolder($assetsBaseUrl);

// Si le json des assets n'existe pas ou si une modification a été faite, on réécrit le json
if(!file_exists($jsonAssetsUrl) || ($mostRecentFile > filemtime($jsonAssetsUrl)))
{
	file_put_contents("assets.json", str_replace("\\/", "/",json_encode($filesList, JSON_PRETTY_PRINT)));
	echo "<script>console.info(\"#Assets - json updated !\")</script>";
}