<?php

require "dataHoraPrevista.class.php";


$dataHoraPrevista = new dataHoraPrevista;

$dataHoraPrevista->HORAINI1 = '00:00';
$dataHoraPrevista->HORAFIN1 = '12:00';
$dataHoraPrevista->HORAINI2 = '12:00';
$dataHoraPrevista->HORAFIN2 = '24:00';

$prazo = '12'; //12hs

$srt = strlen($prazo);
if ($srt == '1') {
    $prazo = '0' . $prazo . ":00";
} else {
    $prazo = $prazo . ":00";
}


$dtPrevisao = $dataHoraPrevista->calculaPrazoFinal(date('Y-m-d H:i'), $prazo);

//echo date('Y-m-d H:i',$dtPrevisao);

$dataPrevista = date('Y-m-d', $dtPrevisao);
$horaPrevista = date('H:i', $dtPrevisao);


//Verifica se a data de previsão é sábado, docmingo ou feriado;

$diaSemana = date('w', $dtPrevisao);
$a1 = '';
if ($diaSemana == 0 || $diaSemana == 6) {
    // se SABADO OU DOMINGO, SOMA 01
    $a1 = '0';
} else {
    for ($i = 0; $i <= 12; $i++) {
        if ($dataPrevista == $dataHoraPrevista->Feriados(date('Y'), $i)) {
            $a1 = '1';
        }
    }
}

//Soma 1 dia caso for final de semana ou feriado.
switch ($a1) {
    case '0' :
        $diaSemana = date('w', $dtPrevisao);
        if ($diaSemana == 6) {
            $dtPrevisao = $dataHoraPrevista->Soma1dia($dataPrevista);
            $dataPrevista = $dataHoraPrevista->Soma1dia($dtPrevisao);
        } else {
            $dataPrevista = $dataHoraPrevista->Soma1dia($dataPrevista);
        }
        break;
    case '1' :
        $dataPrevista = $dataHoraPrevista->Soma1dia($dataPrevista);

        $diaSemanaF = date('w', strtotime($dataPrevista));
        if ($diaSemanaF == 6) {
            $dtPrevisao = $dataHoraPrevista->Soma1dia($dataPrevista);
            $dataPrevista = $dataHoraPrevista->Soma1dia($dtPrevisao);
        } else {
            $dataPrevista = $dataHoraPrevista->Soma1dia($dataPrevista);
        }
        break;
    default :
        $dataPrevista;
        break;
}

echo $dataPrevista.' '.$horaPrevista;