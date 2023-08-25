<?php
class ITPluginAPI {
    const ITRestRoot = 'asp';
    const ITVer = 'v2';
    const ITNameSpace = self::ITRestRoot . '/' . self::ITVer;
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    public function register_routes() {
        register_rest_route(self::ITNameSpace, '/listplugins', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'list_plugins'),
        ));
        
        register_rest_route(self::ITNameSpace, '/listplugins/activate', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'activate_plugin'),
        ));
        
        register_rest_route(self::ITNameSpace, '/listplugins/deactivate', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'deactivate_plugin'),
        ));
    }
    
    public function set_response($statusCode, $status, $msg, $data, $resstatus = false) {
        $itresponse = array(
            'statusCode' => $statusCode,
            'status' => $status,
            'message' => $msg,
            'data' => $data,
        );
        
        if ($resstatus) {
            $response = $itresponse;
        } else {
            $response = new WP_REST_Response($itresponse);
            $response->set_status($statusCode);
        }
        return $response;
    }
    
    public function list_plugins($request) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        $plugins = get_plugins();
        $plugin_names = array();
        
        foreach ($plugins as $plugin_file => $plugin_info) {
            if($plugin_info['Name'] != 'Woocommerce Addon: Custom Coupon'){
                $plugin_names[] = $plugin_info['Name'];
            }
        }
        return $this->set_response('200', true, 'Success', $plugin_names);
    }
    
    public function get_plugin_filename_by_name($plugin_name) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        $plugins = get_plugins();
        
        foreach ($plugins as $plugin_file => $plugin_info) {
            if ($plugin_info['Name'] === $plugin_name) {
                return $plugin_file;
            }
        }
        
        return null;
    }
    
    public function deactivate_plugin($request) {
        $plugin_name = empty($request->get_param('plugin')) ? '' : $request->get_param('plugin');
        
        if ($plugin_name == '') {
            return $this->set_response('404', false, 'Please provide valid plugin name', array());
        }
        $plugin_file = $this->get_plugin_filename_by_name($plugin_name);
        deactivate_plugins($plugin_file);
        
        return $this->set_response('200', true, 'Plugin has been deactivated', array());
    }
    
    public function activate_plugin($request) {
        $plugin_name = empty($request->get_param('plugin')) ? '' : $request->get_param('plugin');
        
        if ($plugin_name == '') {
            return $this->set_response('404', false, 'Please provide valid plugin name', array());
        }
        $plugin_file = $this->get_plugin_filename_by_name($plugin_name);
        activate_plugin($plugin_file);
        
        return $this->set_response('200', true, 'Plugin has been activated', array());
    }
}
