<?php

declare(strict_types=1);

namespace zalatov\runtimeCache;

use Closure;

/**
 * Трейт для кэширования данных в runtime-кэше (то есть на время выполнения скрипта).
 *
 * 1. Не требуется указание лишнего private/protected атрибута.
 * 2. Не дублируется аннотация/документация и к атрибуту, и к методу.
 * 3. Инкапсулирование логики методом - есть уверенность, что в других местах атрибут ничего не изменит.
 *
 * @author Zalatov Alexander <zalatov.ao@gmail.com>
 */
trait RuntimeCacheTrait {
	/**
	 * Закэшированные данные (не зависимые от объекта).
	 * Название атрибута выбрано такое, чтобы не пересекалось с объектом, к которому подключён трейт.
	 *
	 * @var array
	 */
	private static $__rctGlobal = [];

	/**
	 * Закэшированные данные (привязанные к конкретному объекту).
	 * Название атрибута выбрано такое, чтобы не пересекалось с объектом, к которому подключён трейт.
	 *
	 * @var array
	 */
	private $__rctObject = [];

	/**
	 * Форматирование ключа в единый формат.
	 *
	 * @param mixed $key Название ключа
	 *
	 * @return string
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	private static function rctFormatKey($key): string {
		if (is_array($key)) {
			return implode('|', $key);
		}

		return (string)$key;
	}

	/**
	 * Кэширование данных в глобальном runtime-кэше на время выполнения скрипта.
	 * Другие объекты тоже могут получит данные из кэша, если укажут тот же самый ключ.
	 *
	 * @param mixed   $key     Название ключа
	 * @param Closure $closure Функция, которая получит данные, если в кэше нет данных
	 *
	 * @return mixed
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	public static function globalRuntimeCache($key, Closure $closure) {
		$key = self::rctFormatKey($key);

		if (false === array_key_exists($key, self::$__rctGlobal)) {
			self::$__rctGlobal[$key] = $closure();
		}

		return self::$__rctGlobal[$key];
	}

	/**
	 * Удаление данных из глобального runtime-кэша.
	 *
	 * @param mixed $key Название ключа
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	public static function globalRuntimeFlush($key) {
		$key = self::rctFormatKey($key);

		unset(self::$__rctGlobal[$key]);
	}

	/**
	 * Кэширование данных в runtime-кэше на время выполнения скрипта.
	 * Кэш привязывается к конкретному объекту.
	 *
	 * @param mixed   $key     Название ключа в кэше
	 * @param Closure $closure Функция, которая получит данные, если в кэше нет данных
	 *
	 * @return mixed
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	public function objectRuntimeCache($key, Closure $closure) {
		$key = self::rctFormatKey($key);

		if (false === array_key_exists($key, $this->__rctObject)) {
			$this->__rctObject[$key] = $closure();
		}

		return $this->__rctObject[$key];
	}

	/**
	 * Обнуление данных из runtime-кэша для указанного ключа.
	 *
	 * @param mixed $key Название ключа
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	public function objectRuntimeFlush($key) {
		$key = self::rctFormatKey($key);

		unset($this->__rctObject[$key]);
	}

	/**
	 * Обнуление всех данных в runtime-кэше для объекта.
	 *
	 * @author Zalatov Alexander <zalatov.ao@gmail.com>
	 */
	public function objectRuntimeFlushAll() {
		$this->__rctObject = [];
	}
}
