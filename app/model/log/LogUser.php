<?php
/**
 * LogsUsuario Active Record
 * @author  Fred Azv.
 */
class LogUser extends TRecord
{
    const TABLENAME = 'logs_usuario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('logdate');
        parent::addAttribute('usuario');
        parent::addAttribute('programa');
        parent::addAttribute('transaction_id');
        parent::addAttribute('log_year');
        parent::addAttribute('log_month');
        parent::addAttribute('log_day');
    }


}
