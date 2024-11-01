<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if(!class_exists('WPTA_DB')){
    require_once( 'class-wpta-db.php' );
}

class WPTA_Table extends WP_List_Table {
    private $db;

    function __construct(){
        global $status, $page;
        $this->db = new WPTA_DB();
        parent::__construct( array(
            'singular'  => 'audience',
            'plural'    => 'audiences',
            'ajax'      => false,
            'screen'    => esc_html($_REQUEST['page'])
        ) );
    }
    
    function column_default($item, $column_name){
        return esc_html($item[$column_name]);
    }

    function column_id($item){
        return '?utm_content='.esc_html($item['id']);
    }
    function column_name($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&audience=%s">'.__("Edit", "targetaudience").'</a>', esc_html($_REQUEST['page']),'edit', esc_html($item['id'])),
        );

        return sprintf('%1$s %2$s', '<p class="description">'.$item['name'].'</p><textarea readonly="readonly" rows="2" class="wpta-shortcode widefat">[target_audience default="'.esc_html($item['name']).'"]</textarea>', $this->row_actions($actions));
    }
    function column_alternative_1($item){
        return '<p class="description">'.$item['alternative_1'].'</p><textarea readonly="readonly" rows="2" class="wpta-shortcode widefat">[target_audience default="'.esc_html($item['alternative_1']).'" alternative="1"]</textarea>';
    }
    function column_alternative_2($item){
        return '<p class="description">'.$item['alternative_2'].'</p><textarea readonly="readonly" rows="2" class="wpta-shortcode widefat">[target_audience default="'.esc_html($item['alternative_2']).'" alternative="2"]</textarea>';
    }

    function column_cb($item){
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], esc_html($item['id']));
    }
    
    function get_columns(){
        $columns = array(
            'cb'    => '<input type="checkbox" />',
            'name'  => __("Name", "targetaudience"),
            'alternative_1'  => __("Alternative 1", "targetaudience"),
            'alternative_2'  => __("Alternative 2", "targetaudience"),
            'id'  => __("Identifier", "targetaudience"),
        );
        return $columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => __("Delete", "targetaudience")
        );
        return $actions;
    }

    function process_bulk_action() {
        global $wpdb;

        if( 'delete'===$this->current_action() && isset($_GET['audience']) && is_array($_GET['audience']) && count($_GET['audience'])) {
            $this->db->delete_multiple(sanitize_text_field(join(',', $_GET['audience'])));
        }
    }
    
    function prepare_items() {
        $this->process_bulk_action();

        $per_page               = 20;
        $hidden                 = array();
        $orderby                = (!empty($_GET['orderby'])) ? sanitize_sql_orderby($_GET['orderby']) : 'id';
        $order                  = (!empty($_GET['order'])) ? sanitize_sql_orderby($_GET['order']) : 'desc';
        $columns                = $this->get_columns();
        $sortable               = $this->get_sortable_columns();
        $curr_page              = $this->get_pagenum();
        $total_items            = $this->db->get_total_count();
        $data                   = $this->db->get_all($curr_page, $per_page, $orderby, $order);
        $this->items            = $data;
        $this->_column_headers  = array($columns, $hidden, $sortable);
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
    
}