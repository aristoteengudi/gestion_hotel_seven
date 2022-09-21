<?php
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('./config.yaml');
$env = trim(file_get_contents('.env'));
$tmp_target_dir = (ini_get('upload_tmp_dir'))?ini_get('upload_tmp_dir'):'/tmp';


if(!$env){
    echo "Error: Veuiler indiquer l'environnement dans le fichier .env";
    exit;
}

$env_config_file = './config_'.$env.'.yaml';
if(!file_exists($env_config_file)){
    echo "Error: le fichier config pour environnement n'existe pas. merci de créer le fichier ". $env_config_file;
    exit;
}

$config = array_merge($config, Yaml::parseFile($env_config_file));
