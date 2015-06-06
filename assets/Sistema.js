/**
 * 
 * @type Function|Sistema_L5.SistemaAnonym$0
 * 
 * 
 * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
 */
// Puxa Sierra
var Sierra = (function () {
    "use strict";
    // VARIAVEIS PRIMARIAS
    var SiteHash                    = '', 
        SiteCarregando              = false,
        Historico_Controle          = '0',

        // Cache Interno Para quem nao tem HTML5
        Cache                       = {}, 

        MsgFila                     = new Array(), 
        MgsAtivo                    = 0, 
        Config_Formulario_Vazios    = [
            "",                   // Vazio Normal
            "__/__/____",         // DATA
            "__/__/____ __:__:__",// DATA HORA
            "__:__",              // Hora
            "____",               // ANO
            "__/____",            // Validade
            "(__) ____-____",     // Telefone
            "__.___-___",         // CEP
            "___.___.___-__",     // CPF
            "__.___.___/____-__", // CNPJ
            "_______-_",          // Inscricao Municipal
            "__.___.___-_",       //RG
            "R$ 0,00"             // REAL
        ],
        DataTable_Selected          = [],
        documento                   = $(document),
        janela                      = $(window);

    /**
     * TRANSFORMA FORMULARIOS PRA AJAX
     */
    documento.on("submit", 'form', function () {
        Control_Form_Tratar(this);
        return false;
    });
    	  
    janela.load(function () {
    });
    
    /**
     * FAZ SEMPRE QUE SE USA AJAX
     * @returns {undefined}
     * @version 3.1.1 // Mudado a fim da reaproveitacao de código, aqui repetia o conteudo de mascaras
    * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Layoult_Recarrega () {
        
    }
    /**
     * 
     * @param {type} tipo
     * @param {type} mensagem_principal
     * @param {type} mensagem_secundaria
     * @returns {undefined}
    * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_PopMgs_Abrir (tipo,mensagem_principal,mensagem_secundaria) {
        if (tipo === 'sucesso') {
            toastr.success(mensagem_secundaria, mensagem_principal);
        }else if (tipo === 'erro') {
            toastr.error(mensagem_secundaria, mensagem_principal);
        }else if (tipo === 'aviso') {
            toastr.warning(mensagem_secundaria, mensagem_principal);
        }else{
            toastr.info(mensagem_secundaria, mensagem_principal);
        }
    };
    /**
     * 
     * @param {type} formulario
     * @returns {Boolean}
     * 
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Form_Tratar (formulario) {
        var $button = $('button',formulario).attr('disabled',true), //puxa tanto button quanto formulario
            params = $(formulario.elements).serialize(),
            //var self = this,
            id = $(formulario),
            url = formulario.action,
            passar = true;

        // verifica se existe validor
        id.find('input').each(function (i) {
            var elemento        = $(this),
                funcao_valida   = elemento.attr("validar"),
                esta_escondido  = elemento.attr("escondendo"),
                atr_max  = elemento.attr("max"),
                atr_min  = elemento.attr("min"),
                valor           = elemento.val(),
                validar         = true;
            elemento.removeClass('obrigatoriomarcado');
            // Se tiver atr de max verifica
            if (atr_max !== undefined){
                if(parseInt(valor)>parseInt(atr_max)){
                    Control_PopMgs_Abrir('erro','Número Inválido',valor+' é maior que '+atr_max);
                    elemento.addClass('obrigatoriomarcado').focus();
                     passar = false;
                }
            }
            // Se tiver atr de min verifica
            if (atr_min !== undefined){
                console.log(valor,atr_min);
                if(parseInt(valor)<parseInt(atr_min)){
                    Control_PopMgs_Abrir('erro','Número Inválido',valor+' é menor que '+atr_min);
                    elemento.addClass('obrigatoriomarcado').focus();
                     passar = false;
                }
            }
            if (funcao_valida !== undefined && inArray(valor,Config_Formulario_Vazios) === false && funcao_valida !== 'undefined' && esta_escondido !== 'ativado') {
                validar = Control_Layoult_Validar(elemento,funcao_valida,valor);
                
                if (validar === false){ 
                    passar = false;
                    elemento.addClass('obrigatoriomarcado').focus();   
                }
                // verifica se pode passar
            }
        }); 
        id.find('.obrigatorio').each(function (i) {
            var elemento                    = $(this),
                esta_escondido              = elemento.attr("escondendo"),
                valor                       = elemento.val(),
                identificador               = $(document.getElementById(elemento.attr("id")+"_chosen")),
                /*identificador_a             = identificador.children("a"),
                identificador_a_tamanho     = identificador_a.length,*/
                identificador_ch            = identificador.children(".chosen-drop"),
                identificador_ch_tamanho    = identificador_ch.length;
            if (inArray(valor, Config_Formulario_Vazios) === true  && esta_escondido !== 'ativado') {
                passar = false;
                Control_PopMgs_Abrir('erro','Campo Obrigatório Vazio','Por favor Preencha Todos os Campos');
                // Aplica a cor de fundo
                elemento.addClass('obrigatoriomarcado').focus();

                /*if (identificador_a_tamanho) {
                    identificador_a.addClass('obrigatoriomarcado').focus();
                }*/
                if (identificador_ch_tamanho) {
                    identificador.addClass('obrigatoriomarcado');
                }
            }else{
                // Aplica a cor de fundo
                elemento.removeClass('obrigatoriomarcado');

                /*if (identificador_a_tamanho) {
                    identificador_a.removeClass('obrigatoriomarcado');
                }*/
                if (identificador_ch_tamanho) {
                    identificador.removeClass('obrigatoriomarcado');
                }
            }
        });
        // verifica se pode passar
        if (passar === true) {
            Modelo_Ajax_Chamar(url,params,'POST',true,false,true);
            Control_Ajax_Popup_Fechar('popup');
            $button.attr('disabled',false);
            //self.reset();
            return true;
        }else{
            $button.attr('disabled',true);
        }
        return false;
    };
    /**
     * FUNCAO PRA CHAMAR AJAX
     * @param {type} url
     * @param {type} data
     * @param {type} navegador
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Modelo_Ajax_JsonTratar (url, data, navegador) {
        var cod = '',
            i   = 0,
            tam;
        if (data !== null && typeof(data) === "object") {
            // Verifica se foi chamado pelo historico do navegador
            if (typeof(navegador) === "undefined") {
                navegador = false; //False
            }
            // Verifica Titulo
            if (data['Info']['Titulo'] !== '') {
                document.title = data['Info']['Titulo'];
                document.getElementById('Framework_Titulo').innerHTML = data['Info']['Titulo'];
            }
            // Atualiza Link se tiver historico e nao for via navegador
            if (navegador === false && data['Info']['Historico'] === true) {
                Control_Link_Atualizar(url/*, data*/);
            }
            // Chama os Tipos de Json
            tam = Object.keys(data['Info']['Tipo']).length;
            for(;i<tam;++i){
                console.log(data['Info']['Tipo'][i],'data[\''+data['Info']['Tipo'][i]+'\']');
                cod += 'Control_Ajax_'+data['Info']['Tipo'][i]+'(data[\''+data['Info']['Tipo'][i]+'\']);';
            }
            eval(cod);
            Control_Layoult_Recarrega();
        }else{
            console.log('Erro',data);
        }
    };
    /**
     * 
     * @param {type} url
     * @param {type} params
     * @param {type} tip
     * @param {type} resposta
     * @param {type} historico 
     * @param {bool} carregando caso true aparece mensagem carregando
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Modelo_Ajax_Chamar (url, params, tip, resposta, historico,carregando) {
        console.time('Acao_LINK');
        var retorno = false;
        //retorno = Cache_Ler(url);
        console.log('Retorno',retorno);
        if(retorno!==false){
            Modelo_Ajax_JsonTratar(url,retorno,historico);
        }else{
            if(carregando===true){
                Control_PopMgs_Carregando();
            }
            /* 
            * XMLHttpRequest2 (html5) -> Opera Mini ainda nao suporta, nao usar por enquanto
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
              //done
            }
            xhr.open("GET", "http://jsperf.com");
            xhr.send(null);
             */
            // Verifica se Contem http ou www se nao tiver acrescenta url do sistema
            if(url.indexOf('http://') === -1 && url.indexOf('www.') === -1){
                url = ConfigArquivoPadrao+url;
            }
            
            $.ajax({ type: tip, url: url, async: true,  dataType: 'json', data: params,/*complete: function () { 

            },*/success: function (data) {
                if (resposta === true) {
                    Cache_Gravar(url,data);
                    Modelo_Ajax_JsonTratar(url,data,historico);
                }
                // Agora Tira o CArregando
                if (SiteCarregando === true) {
                    Control_PopMgs_Carregando_Fechar();
                }
            }, error: function(req) {
                // Tira Carregando pra nao travar o Sistema:
                if (SiteCarregando === true) {
                    Control_PopMgs_Carregando_Fechar();
                }
                // Trata o Erro
                if(req.status === 200 && resposta===true){
                    // Caso Pagina Exista, mas JSON Esteja incorreto
                    Modelo_Ajax_Chamar('_Sistema/erro/Javascript','html='+req.responseText,'POST',false,false,false);
                }else{
                    // Página não existe
                }
            }});
        }
    };
    /**
    * Realiza Controle de link
    * @param {type} elemento
    * @param {type} funcao_valida
    * @param {type} valor
    * @returns {Boolean}
    * 
    * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
    */
    function Control_Layoult_Validar (elemento, funcao_valida, valor) {
        var validar = false;
        eval('validar = '+funcao_valida+'(valor)');
        if (validar === false) {
            elemento.addClass('obrigatoriomarcado').focus();
        }else{
            elemento.removeClass('obrigatoriomarcado');
        }
        return validar;
    };
    /**
     * 
     * @param {object} o
     * @param {string} f
     * @returns {void}
     * 
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Visual_Formulario_Mascara(o,f){
        var v_obj   =   o,
            v_fun   =   "Visual_Formulario_Mascara_"+f;
        setTimeout(function(){
            eval('v_obj.value = '+v_fun+'(v_obj.value);');
        },10);
    };
    /**
     * Só Numeros
     * @param {string} v
     * @returns {string}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Visual_Formulario_Mascara_Numero(v){
        return v.replace(/\D/g,"");
    }
    
    
    /**
     * FUNCOES DE RETORNO
     * 
     * @param {type} id
     * @returns {undefined}@author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Popup_Fechar (id) {
        var identificador = $(document.getElementById(id)).removeClass('in');
        window.setTimeout(function () {
            identificador.css('display','none');
        }, 500);
    };
    /**
     * ['Popup']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Popup (json) {
        var head        = '',
            body        = '',
            footer      = '',
            popup       = $(document.getElementById(json['id'])),
            popup2      = popup.children(".modal-dialog").children(".modal-content"),
            i           = 0,
            tam = Object.keys(json['botoes']).length;
        // Percorre Botoes e os fazem
        for(; i<tam; ++i){
            if (json['botoes'][i]['clique'] === '$( this ).dialog( "close" );') {
                footer += '<button class="btn" data-dismiss="modal" aria-hidden="true" onCLick="Sierra.Control_Ajax_Popup_Fechar(\''+json["id"]+'\');">'+json['botoes'][i]['text']+'</button>';
            }else{
                footer += '<button class="btn btn-primary" onClick="'+json['botoes'][i]['clique']+'">'+json['botoes'][i]['text']+'</button>';
            }
        }
        popup2.children(".modal-header").children("#popuptitulo").html(json['title']);
        popup2.children(".modal-body").html('<div class="row">'+json['html']+'</div>');
        popup2.children(".modal-footer").html(footer);
        popup.css('display','block').addClass('in');
    };
    /**
     * ['Conteudo']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Conteudo (json) {
        var cod         = '',
            script      = '';
    
        for (var i in json){
            if(json[i] !== undefined){
                $(json[i]['location']).html(json[i]['html']);
                script += json[i]['js'];
            }
        }
        if (script !== '') {
            $('body').append('<script type="text/javascript">'+script+'</script>');
        }
    };
    /**
     * ['Redirect']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Redirect (json) {
        var cod         = '',
            script      = '',
            url         = [];
    
        for (var i in json){
            if(json[i] !== undefined){
                url = json[i]['Url'];
                
                Modelo_Ajax_Chamar(url,'','get',true,true,true);
            }
        }
    };
    /**
     * ['Select']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Select (json) {
        var i           = 0,
            tam         = Object.keys(json).length,
            i2          = 0,
            tam2,
            identificador;
        for (var i in json){
            identificador = $(document.getElementById(json[i]['id']));
            tam2 = Object.keys(json[i]['valores']).length;
            identificador.find('option').remove();
            for(; i2<tam2; ++i2){
                identificador.append(
                    new Option(
                        json[i]['valores']['nome'], 
                        json[i]['valores']['valor'], 
                        true, 
                        true
                    )
                );
            }
        }
    };
    /**
     * ['JavascriptInterno']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Css (json) {
        var script      = '',
            cache = Cache_Ler('Dependencias_Css');
    
        if(cache===false){
            cache = new Array();
        }else{
            cache = cache.split('|');
        }
        for (var i in json){
            // VErifica se ja esta carregado
            if(!inArray(json[i],cache)){
                // Adiciona ao Cache
                cache.push(json[i]);
                
                if (script !== ''){
                    script += ',';
                }
                script += json[i]+'.css';
            }
        }
        if (script !== '') {
            // Salva Cache
            Sessao_Gravar('Dependencias_Css',cache.join('|'));
            
            $('head').append('<link href="'+ConfigArquivoPadrao+'web/min/?f='+script+'" rel="stylesheet" />');
        }
    };
    /**
     * ['JavascriptInterno']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_JavascriptInterno (json) {
        var script      = '';
        for (var i in json){
            script += json[i];
        }
        if (script !== '') {
            $('body').append('<script type="text/javascript">'+script+'</script>');
        }
    };
    /**
     * ['Javascript']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Javascript (json) {
        var script = '',
            cache = Cache_Ler('Dependencias_Js');
    
        if(cache===false){
            cache = new Array();
        }else{
            cache = cache.split('|');
        }
        
        for (var i in json){
            // VErifica se ja esta carregado
            if(!inArray(json[i],cache)){
                // Adiciona ao Cache
                cache.push(json[i]);
                
                if (script !== ''){
                    script += ',';
                }
                // ADiciona ao codigo
                script += json[i]+'.js';
            }
        }
        
        if(script!==''){
            // Salva Cache
            Sessao_Gravar('Dependencias_Js',cache.join('|'));

            $('head').append('<script type="text/javascript" src="'+ConfigArquivoPadrao+'web/min/?f='+script+'"></script>');
        }
        //eval(cod);
    };
    /**
     * ['Mensagens']
     * @param {type} json
     * @returns {undefined}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    function Control_Ajax_Mensagens (json) {
        var cod = '';
        for (var i in json){
            Control_PopMgs_Abrir(json[i]['tipo'],json[i]['mgs_principal'],json[i]['mgs_secundaria']);
        }
        return true;
    };
    
    /**
     * Funções a Retornar
     * METODOS PUBLICOS
     * 
     * @param {type} valor
     * @returns {unresolved}
     * @author Ricardo Rebello Sierra <contato@ricardosierra.com.br>
     */
    return {
        // Funções Usadas pelo Objeto que serão publicas
        Control_Form_Tratar                 : Control_Form_Tratar,
        Visual_Formulario_Mascara           : Visual_Formulario_Mascara,
    };
}());