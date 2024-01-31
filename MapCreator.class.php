<?php

class MapCreator {

    private $pastas = array();
    private $classes = array();

    function __construct(array $pastas) {
        $this->pastas = $pastas;
    }

    private function getClassFromPath(array $paths) {
        foreach ($paths as $key => $path) {

            $files = scandir($path);
            foreach ($files as $key => $file) {
                if ($file != "." && $file != "..") {
                    if (is_file($path . $file)) {
                        $file_exploded = explode(".", $file);
                        if (end($file_exploded) == "php") {
                            $classe = new stdClass;
                            $classe->nome = $file_exploded[0];
                            $classe->caminho = $path . $file;
                            $this->classes[] = $classe;
                        }
                    } else if (is_dir($path . $file)) {
                        $this->getClassFromPath(array($path . $file . "/"));
                    }
                }
            }
        }
    }

    public function getMap() {
        $this->getClassFromPath($this->pastas);
        $content = "<?php" . PHP_EOL . PHP_EOL;
        $content .= "use Adianti\Core\AdiantiCoreLoader;" . PHP_EOL . PHP_EOL;
        foreach ($this->classes as $key => $classe) {
            $content .= "AdiantiCoreLoader::setClassPath('{$classe->nome}','{$classe->caminho}');" . PHP_EOL;
        }
        file_put_contents("map.php"  , $content);
        //echo highlight_string($content);
    }
}