<?php

namespace Classes; 

class Funcao {
    /**
     * Verifica a Versão do PHP
     * Returna TRUE para comporta a versao, e false para versao nao compativel
     * 
     * @param type $versao
     * @return boolean
     * 
     * @version 2.0
     * @author Ricardo Sierra <web@ricardosierra.com.br>
     */
    public static function VersionPHP($versao){
        if (strnatcmp(phpversion(),$versao) >= 0) return TRUE;
        else                                        return FALSE;
    }
    static public function Gerar_Senha($tamanho=8, $forca=6) {
        
        $vogais             = '2357';
        $consoantes         = '014689';
        
        if ($forca >= 2) {
            $consoantes .= 'bcdfghjklmnpqrstvwxz';
        }
        if ($forca >= 3) {
            $vogais .= 'aeiouy';
        }
        if ($forca >= 5) {
            $consoantes    .= 'BCDFGHJKLMNPQRSTVWXZ';
        }
        if ($forca >= 6) {
            $vogais        .= "AEIOUY";
        }
        if ($forca >= 8 ) {
            $vogais .= '*@';
        }
        if ($forca >= 10 ) {
            $vogais .= '-!#%$';
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

