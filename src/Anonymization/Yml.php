<?php
namespace Anonymization\Anonymization;

use Symfony\Component\Yaml\Yaml as Yaml;

class Yml extends Documents {

    /**
     * Yml constructor.
     * @param $file
     */
    public function __construct($file){

        $this->config = Yaml::parse($file);

    }

}
