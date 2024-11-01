<?php
class WPTA_SHORTCODE {

    private $id;

	function __construct() {
        $this->id = intval(sanitize_text_field($_GET["utm_content"]));

        if(!empty($this->id)) {
            setcookie('wpta_utm_content', $this->id, strtotime('+6 months', time()),'/');
        }

		add_shortcode('target_audience', array($this, 'target_audience_shortcode'));
	}

	function target_audience_shortcode($atts){
		extract(shortcode_atts(array(
    		'default' => NULL,
            'alternative' => NULL,
            'prefix' => NULL,
            'suffix' => NULL
        ), $atts));

		if(empty($this->id)) {
            $this->id = intval(sanitize_text_field($_COOKIE['wpta_utm_content']));
        }
        
        if(!empty($this->id)) {
			$db 	= new WPTA_DB();
			$audience 	= $db->get($this->id);

			if($alternative != NULL) {
                if(!empty($audience['alternative_1']) && $alternative == '1') {
                    return esc_html($prefix.$audience['alternative_1'].$suffix);
                }
                if(!empty($audience['alternative_2']) && $alternative == '2') {
                    return esc_html($prefix.$audience['alternative_2'].$suffix);
                }
            } else {
                if(!empty($audience['name'])) {
                    return esc_html($prefix.$audience['name'].$suffix);
                }
            }

        }
        return esc_html($default);
	}
}