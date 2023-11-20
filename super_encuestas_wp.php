<?php
/*
Plugin Name: Super Encuestas WP
Description: Con ésta util herramienta para WordPress, conocido como "Super Encuestas WP", lograrás realizar encuestas de forma rápida y sencilla.
Version: 1.0.0
Author: Johan Román
Author URI: https://rojo95.github.io/portfolio/
License: GPLv2 or later
Tags: encuestas,consultas,preguntas,respuestas
*/

/**
 * activation function and actions
 */
function SEWPActivar()
{
    global $wpdb;
    $table_survey = $wpdb->prefix . 'sewp_survey';
    $sql = "CREATE TABLE IF NOT EXISTS {$table_survey} (
        survey_id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(45) NOT NULL UNIQUE,
        short_code VARCHAR(45) NOT NULL UNIQUE,
        status BOOLEAN DEFAULT TRUE,
        PRIMARY KEY (survey_id)
    );";
    $wpdb->query($sql);

    $table_questions = $wpdb->prefix . 'sewp_questions';
    $sql2 = "CREATE TABLE IF NOT EXISTS {$table_questions} (
        question_id INT NOT NULL AUTO_INCREMENT,
        survey_id INT NOT NULL, 
        ask VARCHAR(150) NOT NULL,
        type_ask VARCHAR(45) NOT NULL,
        PRIMARY KEY(question_id),
        FOREIGN KEY (survey_id) REFERENCES {$wpdb->prefix}sewp_survey(survey_id)
    );";
    $wpdb->query($sql2);

    $table_answers = $wpdb->prefix . 'sewp_answers';
    $sql3 = "CREATE TABLE IF NOT EXISTS {$table_answers} (
        answer_id INT NOT NULL AUTO_INCREMENT,
        question_id INT NOT NULL, 
        answer VARCHAR(45) NOT NULL,
        PRIMARY KEY(answer_id),
        FOREIGN KEY (question_id) REFERENCES {$wpdb->prefix}sewp_questions(question_id)
    );";
    $wpdb->query($sql3);
}

/**
 * deactivation function and actions
 */
function SEWPDesactivar()
{

}

register_activation_hook(__FILE__, 'SEWPActivar');
register_deactivation_hook(__FILE__, 'SEWPDesactivar');

add_action('admin_menu', 'SEWPCrearMenu');
function SEWPCrearMenu()
{
    // menú del plugin
    add_menu_page(
        'Super Encuestas WP', // Título de lapágina
        'Super Encuestas WP', // Título del Menú
        'manage_options', // usuario que tenrá permiso de acceso a ésto (Administrador) 
        plugin_dir_path(__FILE__) . 'admin/views/encuestas.php', // 'scapi_menu', // slug de acceso
        null, // 'MenuContent', // función para llamar el contenido de ésta página de menú
        plugin_dir_url(__FILE__) . 'admin/img/icon.png', // icono del elemento del menú
        '1' // posición del menú
    );

}

// encolar bootstrap
function SEWPEncolarBootstrapJs($hook)
{
    if ($hook != "super_encuestas_wp/admin/views/encuestas.php") {
        return;
    }
    wp_enqueue_script('SEWPBootstrapJs', plugins_url('admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'SEWPEncolarBootstrapJs');

function SEWPEncolarBootstrapCss($hook)
{
    if ($hook != "super_encuestas_wp/admin/views/encuestas.php") {
        return;
    }
    wp_enqueue_style('SEWPBootstrapCss', plugins_url('admin/bootstrap/css/bootstrap.min.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'SEWPEncolarBootstrapCss');

// encolar js propio 
function SEWPEncolarJs($hook)
{
    if ($hook != "super_encuestas_wp/admin/views/encuestas.php") {
        return;
    }
    wp_enqueue_script('SEWPJs', plugins_url('admin/js/super_encuestas_wp.js', __FILE__), array('jquery'));
    wp_localize_script('SEWPJs', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'SEWPEncolarJs');

function EliminarEncuesta()
{
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die('no tiene permisos para ejecutar ese ajax');
    }

    $id = $_POST['id'];
    global $wpdb;
    $tabla = $wpdb->prefix . 'sewp_survey';
    $query = "SELECT status FROM {$tabla} WHERE survey_id=\"{$id}\";";
    $results = $wpdb->get_results($query, ARRAY_A);
    $wpdb->update($tabla, ['status' => !$results[0]['status']], array('survey_id' => $id));
    return true;
}

add_action('wp_ajax_deleteSurvey', 'EliminarEncuesta');