<?php

namespace App\Model\Entity;

class Organization{
    
    /**
     * id da org
     * @var int
     */
    private static $id = 1;
        
    /**
     * Nome da Organização
     * @var string
     */
    private static $name = "Etec";
    
    /**
     * site dos criadores
     * @var string
     */
    private static $site = "https://www.github.com/adornos";
    
    /**
     * Descrição da organização
     *
     * @var string
     */
    private static $description = "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Commodi odit laudantium possimus iure magnam dicta sapiente officia voluptatum consequuntur? Nobis cupiditate asperiores expedita tempora reiciendis, praesentium assumenda quaerat amet dolore.";

    public static function getOrganizationData(){

        return [
            'name' => self::$name,
            'description' => self::$description,
            'site' => self::$site
        ];

    } 
    
}

?>