<?php
namespace Library;
/**
 * @class Library.Model
 */
abstract class Model {

	/**
	 * @var \Library\Database\Connector\MySQL
	 */
	protected $db;
	protected $primary;

	protected $config = array(
		'primary' => NULL,
		'table' => NULL,
		'translatemap' => array()
	);

	protected static $translation = array(
		'procedure' => 'translate',
		'table' => 'translation'
	);

	protected static $language = 'en';

	protected $fields = array();

	final public function __construct($id = 0) {
		$this->db = \Library\Database\Connector::getInstance('MySQL');
		$this->primary = $id;

		if (is_null($this->config['primary']))
			$this->config['primary'] = 'id';

		if (is_null($this->config['table']))
			throw new \Library\Common\Exception('A model has to have table.');

		if ($this->primary > 0)
			$this->load(array($this->config['primary'] => $this->primary));
	}

	private function getModelFields() {
		if (count($this->fields) == 0) {
			$class = new \ReflectionClass(get_class($this));
			$properties = $class->getProperties();
			/**
			 * @var $property ReflectionProperty
			 */
			foreach ($properties as $property) {
				if ($property->isPublic()) {
					$this->fields[] = $property->getName();
				}
			}
			$this->fields[] = $this->config['primary'];
		}

		return $this->fields;
	}

	public static function where($class_name, array $where = array(), $order_by = NULL, $limit = NULL) {
		$object = new $class_name();
		return $object->load($where, $order_by, $limit);
	}

	private function load(array $where = array(), $order_by = NULL, $limit = NULL) {

		$parameters = array();
		foreach ($where as $field => $value)
		{
			if (is_object($value) && is_subclass_of($value, __CLASS__)) {
				$parameters[] = sprintf("`%s`.`%s` = %s", $this->config['table'], $field, $value->getPrimary());
			} else if (is_array($value)) {
				$options = array();
				foreach ($value as $option)
				{
					$options[] = sprintf("'%s'", $this->db->clear($option));
				}
				$parameters[] = sprintf("`%s`.`%s` IN (%s)", $this->config['table'], $field, implode(', ', $options));
			} else if (is_numeric($value)) {
				$parameters[] = sprintf("`%s`.`%s` = %s", $this->config['table'], $field, preg_replace('/[^0-9\.]/u', '', $value));
			} else {
				$parameters[] = sprintf("`%s`.`%s` = '%s'", $this->config['table'], $field, $this->db->clear($value));
			}
		}

		if (count($parameters) > 0) {
			$query = sprintf('SELECT * FROM `%s` WHERE %s', $this->config['table'], implode(' AND ', $parameters));
		} else {
			$query = sprintf('SELECT * FROM `%s`', $this->config['table']);
		}

		if (isset($this->config['translatemap']) && count($this->config['translatemap']) > 0) {
			$translation_fields = array();
			foreach ($this->config['translatemap'] as $field)
			{
				$translation_fields[] = sprintf("%s('%s', '%s', %s, '%s') AS `%s`",
					self::$translation['procedure'],
					$this->config['table'],
					$field, '`' . $this->config['table'] . '`.`' . $this->config['primary'] . '`',
					self::$language, $field);
			}
			$query = str_replace('*', '*,' . implode(', ', $translation_fields), $query);
		}

		if (!is_null($order_by)) {
			if (!is_array($order_by))
				$order_by = array($order_by);

			foreach ($order_by as $i => $field)
			{
				$order_by[$i] = sprintf('`%s`', $field);
			}

			$query .= ' ORDER BY ' . implode(', ', $order_by);
		}

		if ($limit instanceof \Library\Utility\Pagination) {
			/**
			 * @var $limit \Library\Utility\Pagination
			 */
			$total_row_query = sprintf('SELECT COUNT(*) AS `total` FROM `%s`', $this->config['table']);
			$find = $this->db->getFirstRow($total_row_query);
			$total_row_number = $find['total'];
			$limit->setTotalRows($total_row_number);

			$query = sprintf('%s LIMIT %d,%d', $query, $limit->getStartFrom(), $limit->getOffset());
		}

		$find = $this->db->getRows($query);

		$result = array();
		if (false !== $find && count($find) > 0) {

			$fields = $this->getModelFields();

			if (count($find) == 1) {
				foreach ($fields as $field)
				{
					$value = $find[0][$field];

					if ($field == $this->config['primary'])
						$this->primary = $value;

					$this->{$field} = $value;
				}
				$result[] = $this;

			} else {

				foreach ($find as $data)
				{
					$class = get_class($this);
					$object = new $class();
					foreach ($data as $field => $value)
					{
						if ($field == $this->config['primary'])
							$object->primary = $value;

						$object->{$field} = $value;
					}
					$result[] = $object;
				}
			}
			return $result;
		} else {
			return array();
		}
	}

	public function save() {
		$fields = $this->getModelFields();
		foreach ($fields as $field)
		{
			$value = $this->{$field};
			/**
			 * @var $value Model
			 */
			if (isset($this->config['translatemap']) && is_array($this->config['translatemap'])) {
				if (in_array($field, $this->config['translatemap']) && !is_array($this->{$field})) {
					$this->{$field} = array(self::$language => $this->{$field});
				}

				if (in_array($field, $this->config['translatemap'])) {
					$translation_sql = sprintf("REPLACE INTO `%s` (`table`, `field`, `row_id`, `lang`, `translation`) VALUES ", self::$translation['table']);
					$translations = array();
					foreach ($this->{$field} as $language => $value)
					{
						$translations[] = sprintf("('%s', '%s', %%1\$d, '%s', '%s')", $this->config['table'], $field, $language, $value);
					}
					$translation_sql .= implode(', ', $translations);
					continue;
				}
			}

			if (is_object($value) && is_subclass_of($value, __CLASS__)) {
				$value->save();
				$parameters[] = sprintf("`%s` = %s", $field, $value->getPrimary());
			} else if (is_numeric($value)) {
				$parameters[] = sprintf("`%s` = %s", $field, preg_replace('/[^0-9\.]/u', '', $value));
			} else {
				$parameters[] = sprintf("`%s` = '%s'", $field, $this->db->clear($value));
			}
		}

		if ($this->primary > 0) {

			$parameters[] = sprintf("`%s` = '%s'", 'update_date', $this->db->clear(date('Y-m-d H:i:s')));

			$this->db->query(sprintf('UPDATE `%s` SET %s WHERE `%s` = %d', $this->config['table'], implode(', ', $parameters), $this->config['primary'], $this->primary));

			if (isset($translation_sql)) {
				$this->db->query(sprintf($translation_sql, $this->primary));
			}
		} else {

			$parameters[] = sprintf("`%s` = '%s'", 'create_date', $this->db->clear(date('Y-m-d H:i:s')));
			$parameters[] = sprintf("`%s` = '%s'", 'update_date', $this->db->clear(date('Y-m-d H:i:s')));

			$this->db->query(sprintf('INSERT INTO `%s` SET %s', $this->config['table'], implode(', ', $parameters)));
			$this->primary = $this->db->insertId();
			if ($this->primary > 0 && isset($translation_sql)) {
				$this->db->query(sprintf($translation_sql, $this->primary));
			}
			$this->load();
		}
	}

	public function delete() {
		$this->db->query(sprintf("DELETE FROM `%s` WHERE `%1\$s`.`%s` = '%s'", $this->config['table'], $this->config['primary'], $this->primary));
	}

	public function __toString() {
		return (string) (int) $this->primary;
	}

	public static function setLanguage($language) {
		self::$language = $language;
	}

	public function getPrimary() {
		return $this->primary;
	}

}