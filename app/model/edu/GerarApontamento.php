<?php
/**
 * Apontamento Active Record
 * @author  Fred Azv.
 */

class GerarApontamento
{
    public static function apontar($aluno_id, $disciplina_id, $matricula_id, $serie_id, $turma_id, $turno_id, $anoletivo_id, $unit_id) {

        try {

            TTransaction::open('sample');

            $apontamento = new Apontamento;
            $apontamento->aluno_id = $aluno_id;
            $apontamento->disciplina_id = $disciplina_id;
            $apontamento->matricula_id = $matricula_id;
            $apontamento->serie_id = $serie_id;
            $apontamento->turma_id = $turma_id;
            $apontamento->turno_id = $turno_id;
            $apontamento->anoletivo_id = $anoletivo_id;
            $apontamento->unit_id = $unit_id;
            $apontamento->a_1bim = 0;
            $apontamento->a_2bim = 0;
            $apontamento->a_3bim = 0;
            $apontamento->a_4bim = 0;
            $apontamento->ta_anual = 0;
            $apontamento->f_1bim = 0;
            $apontamento->f_2bim = 0;
            $apontamento->f_3bim = 0;
            $apontamento->f_4bim = 0;
            $apontamento->tf_anual = 0;
            $apontamento->p_ibim = 0;
            $apontamento->p_2bim = 0;
            $apontamento->p_3bim = 0;
            $apontamento->p_4bim = 0;
            $apontamento->tp_anual = 0;
            $apontamento->ft_1bim = 0;
            $apontamento->ft_2bim = 0;
            $apontamento->ft_3bim = 0;
            $apontamento->ft_4bim = 0;
            $apontamento->ft_anual = 0;
            $apontamento->store();

            TTransaction::close();
        }
        catch (Exception $e) {
            TTransaction::rollback();
            throw new Exception('Problema ao gerar os apontamentos. <br>' . $e->getMessage());
            return;
        }

    }
}
