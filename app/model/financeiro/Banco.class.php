<?php
/**
 * Banco Active Record
 * @author  Fred Az.
 */
class Banco extends TRecord
{
    const TABLENAME = 'banco';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;

    private static $bancos = [
         '001' => 'Banco do Brasil S.A.',
         '033' => 'Banco Santander (Brasil) S.A.',
         '104' => 'Caixa Econômica Federal',
         '237' => 'Banco Bradesco S.A.',
         '341' => 'Itaú Unibanco S.A.',
         '399' => 'HSBC Bank Brasil S.A.',
         '748' => 'Banco Cooperativo Sicredi S.A.',
         '041' => 'Banco do Estado do Rio Grande do Sul S.A.',
         '756' => 'Banco Cooperativo do Brasil S.A. - BANCOOB',
         '004' => 'Banco do Nordeste do Brasil S.A.'
    ];

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('num_banco');
        parent::addAttribute('nome_banco');
        parent::addAttribute('unit_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public static function getBancos() {
        return self::$bancos;
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $banco->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $banco->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    


}
