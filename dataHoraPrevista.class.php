<?php
date_default_timezone_set ( 'America/Sao_Paulo' );

class dataHoraPrevista
{

    public $HORAFIN1;
    public $HORAFIN2;
    public $HORAINI1;
    public $HORAINI2;

    // hora de inicio do almoco
    private $inicioAlmoco;
    // hora de termino do almoco
    private $terminoAlmoco;
    // hora de inicio do expediente
    private $inicioExpediente;
    // hora de termino do expediente
    private $terminoExpediente;


    public function setDefaults()
    {
        $this->inicioAlmoco = $this->hora_em_minutos($this->HORAFIN1);
        $this->terminoAlmoco = $this->hora_em_minutos($this->HORAINI2);
        $this->inicioExpediente = $this->hora_em_minutos($this->HORAINI1);
        $this->terminoExpediente = $this->hora_em_minutos($this->HORAFIN2);
    }


    public function hora_em_minutos($strHora)
    {
        $min = 0;
        if (preg_match('@^(\d{2}):(\d{2})(:(\d{2}))?@', $strHora, $reg)) {
            $min = $reg [1] * 60 + $reg [2];
        }

        return $min;
    }


    public function calculaPrazoFinal($data, $prazo)
    {
        // seta os valores padroes
        $this->setDefaults();

        // verifica a data informada
        $res = preg_match('@^((\d{2}/\d{2}/\d{4})|(\d{4}-\d{2}-\d{2})) (\d{2}):(\d{2})(:\d{2})?$@', $data, $reg);
        // se nao esta no padrao
        if ($res == false) {
            throw new Exception ('Formato de data invalida');
        }

        // se for data no formato com barras - 25/07/2010
        if (!empty ($reg [2])) {
            $arr = explode('/', $reg [2]);
            $data = mktime(0, 0, 0, $arr [1], $arr [0], $arr [2]);

            // se for data no formato do banco - 2010-07-25
        } else {
            $arr = explode('-', $reg [3]);
            $data = mktime(0, 0, 0, $arr [1], $arr [2], $arr [0]);
        }

        // valor de um dia em segundos
        $day = 3600 * 24;

        // calcula o prazo em minutos
        $prazotime = $this->hora_em_minutos($prazo);
        // hora informada na data inicial em minutos
        $hora = $this->hora_em_minutos($reg [4] . ':' . $reg [5]);

        // enquanto houver prazo
        while ($prazotime > 0) {
            // incrementa a hora
            $hora++;
            // decrementa o prazo
            $prazotime--;

            // se a hora for maior que o expediente
            if ($hora > $this->terminoExpediente) {
                // adiciona um dia
                $data += $day;
                // volta para o inicio do expediente
                $hora = $this->inicioExpediente + 1;

                // se a hora for maior que o inicio do almoco e menor que o termino do almoco
            } else if ($hora > $this->inicioAlmoco && $hora < $this->terminoAlmoco) {
                // coloca para depois do almoco
                $hora = $this->terminoAlmoco + 1;
            }
        }

        // adiciona a hora encontrada (em segundos) na data final
        $data += $hora * 60;

        // retorna o timestamp da data
        return $data;
    }

    /* Abaixo criamos um array para registrar todos os feriados existentes durante o ano. */
    public function Feriados($ano, $posicao)
    {
        $dia = 86400;
        $datas = array();
        $datas ['pascoa'] = easter_date($ano);
        $datas ['sexta_santa'] = $datas ['pascoa'] - (2 * $dia);
        $datas ['carnaval'] = $datas ['pascoa'] - (47 * $dia);
        $datas ['corpus_cristi'] = $datas ['pascoa'] + (60 * $dia);
        $feriados = array(
            '01/01',
            '02/02', // Navegantes
            date('d/m', $datas ['carnaval']),
            date('d/m', $datas ['sexta_santa']),
            date('d/m', $datas ['pascoa']),
            '21/04',
            '01/05',
            date('d/m', $datas ['corpus_cristi']),
            '20/09', // Revolução Farroupilha \m/
            '12/10',
            '02/11',
            '15/11',
            '25/12'
        );

        return $feriados [$posicao] . "/" . $ano;
    }

    // Função soma mais um dia
    public function Soma1dia($data)
    {
        $dia = substr($data, 8, 2);
        $mes = substr($data, 5, 2);
        $ano = substr($data, 0, 4);
        return date("Y-m-d", mktime(0, 0, 0, $mes, $dia + 1, $ano));
    }

}