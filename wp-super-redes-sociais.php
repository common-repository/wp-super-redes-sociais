<?php

/*
  Plugin Name: WP Super Redes Sociais
  Plugin URI: http://www.williamokano.com/plugins/
  Description: Plugin que exibe os principais share buttons das redes sociais
  Author: William Okano <williamokano@gmail.com>, Claudney S. Reis <claudsan@gmail.com>
  Author URI: http://www.williamokano.com/
  Version: 1.2.1
 */

class WpSuperRedesSociais {

    var $plugin_slug = "CHW_Plugins";

    public function ativacao() {
        update_option("chw_srs_plugin", array(
            "chk_twitter" => "on",
            "chk_facebook" => "on",
            "chk_googleplus" => "on",
            "chk_googlebuzz" => "on"
        ));
    }

    public function geraHtmlRedes() {
		if(is_admin())
			return;
        $the_xort_url = $this->getTheXortUsUrl(get_permalink());
        echo "<div id='super-redes-sociais'>";

        $opcoes = $this->recuperaOpcoes();

        /* --- TWITTER --- */
        if (array_key_exists("chk_twitter", $opcoes)) {
            echo '<div class="srs-twitter">';
            echo '<a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $the_xort_url . '" data-text="' . get_the_title() . '" data-count="horizontal" data-via="' . $opcoes["user_twitter"] . '" data-lang="pt">Tweetar</a>';
            echo '</div>';
        }
        /* --- TWITTER --- */

        /* --- FACEBOOK --- */
        if (array_key_exists("chk_facebook", $opcoes)) {
            echo '<div class="srs-facebook">';
            echo '<div id="fb-root"></div><script src="http://connect.facebook.net/' . $opcoes["idioma_facebook"] . '/all.js#appId=182405871826949&amp;xfbml=1"></script><fb:like href="' . $the_xort_url . '" send="false" layout="button_count" width="100" show_faces="true" action="like" font=""></fb:like>';
            echo '</div>';
        }
        /* --- FACEBOOK --- */

        /* --- GOOGLEPLUS --- */
        if (array_key_exists("chk_googleplus", $opcoes)) {
            echo '<div class="srs-gplusone">';
            echo '<g:plusone size="standard" count="true" href="' . $the_xort_url . '"></g:plusone>';
            echo '</div>';
        }
        /* --- GOOGLEPLUS --- */

        /* --- GOOGLE BUZZ --- */
        if (array_key_exists("chk_googlebuzz", $opcoes)) {
            echo '<div class="srs-googlebuzz">';
            echo '<a title="Postar no Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="small-button" data-locale="pt_BR" data-url="' . $the_xort_url . '" data-imageurl="' . $opcoes["buzzimg"] . '"></a>';
            echo '</div>';
        }
        /* --- GOOGLE BUZZ --- */

        echo "</div>";
    }

    public function addCss() {
        /* --- CSS --- */
        echo '<link href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-redes-sociais/srscss.css" rel="stylesheet" type="text/css" />';
        /* --- CSS --- */
    }

    public function addScripts() {
        echo "<!-- CHW PLUGINS -->";

        /* --- TWITTER --- */
        echo '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
        /* --- TWITTER --- */

        /* --- FACEBOOK --- */
        echo '<meta property="og:image" content="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-redes-sociais/mythumb.gif"/>';
        /* --- FACEBOOK --- */

        /* --- GPLUSONE --- */
        echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
        /* --- GPLUSONE --- */

        /* --- GOOGLE BUZZ --- */
        echo '<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>';
        /* --- GOOGLE BUZZ --- */

        echo "<!-- CHW PLUGINS -->";
    }

    public function getTheXortUsUrl($the_permalink = null) {
        if (!function_exists("curl_init")) {
            die("Seu servidor deve ter suporte a curl para utilizar este plugin.");
        } else {
            if (!function_exists("preg_match")) {
                die("Seu servidor não tem suporte a expressões regulares.");
            } else {
                if (preg_match("//i", $the_permalink)) {
                    // create a new cURL resource
                    $ch = curl_init();

                    // set URL and other appropriate options
                    curl_setopt($ch, CURLOPT_URL, "http://migre.me/api.txt?url=" . $the_permalink);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    // grab URL and pass it to the browser
                    $url_encurtada = curl_exec($ch);

                    // close cURL resource, and free up system resources
                    curl_close($ch);
                    return $url_encurtada;
                }
            }
        }
    }

    public function recuperaOpcoes() {
        $options = get_option("chw_srs_plugin");
        if (!is_array($options)) {
            $options = (array) array();
        }
        return $options;
    }

    public function menuPrincipal() {
        if (getenv("REQUEST_METHOD") == "POST") {

            /* --- GRAVO O CSS EM ARQUIVO --- */
            file_put_contents(realpath(dirname(__FILE__)) . "/srscss.css", $_POST["srscss"]);
            unset($_POST["srscss"]);

            /* --- ATUALIZO AS OPÇÕES NO BANCO DE DADOS --- */
            update_option("chw_srs_plugin", $_POST);
        }
        $options = $this->recuperaOpcoes();
		
        define("DS", DIRECTORY_SEPARATOR);
        define("PLUGIN_ROOT", realpath(dirname(__FILE__)) . DS);

        $template = file_get_contents(PLUGIN_ROOT . "templates/configuracao.html");
        $template = str_replace(array("%%SITEURL%%", "%%PLUGIN_SLUG%%"), array(get_bloginfo("siteurl"), $this->plugin_slug), $template);

        /* --- TWITTER --- */
        if (array_key_exists("chk_twitter", $options)) {
            $template = str_replace('checked="%%TWITTER%%"', 'checked="checked"', $template);
        } else {
            $template = str_replace('checked="%%TWITTER%%"', "", $template);
        }
        $template = str_replace("%%USER_TWITTER%%", $options["user_twitter"], $template);
        /* --- TWITTER --- */

        /* --- FACEBOOK --- */
        if (array_key_exists("chk_facebook", $options)) {
            $template = str_replace('checked="%%FACEBOOK%%"', 'checked="checked"', $template);
        } else {
            $template = str_replace('checked="%%FACEBOOK%%"', "", $template);
        }

		//Listo os idiomas disponíveis
		$idiomas = (array) array();
		$idiomas[] = array("slug" => "pt_BR", "idioma" => "Português Brasil");
		$idiomas[] = array("slug" => "en_US", "idioma" => "English (United States)");
		$idiomas[] = array("slug" => "es_ES", "idioma" => "Español");
		$idiomas[] = array("slug" => "fr_FR", "idioma" => "Française");
		$optionsReplace = "";
		foreach($idiomas as $idioma){
			$selected = "";
			if($idioma["slug"] == $options["idioma_facebook"])
				$selected = "selected='selected' ";
			$optionsReplace .= "<option {$selected}value='{$idioma["slug"]}'>{$idioma["idioma"]}</option>";
		}
		$template = str_replace("%%IDIOMAS_FACE%%", $optionsReplace, $template);
		
        /* --- FACEBOOK --- */
		
        /* --- GOOGLE PLUS --- */
        if (array_key_exists("chk_googleplus", $options)) {
            $template = str_replace('checked="%%GOOGLEPLUS%%"', 'checked="checked"', $template);
        } else {
            $template = str_replace('checked="%%GOOGLEPLUS%%"', "", $template);
        }
        /* --- GOOGLE PLUS --- */

        /* --- GOOGLE BUZZ --- */
        if (array_key_exists("chk_googlebuzz", $options)) {
            $template = str_replace('checked="%%GOOGLEBUZZ%%"', 'checked="checked"', $template);
        } else {
            $template = str_replace('checked="%%GOOGLEBUZZ%%"', "", $template);
        }
        /* --- GOOGLE BUZZ --- */

        /* --- SUBSTITUI A ÁREA DO CSS --- */
        $srscss = file_get_contents(PLUGIN_ROOT . "srscss.css");
        $template = str_replace("%%SRSCSS%%", $srscss, $template);

        echo $template;
    }

    public function adicionarMenus() {
        $icon_url = get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-redes-sociais/imagens/tesoura.png';
        add_menu_page('Configuração das Redes Sociais', 'CHW Plugins', 'read', $this->plugin_slug, array($this, "menuPrincipal"), $icon_url);
    }

}

$wpsuperredessociais = new WpSuperRedesSociais();
register_activation_hook(__FILE__, array($wpsuperredessociais, 'ativacao'));
add_action("wp_head", array($wpsuperredessociais, "addCss"));
add_action("wp_head", array($wpsuperredessociais, "addScripts"));
add_action("the_post", array($wpsuperredessociais, "geraHtmlRedes"));
add_action("admin_menu", array($wpsuperredessociais, "adicionarMenus"));
?>
