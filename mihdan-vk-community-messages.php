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
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
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
		 * Добавить настройки плагина
		 * в меню админки.
		 */
		public function admin_menu() {
			add_options_page(
				'Настройки',
				'VK Community Messages',
				'manage_options',
				$this->slug,
				array( $this, 'options_page' )
			);
		}

		public function register_settings() {}

		/**
		 * Подключить шаблоны вывода
		 * страницы настроек.
		 */
		public function options_page() {
			require_once ( $this->dir_path . 'admin/options.php' );
		}
	}

	function mihdan_vk_community_messages() {
		return Mihdan_Vk_Community_Messages::get_instance();
	}

	// Вешаем инициализацию плагина на хук `after_setup_theme`,
	// чтобы можно было переопределять настройки в самой теме сайта.s
	add_action( 'after_setup_theme', 'mihdan_vk_community_messages' );
}

// eof
