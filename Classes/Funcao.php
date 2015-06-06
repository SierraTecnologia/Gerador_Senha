<?php

namespace Classes; 

class Funcao {
    static public function Gerar_Senha($tamanho=8, $forca=6) {
        
        $vogais             = '2357';
        $consoantes         = '014689';
        
        if ($forca >= 1) {
            $consoantes .= 'bcdfghjklmnpqrstvwxz';
        }
        if ($forca >= 2) {
            $vogais .= 'aeiouy';
        }
        if ($forca >= 3) {
            $consoantes    .= 'BCDFGHJKLMNPQRSTVWXZ';
        }
        if ($forca >= 4) {
            $vogais        .= "AEIOUY";
        }
        if ($forca >= 7 ) {
            $vogais .= '*@';
        }
        if ($forca >= 8 ) {
            $vogais .= '-!#%$';
        }
        if ($forca >= 9 ) {
            $consoantes .= 'çÇ';
        }
        if ($forca >= 10 ) {
            $vogais .= 'áÁ';
        }

        $senha = '';
        $alt = time() % 2;
        for ($i = 0; $i < $tamanho; $i++) {
            if ($alt == 1) {
                $senha .= $consoantes[(rand() % strlen($consoantes))];
                $alt = 0;
            } else {
                $senha .= $vogais[(rand() % strlen($vogais))];
                $alt = 1;
            }
        }
        return $senha;
    }
    static public function anti_injection($sql,$tags=false){
         if(is_array($sql)){
             $seg = Array();
             foreach($sql as $indice=>&$valor){
                 $seg[\anti_injection($indice)] = \anti_injection($valor,$tags);
             }
             $sql = $seg;
         }else{
            /*// remove palavras que contenham sintaxe sql
            $sql = mysql_real_escape_string($sql);
            if($tags===false){
                $sql = strip_tags($sql);//tira tags html e php
            }*/
         }
         return $sql;
    }
}

