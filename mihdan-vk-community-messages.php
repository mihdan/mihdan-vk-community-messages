<?php
/**
 * Plugin Name: Mihdan: VK Community Messages
 * Description: Плагин добавляет на ваш WordPress сайт виджет «Сообщения сообщества» из ВКонтакте.
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-vk-community-messages
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com/
 * Plugin URI: https://www.kobzarev.com/projects/vk-community-messages/
 * Version: 1.0
 *
 * @package mihdan-vk-community-messages
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Mihdan_Vk_Community_Messages' ) ) {

	/**
	 * Class Mihdan_Vk_Community_Messages
	 */
	final class Mihdan_Vk_Community_Messages {

		/**
		 * @var string
		 */
		private $slug = 'mihdan_vk_community_messages';

		/**
		 * Путь к плагину
		 *
		 * @var string
		 */
		private $dir_path;

		/**
		 * URL до плагина
		 *
		 * @var string
		 */
		private $dir_uri;

		/**
		 * Хранит экземпляр класса
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * @var array
		 * @link https://vk.com/dev/widget_community_messages
		 */
		private $options = array();

		/**
		 * Вернуть единственный экземпляр класса
		 *
		 * @return Mihdan_Vk_Community_Messages
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Инициализируем нужные методы
		 *
		 * Mihdan_FAQ constructor.
		 */
		private function __construct() {
			$this->setup();
			$this->includes();
			$this->hooks();
		}

		/**
		 * Установка основных переменных плагина
		 */
		private function setup() {
			$this->dir_path = apply_filters( 'mihdan_vk_community_messages_dir_path', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->dir_uri   = apply_filters( 'mihdan_vk_community_messages_dir_uri', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			$this->options = array(
				'onCanNotWrite' => 'function() {}',
				'welcomeScreen' => false,
				'expandTimeout' => 0,
				'expanded' => 0,
				'widgetPosition' => 'right',
				'buttonType' => 'blue_circle',
				'disableButtonTooltip' => false,
				'tooltipButtonText' => 'Ответим на любые ваши вопросы',
				'disableNewMessagesSound' => false,
				'disableExpandChatSound' => false,
				'disableTitleChange' => false,
				//'from_dev' => true,
			);

			// Можно переопределить настройки програмно
			$this->options   = apply_filters( 'mihdan_vk_community_messages_options', $this->options );
		}

		/**
		 * Подключаем зависимости
		 */
		private function includes() {}

		/**
		 * Получить слюг плагина.
		 *
		 * @return string
		 */
		public function get_slug() {
			return $this->slug;
		}

		/**
		 * Хукаем.
		 */
		private function hooks() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'insert_placeholder' ) );
			//add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			//add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'customize_register', array( $this, 'customize_register' ) );
		}

		/**
		 * Подключаем скрипты и стили.
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'vk-api', '//vk.com/js/api/openapi.js', array(), false, true );
			wp_localize_script( 'vk-api', $this->slug . '_options', $this->options );
			wp_add_inline_script( 'vk-api', 'VK.Widgets.CommunityMessages( "vk_community_messages", 127607773, ' . $this->slug . '_options );' );
		}

		/**
		 * Вставка плейсхолдера в футер
		 * для подгрузки в него VK API.
		 */
		public function insert_placeholder() {
			echo '<div id="vk_community_messages"></div>';
		}

		/**
		 * @param WP_Customize_Manager $wp_customize
		 */
		public function customize_register( $wp_customize ) {

			// 1. Добавить секцию
			$wp_customize->add_section( $this->slug,
				array(
					'title'       => __( 'VK Community Messages', $this->slug ),
					//'priority'    => 35,
					'capability'  => 'edit_theme_options',
					'description' => __( 'Allows you to customize some example settings for MyTheme.', $this->slug ),
				)
			);

			$wp_customize->add_setting(
				$this->slug . '_options[welcome_screen]',
				array(
					'default'    =>  'true',
					'type' => 'option',
					//'transport'  =>  'postMessage'
				)
			);

			$wp_customize->add_control(
				'welcome_screen',
				array(
					'section'   => $this->slug,
					'settings' => $this->slug . '_options[welcome_screen]',
					'label'     => 'Экран приветствия',
					'description'     => 'Информация о том, нужно ли показывать экран приветствия',
					'type'      => 'checkbox'
				)
			);

			$wp_customize->add_setting(
				$this->slug . '_options[expanded]',
				array(
					'default'    =>  'true',
					'type' => 'option',
					//'transport'  =>  'postMessage'
				)
			);

			$wp_customize->add_control(
				'expanded',
				array(
					'section'   => $this->slug,
					'settings' => $this->slug . '_options[expanded]',
					'label'     => 'Раскрытие',
					'description'     => 'Если нужно раскрыть виджет сразу;',
					'type'      => 'checkbox'
				)
			);

			//2. Register new settings to the WP database...
//			$wp_customize->add_setting( 'link_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
//				array(
//					'default'    => '#2BA6CB', //Default setting/value to save
//					'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
//					'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
//					'transport'  => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
//				)
//			);
//
//			//3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
//			$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
//				$wp_customize, //Pass the $wp_customize object (required)
//				'mytheme_link_textcolor', //Set a unique ID for the control
//				array(
//					'label'      => __( 'Link Color', 'mytheme' ), //Admin-visible name of the control
//					'settings'   => 'link_textcolor', //Which setting to load and manipulate (serialized is okay)
//					'priority'   => 10, //Determines the order this control appears in for the specified section
//					'section'    => $this->slug, //ID of the section this control should render in (can be one of yours, or a WordPress default section)
//				)
//			) );
		}

		/**
		 * Добавить настройки плагина
		 * в меню админки.
		 */
//		public function admin_menu() {
//			add_theme_page(
//				'Настройки',
//				'VK Community Messages',
//				'edit_theme_options',
//				'customize.php'
//			);
//		}

//		public function register_settings() {}

		/**
		 * Подключить шаблоны вывода
		 * страницы настроек.
		 */
//		public function options_page() {
//			require_once ( $this->dir_path . 'admin/options.php' );
//		}
	}

	function mihdan_vk_community_messages() {
		return Mihdan_Vk_Community_Messages::get_instance();
	}

	// Вешаем инициализацию плагина на хук `after_setup_theme`,
	// чтобы можно было переопределять настройки в самой теме сайта.s
	add_action( 'after_setup_theme', 'mihdan_vk_community_messages' );
}
// eof
