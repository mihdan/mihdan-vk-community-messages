<?php
/**
 * Plugin Name: Mihdan: VK Community Messages
 * Description: Плагин добавляет на ваш сайт виджет «Сообщения сообщества» из соцсети ВКонтакте на ваш WordPress сайт.
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
		public static $dir_path;

		/**
		 * URL до плагина
		 *
		 * @var string
		 */
		public static $dir_uri;

		/**
		 * Хранит экземпляр класса
		 *
		 * @var $instance
		 */
		private static $instance;

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
			self::$dir_path = apply_filters( 'mihdan_vk_community_messages_dir_path', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			self::$dir_uri   = apply_filters( 'mihdan_vk_community_messages_dir_uri', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		/**
		 * Подключаем зависимости
		 */
		private function includes() {}

		/**
		 * Хукаем.
		 */
		private function hooks() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Подключаем скрипты и стили.
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'vk-api', '//vk.com/js/api/openapi.js', array(), false, true );
			wp_localize_script( 'vk-api', $this->slug . '_options', $this->options );
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
