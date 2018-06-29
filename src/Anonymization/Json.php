<?php
namespace Anonymization\Anonymization;

class Json extends Documents{

    /**
     * Json constructor.
     * @param $file
     */
    public function __construct($file){

        $data = file_get_contents($file);

        $this->config = json_decode($data, true);

    }

}
