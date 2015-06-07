<?php
define('SISTEMA_LINGUAGEM_PADRAO', 'pt_BR');
define('SISTEMA_SERVIDOR', $_SERVER['SERVER_NAME']);
define('SISTEMA_ENDERECO', $_SERVER['REQUEST_URI']);

define('DS', DIRECTORY_SEPARATOR);
define('US', '/'); // Divisor de URL
define('ROOT'    , realpath(dirname(__FILE__)). DS);
define('DIR_CLASSES'    , ROOT.'Classes'. DS);
define('LIB_PATH',         ROOT.'libs'.DS);
define('LANG_PATH',         ROOT.'i18n'.DS);

require DIR_CLASSES.'Funcao.php';
require DIR_CLASSES.'Visual.php';

if(isset($_POST['tamanho']) && isset($_POST['forca'])){
    $tamanho = (int) $_POST['tamanho'];
    $forca = (int) $_POST['forca'];
    $senha = \Classes\Funcao::Gerar_Senha($tamanho, $forca);
    $Visual = new \Classes\Visual();
    
    $conteudo = array(
        'location' => '#senha_gerada',
        'js' => '',
        'html' =>  $senha
    );
    $Visual->Json_IncluiTipo('Conteudo',$conteudo);
    $mensagem = array(
        'tipo' => 'sucesso',
        'mgs_principal' => 'Gerado com Sucesso',
        'mgs_secundaria' =>  'Sua senha é: '.$senha
    );
    $Visual->Json_IncluiTipo('Mensagens',$mensagem);
    
    echo $Visual->Json_Retorna();
    exit;
}


/**
 * Carrega Funções de Internaciolização
 */
$textdomain = "Framework";
if (isset($_GET['locale']) && !empty($_GET['locale'])){
    define('SISTEMA_LINGUAGEM', \Classes\Funcao::anti_injection($_GET['locale']));
}else{
    define('SISTEMA_LINGUAGEM', SISTEMA_LINGUAGEM_PADRAO);
}
putenv('LANGUAGE=' . SISTEMA_LINGUAGEM);
putenv('LANG=' . SISTEMA_LINGUAGEM);
putenv('LC_ALL=' . SISTEMA_LINGUAGEM);
putenv('LC_MESSAGES=' . SISTEMA_LINGUAGEM);
require_once(LIB_PATH.'i18n'.DS.'gettext.inc');
_setlocale(LC_ALL, SISTEMA_LINGUAGEM);
_setlocale(LC_CTYPE, SISTEMA_LINGUAGEM);
_bindtextdomain($textdomain, LANG_PATH);
_bind_textdomain_codeset($textdomain, 'UTF-8');
_textdomain($textdomain);
function _e($string) {
  echo __($string);
}

if(stripos(SISTEMA_ENDERECO, '?')===false){
    define('SISTEMA_URL_ATUAL', "http://" . SISTEMA_SERVIDOR . SISTEMA_ENDERECO.'?locale='.SISTEMA_LINGUAGEM_PADRAO);
}else{
    define('SISTEMA_URL_ATUAL', "http://" . SISTEMA_SERVIDOR . SISTEMA_ENDERECO);
}










// Twitter
$linguagem_quebrada = explode('_',SISTEMA_LINGUAGEM_PADRAO);
$compartilhar = Array();
$compartilhar[] =  '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.SISTEMA_URL_ATUAL.'" data-lang="'.$linguagem_quebrada[0].'">Tweetar</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
// Facebook
$compartilhar[] .= '<iframe src="//www.facebook.com/plugins/like.php?href='.urlencode(SISTEMA_URL_ATUAL).'&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=285497731507842" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>';
// Google Plus
$compartilhar[] .= '<g:plusone size="small"></g:plusone>
<script type="text/javascript">
  window.___gcfg = {lang: \''.str_replace("_", "-", SISTEMA_LINGUAGEM).'\'};

  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';

?>


<!DOCTYPE html>
<html lang="<?php echo $linguagem_quebrada[0]; ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php _e('Gerador de Senha'); ?></title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="assets/toastr/toastr.min.css">
    <link rel="stylesheet" href="assets/nprogress/nprogress.css">

    <!-- Optional theme -->
    <style>
      body {
        padding-top: 50px;
      }
      .starter-template {
        padding: 40px 15px;
        text-align: center;
      }
      .col-centered{
        padding: 0 auto;
        text-align: center;
      }
    </style>
    <script type="text/javascript">
    ConfigArquivoPadrao = '<?php echo $_SERVER["HTTP_REFERER"];?>';
    </script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?php _e('Gerador de Senha'); ?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#"><?php _e('Gerar Senha'); ?></a></li>
            <li><a href="http://ricardosierra.com.br" target="_BLANK"><?php _e('Site'); ?></a></li>
            <li><a href="http://ricardosierra.com.br/blog" target="_BLANK"><?php _e('Blog'); ?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container-fluid">

        <div class="starter-template">
          <h1></h1>
          <p class="lead"><?php _e('Escolha o Tamanho da sua Senha e sua Força.'); ?><br><?php _e('A força vai de 0 (Somente Números), até 10 (Todos os Caracteres).'); ?></p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form method="GET" action="index.php" class="">
                  <div class="form-group">
                    <label for="tamanho"><?php _e('Tamanho da Senha (Vai de 4 até 32)'); ?></label>
                    <input type="number" max="32" min="4" value="8" class="form-control" name="tamanho" id="tamanho" placeholder="<?php _e('Escolha o Tamanho da Senha'); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="forca"><?php _e('Força da Senha (Vai de 0 até 10)'); ?></label>
                    <input type="number" max="10" min="0" value="6" class="form-control" name="forca" id="forca" placeholder="<?php _e('Escolha uma Força para a senha de 0 a 10'); ?>" required>
                  </div>
                  <button type="submit" class="btn btn-default">Gerar Senha</button>
                </form>
            </div>
            <div class="col-md-6">
                <?php _e('A Senha Gerada é:'); ?><span id="senha_gerada"></span>
                
            </div>
        </div>
    </div><!-- /.container -->
    <footer> <!-- Aqui e a area do footer -->
	<div class="container-fluid">
            <div class="row">
                <div id="linksImportantes" class="col-md-3 col-centered">
                    <!--<a href="http://www.ricardosierra.com.br">Site</a><br>
                    <a href="http://www.ricardosierra.com.br/blog">Blog</a><br>
                    <a href="http://www.sierratecnologia.com.br">SierraTecnologia</a>-->
                </div> <!-- Aqui e a area dos links importantes -->
                <div id="redesSociais" class="col-md-3  col-centered">
                    <?php 
                    foreach($compartilhar as $valor){
                        echo $valor; 
                    }
                    ?>
                </div> <!-- Aqui e a area das redes sociais -->
                <div id="logoFooter" class="col-md-offset-3 col-md-3  col-centered">
                    Feito por <a href="http://www.sierratecnologia.com.br" target="_BLANK">SierraTecnologia</a>
                </div> <!-- Aqui e a area da logo do rodape -->
            </div>
	</div>
    </footer>

    <div class="push"></div><div id="escondido"></div><div class="growlUI" style="display:none;"><h1>SierraTecnologia</h1> <h2>Ricardo Sierra <contato@ricardosierra.com.br></h2></div><div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="popup" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="popuptitulo">Popup</h4></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="assets/toastr/toastr.min.js"></script>
    <script src="assets/nprogress/nprogress.js"></script>
    <script src="assets/Sistema.js"></script>
  </body>
</html>
