<?php
namespace Classes;
Class Visual{
    // Registro e Classes do Framework
    protected $_Registro;
    protected $_Acl;
    
    // Config Visual
    private $head_js                    = '';
    public $layoult                     = 'completo';
    
    // Staticas
    public static $layoult_idaleatorio      = 0;
    public static $config_template;
    
    // Lang
    protected $sistema_linguagem        = 'ptBR';
    protected $Layolt_Tipo              = 0;

    // Variaveis de Template
    private $template_url;
    private $template_dir;
    private $template_config_dir;
    private $_widgets_params            = Array();
    
    // Minimiza
    private $arquivos_js                = Array();
    private $arquivos_css               = Array();
    private $arquivos_js_dependencia    = Array();
    private $arquivos_css_dependencia   = Array();
    
    // Variaveis do Json
    private $jsonativado                = false;
    private $json                       = array();
    private $jsontipoqnt                = 0;
    
    // Manipulação dos Blocos
    private     $conteudo;
    protected   $blocos                 = Array();
    protected   $Layoult_BlocoUnico     = Array();
    protected   $Layoult_BlocoMaior     = Array();
    protected   $Layoult_BlocoMenor     = Array();
    // Widgets
    protected static $widgets_inline        = Array();

    //menu
    private $contmenu                   = ''; //janelas do menu
    public  $menu                       = array(
        'link'      => Array(),
        'nome'      => Array(),
        'img'       => Array(),
        'ativo'     => Array(),
        'icon'      => Array(),
        'filhos'    => Array()
    ); // menus (nomes e links)
    
    public function Javascript_Executar($javascript=''){
        // Se parametro vier falso, zera javascript pra executar
        if($javascript===false){
            $this->head_js = '';
            return true;
        }else
        // Se parametro nao for falso e igual a zero entao add js
        if($javascript!==''){
            $this->head_js .= $javascript;
            return true;
        }else
        // Caso contrario (seja só vazio), retorna o js
        {
            return $this->head_js;
        }
    }
    /***************************************************\
    *                                                   *
    *                FUNCOES PARA JSON                  *
    *                                                   *
    \***************************************************/
    /**
     * 
     * @param type $title
     * @param type $historico
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_Start($title='',$historico=true){
        if($this->jsonativado===false){
            $this->json['Info'] = array(
                'Titulo' => $title,
                'Historico' => $historico,
                'Tipo' => array(),
                'callback' => ''
            );
            $this->jsonativado = true;
        }
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    private function Json_Get_Titulo(){
        if(isset($this->json['Info']['Titulo']) && $this->json['Info']['Titulo']!=='' && $this->json['Info']['Titulo']!==false){
            return $this->json['Info']['Titulo'];
        }else{
            return false;
        }
    }
    /**
     * 
     * @param type $indice
     * @param type $valor
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_Info_Update($indice,$valor){
        if($this->jsonativado===false){
            $this->Json_Start();
        }
        $this->json['Info'][$indice] = $valor;
    }
    /**
     * 
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_Exist(){
        if($this->jsonativado===false){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 
     * @param type $tipo
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_ExisteTipo($tipo){
        if(isset($this->json[$tipo])){
            return true;
        }else{
            return false;
        }

    }
    /**
     * retira Algo Ultrapassado Do Json
     * @param type $id
     * @return boolean
     */
    public function Json_RetiraTipo($id){
        if(!empty($this->json['Conteudo'])){
            foreach($this->json['Conteudo'] as $indice=>&$valor){
                if($valor['location'] == $id){
                    unset($this->json['Conteudo'][$indice]); 
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * 
     * @param type $tipo
     * @param type $array
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_IncluiTipo($tipo,&$array){
        // Se nao tiver json, ativa
        if($this->jsonativado===false){
            $this->Json_Start();
        }
        if(array_search($tipo, $this->json['Info']['Tipo'])===false){
            if($tipo=='JavascriptInterno'){
                array_push($this->json['Info']['Tipo'], $tipo);
            }else{
                array_unshift($this->json['Info']['Tipo'], $tipo);
            }
        }
        // Dependendo do Tipo, faz a inserção necessaria
        if($tipo=='Redirect'){
            if(!isset($this->json['Redirect'])){
                $this->json['Redirect'] = Array();
            }
            $this->json['Redirect'][] = $array;
        }else if($tipo=='Popup'){
            if(!isset($this->json['Popup'])){
                $this->json['Popup'] = Array();
            }
            $botoes = Array();
            if(isset($array['width'])){
                $largura    = $array['width'];
            }
            else{
                $largura    = 800;
            }
            if(isset($array['height'])){
                $altura     = $array['height'];
            }
            else{
                $altura     = 600;
            }
            if(isset($array['botoes'])){
                foreach ($array['botoes'] as &$valor) {
                    $botoes[] = Array(
                        'text'      => $valor['text'],
                        'clique'    =>  $valor['clique']
                    );
                }
            }
            $this->json['Popup'] = array(
                "id" => $array['id'],
                "title" => $array['title'],
                "width" => $largura,
                "height" => $altura,
                "botoes" => $botoes,
                "html" => $array['html']
            );
        }elseif($tipo=='Mensagens'){
            if(!isset($this->json['Mensagens'])){
                $this->json['Mensagens'] = Array();
            }
            $this->json['Mensagens'][] = array(
                "tipo" => $array['tipo'],
                "mgs_principal" => $array['mgs_principal'],
                "mgs_secundaria" => $array['mgs_secundaria']
            );
        }elseif($tipo=='Conteudo'){
            if(!isset($this->json['Conteudo'])){
                $this->json['Conteudo'] = Array();
            }
            $this->json['Conteudo'][] = array(
                "location" => $array['location'],
                "js" => $array['js'],
                "html" => $array['html']
            );
        }elseif($tipo=='Select'){
            if(!isset($this->json['Select'])){
                $this->json['Select'] = Array();
            }
            $this->json['Select'][] = array(
                "id" => $array['id'],
                "valores" => $array['valores']
            );
        }elseif($tipo=='Javascript'){
            if(!isset($this->json['Javascript'])){
                $this->json['Javascript'] = Array();
                if(is_array($array)){
                    $this->json['Javascript'] = $array;
                    return true;
                }
            }
            if(is_array($array)){
                $this->json['Javascript'] = array_merge($this->json['Javascript'], $array);
            }else{
                $this->json['Javascript'][] = $array;
            }
        }elseif($tipo=='JavascriptInterno'){
            if(!isset($this->json['JavascriptInterno'])){
                $this->json['JavascriptInterno'] = Array();
            }
            $this->json['JavascriptInterno'][] = $array;
        }elseif($tipo=='Css'){
            if(!isset($this->json['Css'])){
                $this->json['Css'] = Array();
                if(is_array($array)){
                    $this->json['Css'] = $array;
                    return true;
                }
            }
            if(is_array($array)){
                $this->json['Css'] = array_merge($this->json['Css'], $array);
            }else{
                $this->json['Css'][] = $array;
            }
        }
        return false;
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_Retorna(){
        //$imprimir = new \Framework\App\Tempo('Retornar Json Visual - SEM SMARTY');
        if($this->jsonativado===false){
            $this->Json_Start();
        }
        if($this->json['Info']['Titulo']!=''){
            $html='';
            if(isset($this->menu['SubMenu'])){
                $tamanho = sizeof($this->menu['SubMenu']['link']);
                for($i = 0; $i<$tamanho; $i++){
                    $html .= '<li><a href="'.$this->menu['SubMenu']['link'][$i].'" class="lajax-mesub';
                    if($this->menu['SubMenu']['ativo'][$i]==1){
                        $html .= ' active';
                    }
                    $html .= '" acao="">'.$this->menu['SubMenu']['nome'][$i].'</a></li>';
                }
            }
            $conteudo = array(
                "location" => "#sub-menu",
                "js" => "",
                "html" => $html
            );
            $this->Json_IncluiTipo('Conteudo',$conteudo);
        }

        return $this->Json_Codificar();
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Json_Codificar(){

        if (\Classes\Funcao::VersionPHP('5.3.10'))
        {
            // retirei JSON_UNESCAPED_UNICODE
            return json_encode($this->json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        }
        else
        {
            // caso a versao do php nao seja 5.3.10 para cima, entao nao suporta json_encode e fara o seguinte
            if (!function_exists('json_encode')) {
                function json_encode($data) {
                    switch ($type = gettype($data)) {
                        case 'NULL':
                            return 'null';
                        case 'boolean':
                            return ($data ? 'true' : 'false');
                        case 'integer':
                        case 'double':
                        case 'float':
                            return $data;
                        case 'string':
                            return '"' . addslashes($data) . '"';
                        case 'object':
                            $data = get_object_vars($data);
                        case 'array':
                            $output_index_count = 0;
                            $output_indexed = array();
                            $output_associative = array();
                            foreach ($data as $key => &$value) {
                                $output_indexed[] = json_encode($value);
                                $output_associative[] = json_encode($key) . ':' . json_encode($value);
                                if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                                    $output_index_count = NULL;
                                }
                            }
                            if ($output_index_count !== NULL) {
                                return '[' . implode(',', $output_indexed) . ']';
                            } else {
                                return '{' . implode(',', $output_associative) . '}';
                            }
                        default:
                            return ''; // Not supported
                    }
                }
            }
            // faltou JSON_UNESCAPED_UNICODE
            // Faz as substituicoes necessarias para o encode funcionar
            $contem     = array('<'    , '>'     , "'"     , '"'     , '&s');
            $alterar    = array('\u003C', '\u003E', "\u0027", '\u0022', '\u0026');

            $this->json = str_replace($contem, $alterar, $this->json);
            return json_encode($this->json);
        } 
    }
}
?>