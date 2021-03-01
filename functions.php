<?php
function barcode_scanner_scripts() {
//	styles
	if ( is_product() ) {
		wp_enqueue_script( 'barcode_scanner_lib', plugins_url( '/assets//js/barcode_scanner_lib.js', __FILE__ ) );
		wp_enqueue_script( 'barcode_scanner_init', plugins_url( '/assets/js/barcode_scanner_init.js', __FILE__ ), array('barcode_scanner_lib') );
	}

}

add_action( 'wp_enqueue_scripts', 'barcode_scanner_scripts' );

add_shortcode( 'add__qrcsanner', 'qrcsanner' );
function qrcsanner() {
	?>
	<div class="qrscanner d-block d-md-none">
		<section class="container" id="demo-content">


			<div class="d-flex justify-content-between" style="margin-bottom: 10px;">
				<a class="button" id="startButton">Старт</a>
				<a class="button" id="resetButton">Стоп</a>
				<!--                        <a class="button" id="stopButton">Reset</a>-->
			</div>

			<div class="embed-responsive embed-responsive-16by9">
				<video id="video" class="embed-responsive-item"
				       style="border: 1px solid gray; width: 100%; height: auto; overflow-y: hidden;">

				</video>
			</div>

			<div id="sourceSelectPanel" style="display:none; margin: 5px 0;">
				<label for="sourceSelect">Камера:</label>
				<select id="sourceSelect" style="max-width:400px">
				</select>
			</div>

		</section>
	</div>

	<?php
}

$dds_qr_scanner_page = 'dds_qr_scanner_parametrs';

add_action( 'admin_menu', 'dds_qr_scanner_custom_menu_page' );
add_action( 'admin_init', 'dds_qr_scanner_option_settings' );
add_shortcode( 'add__qrcsanner', 'qrcsanner' );

function dds_qr_scanner_custom_menu_page() {
	global $dds_qr_scanner_page;
	add_menu_page( 'QR scanner', 'QR scanner', 'manage_options', 'qr_scanner', 'dds_qr_scanner_options_page', 'dashicons-grid-view', 6 );
}

function dds_qr_scanner_options_page() {
	global $dds_qr_scanner_page;
	?>
	<div class="wrap">
	<h2>Настройка плагина</h2>
	<form method="post" enctype="multipart/form-data" action="options.php">
		<?php
		settings_fields( 'dds_qr_scanner_options' ); // меняем под себя только здесь (название настроек)
		do_settings_sections(  $dds_qr_scanner_page );
		?>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'arena' ) ?>"/>
		</p>
	</form>
	</div><?php
}

/*
 * Регистрируем настройки
 * Мои настройки будут храниться в базе под названием dds_options (это также видно в предыдущей функции)
 */

function dds_qr_scanner_option_settings() {
	global $dds_qr_scanner_page;
	// Присваиваем функцию валидации ( dds_validate_settings() ). Вы найдете её ниже
	register_setting( 'dds_qr_scanner_options', 'dds_qr_scanner_options', 'dds_qr_scanner_validate_settings' ); // dds_options

	// Добавляем секцию
	add_settings_section( 'dds_qr_scanner_section_1', 'Настройка плагина', '', $dds_qr_scanner_page );

	// Создадим текстовое поле в первой секции
	$dds_qr_scanner_field_params = array(
		'type'      => 'text',
		'id'        => 'target',
		'desc'      => 'Result',
		'label_for' => 'target'
	);
	add_settings_field( 'my_adres_field', 'Цель', 'dds_qr_scanner_option_display_settings', $dds_qr_scanner_page, 'dds_qr_scanner_section_1', $dds_qr_scanner_field_params );

}


function dds_qr_scanner_option_display_settings( $args ) {
	extract( $args );

	$option_name = 'dds_qr_scanner_options';

	$o = get_option( $option_name );

	switch ( $type ) {
		case 'text':
			$o[ $id ] = esc_attr( stripslashes( $o[ $id ] ) );
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
			echo ( $desc != '' ) ? "<br /><span class='description'>$desc</span>" : "";
			break;
	}
}
/*
 * Функция проверки правильности вводимых полей
 */
function dds_qr_scanner_validate_settings( $input ) {
	foreach ( $input as $k => $v ) {
		$valid_input[ $k ] = trim( $v );

		/* Вы можете включить в эту функцию различные проверки значений, например
		if(! задаем условие ) { // если не выполняется
			$valid_input[$k] = ''; // тогда присваиваем значению пустую строку
		}
		*/
	}

	return $valid_input;
}

$all_options = get_option( 'dds_qr_scanner_options' );

$qr_target = get_option( 'dds_qr_scanner_options' )[ 'target' ];
