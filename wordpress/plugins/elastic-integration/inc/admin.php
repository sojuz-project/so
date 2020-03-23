<?php
function manage_admin_menu() {
	// add_menu_page(
  //       "Import Eksport",
  //       'Import Eksport',
  //       'read',
  //       'import_export',
  //       'import_export',
  //       "dashicons-album",
  //       93
  //   );

    add_menu_page(
        "Ustawienia systemowe",
        'Ustawienia systemowe',
        'manage_options',
        'plugin_settings_page',
        'plugin_settings_page',
        "dashicons-admin-generic",
        95
    );
}
add_action( 'admin_menu' , 'manage_admin_menu' );

function extract_term($term) {
	return $term->name;
}

function plugin_settings_page() {
	if (isset($_POST['notify'])) {
		sync_index();
	}
	?>
	<div class="wrap">
		<h1>Ustawienia systemowe</h1>
		<table class="form-table">
			<tbody>
				<form method="post" action="" novalidate="novalidate">
				<tr>
					<th scope="row">
						<label for="notify">Aktualizacja indeksu</label>
					</th>
					<td>
							<input type="submit" name="notify" id="notify" class="button" value="Odźwież" />
							<p class="description" id="notify-description">Wymusza aktualizację wszystkich pytań w indeksie. <span style="color: #f00;">Może znacząco spowolnić działanie systemu!</span></p>
					</td>
				</tr>
				</form>
			</tbody>
		</table>
	</div>
	<?php
}

// function prevent_auto_delete($event) {
//     switch($event->hook) {
//         case 'wp_scheduled_delete':
//             $event = false;
//             break;
//     }
//     return $event;
// }
// add_filter( 'schedule_event', 'prevent_auto_delete', 10, 1);

function rename_trash_status($views) {
	// var_dump($views);
	$views['trash'] = str_replace('Kosz', 'Archiwum', $views['trash']);
	return $views;
}
add_filter('views_edit-post', 'rename_trash_status' );

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function example_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'example_dashboard_widget',         // Widget slug.
                 'Example Dashboard Widget',         // Title.
                 'example_dashboard_widget_function' // Display function.
        );
}
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function example_dashboard_widget_function() {

	// Display whatever it is you want to show.
	echo "Hello World, I'm a great Dashboard Widget";
}

function acf_register_search_field() {
	include_once(__DIR__.'/acf_field_search.php');
	include_once(__DIR__.'/acf_field_button.php');
}

add_action('acf/include_field_types', 'acf_register_search_field'); // v5
add_action('acf/register_fields', 	'acf_register_search_field'); // v4