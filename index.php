<?php
/*
Plugin Name: Testemonial carousel
Plugin URI: https://github.com/blocus/testemonial-carousel-wordpress
Description: Testemonial carousel for wordpress
Author: Ahmed Meftah
Author URI: https://ahmedmeftah.com
Version: 0.1
*/

$table_name = $wpdb->prefix . 'mef_carousel';
$table_items_name = $wpdb->prefix . 'mef_carousel_items';


register_activation_hook( __FILE__, 'mef_carousel_create_plugin_database_table' );
register_uninstall_hook( __FILE__, 'mef_carousel_delete_plugin_database_table');
add_action("admin_menu", "addMenu");

function addMenu(){
  add_menu_page("Testemonial Carousel", "Testemonial Carousel", 4, "meftah-testemonial-carousel", "testemonialCarouselAdmin" ,"");
}

function testemonialCarouselAdminListHeader() {
    echo "<tr><th>Title</th><th>Short code</th><th width='50'>Delete</th></tr>";
}


function testemonialCarouselAdminListItem($item) {
    ?>
    <tr>
        <td><strong><a class="row-title" href="/wp-admin/admin.php?page=meftah-testemonial-carousel&id=<?= $item->id ?>"aria-label="“<?= $item->title ?>” (Edit)"><?= $item->title ?></a></strong></td>
        <td>[mef-carousel id="<?= $item->id ?>"]</td>
        <td>
            <form method="POST">
                <input type="hidden" name="action" value="DELETE_CAROUSEL" />
                <input type="hidden" name="id" value="<?= $item->id ?>" />
                <button type="submit" style="color:#d63638; border: 1px solid #d63638; background: #fff;" class="submitdelete deletion">
                    Delete
                </button>
            </form>    
        </td>
    </tr>
    <?php
    }


    function testemonialCarouselAdminList(){
    global $wpdb;
    global $table_name;
	global $table_items_name;

    if(isset($_POST['action'])){
        if ($_POST['action'] == "ADD_NEW_CAROUSEL" && !empty($_POST['title'])){
            $wpdb->insert( $table_name, ['title' => $_POST['title']] );
        } elseif ($_POST['action'] == "DELETE_CAROUSEL" ){
            $wpdb->delete($table_items_name, ['parent_id' => $_POST['id']]);
            $wpdb->delete($table_name, ['id' => $_POST['id']]);
            ?>
            <div class="notice notice-success">
                <p>Item has been deleted</p>            
            </div>
            <?php
        }
    }

    $results = $wpdb->get_results( "SELECT * FROM {$table_name}", OBJECT );
    ?>

    <form method="POST">
        <input type="hidden"  name="action" value="ADD_NEW_CAROUSEL">
        <input type="text" required  name="title" value="<?= $carousel->title ?>">
        <input type="submit" class="button" value="Add new item">
    </form>

    <h2>List of Testemonial Carousel </h2>
  
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
        <?php testemonialCarouselAdminListHeader() ?>
        </thead>
        <tbody>
            <?php 
            if($results){
                foreach ($results as $item) {
                    testemonialCarouselAdminListItem($item);
                }
            }
            ?>
        </tbody>
        <tfoot>
          <?php testemonialCarouselAdminListHeader() ?>
        </tfoot>
    </table>



    <?php
}


function testemonialCarouselAdminEditCarouselItem($item) {
    ?>

    <tr>
        <form method="POST">
            <input type="hidden" name="action" value="EDIT_ITEM">
            <input type="hidden" name="item_id" value="<?= $item->id ?>">
            <td><img width="50" height="50" src="<?= $item->author_image ?>"></td>
            <td><input type="text" name="author_image" value="<?= $item->author_image ?>"></td>
            <td><input type="text" name="author_name" value="<?= $item->author_name ?>"></td>
            <td><input type="text" name="author_description" value="<?= $item->author_description ?>"></td>
            <td><textarea name="author_quote"><?= $item->author_quote ?></textarea></td>
            <td>
                <input type="submit" class="button" value="update item">
                <a href="/wp-admin/admin.php?page=meftah-testemonial-carousel&id=1&deleteitem=<?= $item->id ?>" style="color:#d63638" class="submitdelete">
                    Delete item
                </a>
            </td>
        </form>
    </tr>
    <?php
}


function testemonialCarouselAdminAddCarouselItem() {
    ?>
    
    <tr>
        <form method="POST">
            <input type="hidden" name="action" value="INSERT_ITEM">
            <td>add new item</td>
            <td><input type="text" name="author_image" value="<?= $item->author_image ?>"></td>
            <td><input type="text" name="author_name" value="<?= $item->author_name ?>"></td>
            <td><input type="text" name="author_description" value="<?= $item->author_description ?>"></td>
            <td><textarea name="author_quote"><?= $item->author_quote ?></textarea></td>
            <td>
                <input type="submit" class="button" value="add item">
            </td>
        </form>
    </tr>
    <?php
 
}

function testemonialCarouselAdminEditHeader(){
    echo "<tr>
        <th>apercu</th>
        <th>image</th>
        <th>title</th>
        <th>descrition</th>
        <th>quote</th>
        <th></th>
    </tr>";
}


function testemonialCarouselAdminEditCarousel(){
    global $wpdb;
    global $table_name;
	global $table_items_name;

    $id = $_GET['id'];
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        if ($action == "UPDATE_TITLE"){
            $data = ["title" =>$_POST['title'] ];
            $where = ["id" => $id];
            $wpdb->update( $table_name, $data, $where);
            echo "Updating title -> " . $_POST['title'];
        } elseif ($action == "INSERT_ITEM"){
            $data = [
                "parent_id" => $id ,
                "author_image" =>$_POST['author_image'] ,
                "author_name" =>$_POST['author_name'] ,
                "author_description" =>$_POST['author_description'] ,
                "author_quote" =>$_POST['author_quote'] ,
            ];

            $wpdb->insert( $table_items_name, $data );

            ?>
            <div class="notice notice-success">
                <p>Item has been created</p>            
            </div>
            <?php
        } elseif ($action == "EDIT_ITEM"){
            $data = [
                "author_image" =>$_POST['author_image'] ,
                "author_name" =>$_POST['author_name'] ,
                "author_description" =>$_POST['author_description'] ,
                "author_quote" =>$_POST['author_quote'] ,
            ];
            $where = ["id" => $_POST['item_id'] ];
            $wpdb->update( $table_items_name, $data, $where);

            ?>
            <div class="notice notice-success">
                <p>Item has been updated</p>            
            </div>
                        
            <?php
        } elseif ($action == "DELETE_ITEM"){
            $where = ["id" => $_POST['item_id'] ];
            $wpdb->delete( $table_items_name, $where);
            ?>
            <div class="notice notice-success">
                <p>Item has been deleted</p>            
            </div>
            <?php
        }
    }

    if(isset($_GET['deleteitem'])){
        $toDelete = $wpdb->get_results( "SELECT * FROM {$table_items_name} WHERE id = {$_GET['deleteitem']}", OBJECT );
        
        if($toDelete){
            if(isset($_POST['confirm'])){
                $wpdb->delete( $table_items_name, ["id" => $toDelete[0]->id ]);
                ?>
                <div class="notice notice-success astra-sites-must-notices astra-sites-file-permission-issue">
                    <p>Item has been deleted</p>
                </div>
                <?php
            }else {

            ?>
            <div class="card">
                <img width="100%" src="<?= $toDelete[0]->author_image ?>">
                <p><strong>Title :</strong><?= $toDelete[0]->author_name ?></p>
                <p><strong>Description :</strong><?= $toDelete[0]->author_description ?></p>
                <p><strong>Quote :</strong><?= $toDelete[0]->author_quote ?></p>
                <form method="POST">
                    <button type="submit" name="confirm" style="color:#d63638; border: 1px solid #d63638; background: #fff;" class="submitdelete deletion">Confirmer</button>
                <form>
                <a href="/wp-admin/admin.php?page=meftah-testemonial-carousel&id=<?= $id ?>">Cancel</a>
            </div>
            <?php
            }
            
        }
    }

    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE id = $id", OBJECT );
    $items = $wpdb->get_results( "SELECT * FROM {$table_items_name} WHERE parent_id = $id", OBJECT );

    echo "<h2>Edit Item</h2>";

    if($results){
        $carousel = $results[0];

        ?>
        <form action="#" method="POST">
            <input type="hidden"  name="action" value="UPDATE_TITLE">
            <input type="text"  name="title" value="<?= $carousel->title ?>">
            <input type="submit" class="button" value="update title">
        </form>


        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
            <?php testemonialCarouselAdminEditHeader() ?>
            </thead>
            <tbody>
                <?php 
                foreach ($items as $item) {
                    testemonialCarouselAdminEditCarouselItem($item);
                }
                testemonialCarouselAdminAddCarouselItem();
                ?>
            </tbody>
            <tfoot>
            <?php testemonialCarouselAdminEditHeader() ?>
            </tfoot>
        </table>
        
        <?php
       
    }else {
        echo "Element not found try egain ?<a href='/wp-admin/admin.php?page=meftah-testemonial-carousel'>Back</a>";
    }
}



function testemonialCarouselAdmin() {
    ?>
        <div class="wrap">
        <h1 class="wp-heading-inline">Testemonial Carousel</h1>

        <a
            href="/wp-admin/admin.php?page=meftah-testemonial-carousel&action=create"
            class="page-title-action"
            >Add New</a
        >
        <hr class="wp-header-end" />
        
            <?php
                if (isset($_GET['id'])) {
                    testemonialCarouselAdminEditCarousel();
                }else {
                    testemonialCarouselAdminList();
                }

            ?>
            
            
        </div>
    <?php
}

function mef_carousel( $atts ) {
    global $wpdb;
    global $table_name;
	global $table_items_name;

	$atts = shortcode_atts(array('id' => '1'),$atts);
    $id =  $atts['id'];

    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE id = $id", OBJECT );
    $items = $wpdb->get_results( "SELECT * FROM {$table_items_name} WHERE parent_id = $id", OBJECT );

    if($results){
        echo  "<h2 style='text-align: center' class='elementor-image-box-title'>{$results[0]->title}</h2>";
        echo "<div class='mef-carousel'>";
        foreach ( $items as $key => $item) {
            $mainClass = "mef-carousel-item";
            if($key == "0"){
                $mainClass .= " active";
            }

            echo "<div class='$mainClass'>";
            echo "<img src='{$item->author_image}'>";
            echo "<q class='mef-carousel-item-quote'>";
                echo '<i aria-hidden="true" class="fas fa-quote-left"></i>';
                echo "<div class='mef-carousel-item-quote-author'>{$item->author_name}</div>";
                echo "<div class='mef-carousel-item-quote-description'>{$item->author_description}</div>";
                echo "<div class='mef-carousel-item-quote-message'>{$item->author_quote}</div>";
            echo "</q>";
            echo "</div>";
        }
        echo "</div>";
    }else{
        echo  "";
    }
}

add_shortcode( 'mef-carousel', 'mef_carousel' );



function mef_carousel_create_plugin_database_table() {
	global $wpdb;
	// global $table_name;
	// global $table_items_name;services

    $table_name = $wpdb->prefix . 'mef_carousel';
    $table_items_name = $wpdb->prefix . 'mef_carousel_items';
    
	$sql = "CREATE TABLE $table_name ( 
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT, 
        `title` VARCHAR(255) NOT NULL, 
        PRIMARY KEY (`id`)
    );";
    
    $sql .= "CREATE TABLE $table_items_name (
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT , 
        `parent_id` MEDIUMINT NOT NULL , 
        `author_name` LONGTEXT NOT NULL , 
        `author_image` LONGTEXT NOT NULL , 
        `author_description` LONGTEXT NOT NULL , 
        `author_quote` LONGTEXT NOT NULL , 
        PRIMARY KEY (`id`)
    );";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}


function mef_carousel_delete_plugin_database_table(){
    global $wpdb;
    $tableArray = [   
        $wpdb->prefix . 'mef_carousel',
        $wpdb->prefix . 'mef_carousel_items',
    ];

    foreach ($tableArray as $tablename) {
        $wpdb->query("DROP TABLE IF EXISTS $tablename");
    }
}

add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );
add_action( 'wp_enqueue_scripts', 'prefix_add_my_javascript' );

/**
 * Enqueue plugin style-file
 */
function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}
/**
 * Enqueue plugin style-file
 */
function prefix_add_my_javascript() {
    wp_register_script('mef-carousel-js', plugins_url('script.js', __FILE__),'','1.1', true);
    wp_enqueue_script('mef-carousel-js');
}

