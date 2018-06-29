<?php
namespace Anonymization\Database;

class Query {

    public $sql = '';

    public function __get($attr_name){

        if(array_key_exists($attr_name, $this->t))
            return $this->t[$attr_name];

    }

    public function __set($nom_att, $valeur){

        $this->t[$nom_att] = $valeur;

    }

    public function update ($fields){

        $this->sql = "UPDATE $fields ";

    }

    public function set ($parametres) {
        $this->sql .= " SET ";

        foreach ($parametres as $key => $parametre){

            $faker = explode('|', $parametre);

            if (isset($faker[1])){

                $flag = strpos($faker[1], '#');

                if($flag === false){

                    if (sizeof($faker) == 2){

                        unset($parametres[$key]);
                        $faker[1] = $this->getFaker($faker[1]);
                        $this->sql .=  "$faker[0]'$faker[1]', ";

                    }

                }else{

                    unset($parametres[$key]);
                    $format = explode('#', $faker[1]);
                    $this->sql .= $this->concat($faker[0], $format[0], $format[1]);

                }

            }

        }

        $this->sql .= implode( ', ', $parametres);

    }

    public function concat ($field, $format1, $format2) {

        $concat = "'$format1', " . "@rowid:=@rowid+1, " .  "'$format2'";
        $set = "$field CONCAT($concat), ";
        return $set;

    }

    public function where ($condition){

        $a = rtrim($this->sql, ', ');
        $this->sql = $a;
        $this->sql .= "$condition";

    }

    public function getFaker($f){

        $faker = \Faker\Factory::create();

        switch ($f) {
            case "title":
                return $faker->title;
                break;
            case "titleMale":
                return $faker->titleMale;
                break;
            case "titleFemale":
                return $faker->titleFemale;
                break;
            case "suffix":
                return $faker->suffix;
                break;
            case "name":
                return $faker->name;
                break;
            case "firstName":
                return $faker->firstName;
                break;
            case "firstNameMale":
                return $faker->firstNameMale;
                break;
            case "firstNameFemale":
                return $faker->firstNameFemale;
                break;
            case "cityPrefix":
                return $faker->cityPrefix;
                break;
            case "secondaryAddress":
                return $faker->secondaryAddress;
                break;
            case "state":
                return $faker->state;
                break;
            case "stateAbbr":
                return $faker->stateAbbr;
                break;
            case "citySuffix":
                return $faker->citySuffix;
                break;
            case "streetSuffix":
                return $faker->citySuffix;
                break;
            case "city":
                return $faker->city;
                break;
            case "streetName":
                return $faker->streetName;
                break;
            case "streetAddress":
                return $faker->streetAddress;
                break;
            case "postcode":
                return $faker->postcode;
                break;
            case "country":
                return $faker->country;
                break;
            case "latitude":
                return $faker->latitude;
                break;
            case "longitude":
                return $faker->longitude;
                break;
            case "phoneNumber":
                return $faker->longitude;
                break;
            case "tollFreePhoneNumber":
                return $faker->tollFreePhoneNumber;
                break;
            case "e164PhoneNumber":
                return $faker->e164PhoneNumber;
                break;
            case "email":
                return $faker->email;
                break;
            case "safeEmail":
                return $faker->email;
                break;
            case "freeEmail":
                return $faker->freeEmail;
                break;

            default:
                echo "No se ingreso un valor valido en el archivo de configuracion";
        }

    }

}
