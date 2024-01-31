<?php

class SoftDeleteClass
{
	public function Delete($param)
	{	
		$id = $param['id'];
		$classe = $param['class'];
		$exploderClass = explode('List',$classe);
		$class = $exploderClass;

		try
		{	
			TTransaction::open('sample');
			$obj = new $class[0]($id);
			$obj->deleted_at = date('Y-m-d H:i:s');
			$obj->store();
			TTransaction::close();

       	    new TMessage('info', "Deletado com sucesso!");
		}
        catch (Exception $e) {
   
            throw new Exception('Problema ao deletar registro. <br>' . $e->getMessage());
            return;
		}
	}

}