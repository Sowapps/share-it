<?php

namespace Orpheus\Entity;

use Exception;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\SQLRequest\SQLSelectRequest;

class EntityService {
	
	/**
	 * @var string The entity class, if not used, override the calling methods
	 */
	protected $entityClass;
	
	/**
	 * @var array The fields
	 */
	protected $fields = [];
	
	/**
	 * @var array The columns
	 */
	protected $columns = [];
	
	/**
	 * EntityService constructor.
	 *
	 * @param string $entityClass
	 */
	public function __construct($entityClass) {
		$this->entityClass = $entityClass;
	}
	
	/**
	 * @return array
	 */
	public function extractPublicArray($item, $model = 'all') {
		if( method_exists($item, 'asArray') ) {
			return $item->asArray($model);
		}
		return $item->all;
	}
	
	/**
	 * @return string
	 */
	public function getDomain() {
		$c = $this->getEntityClass();
		return $c::getDomain();
	}
	
	public function getEntityClass() {
		if( !$this->entityClass ) {
			throw new Exception('Invalid declaration of ' . get_called_class() . ', override calling methods or provide entityClass property');
		}
		return $this->entityClass;
	}
	
	public function loadItem($id) {
		$c = $this->getEntityClass();
		return $c::load($id, false);
	}
	
	/**
	 * @param array $input
	 * @param array|null $fields
	 * @return int The new item ID
	 */
	public function createItem($input, $fields = null) {
		$c = $this->getEntityClass();
		if( $fields == null ) {
			$fields = $this->getEditableFields();
		}
		if( method_exists($c, 'make') ) {
			return call_user_func([$c, 'make'], $input, $fields);
		}
		return call_user_func([$c, 'create'], $input, $fields);
	}
	
	public function getEditableFields() {
		return $this->fields ? $this->fields->getEditFields() : call_user_func([$this->getEntityClass(), 'getEditableFields']);
	}
	
	/**
	 * @param array|null $filter
	 * @return SQLSelectRequest
	 * @throws Exception
	 */
	public function getSelectQuery($filter = null) {
		/** @var PermanentEntity $c */
		$c = $this->getEntityClass();
		/** @var SQLSelectRequest $query */
		$query = $c::get();
		if( !empty($filter['max']) ) {
			$query->number($filter['max']);
			if( !empty($filter['page']) ) {
				$query->fromOffset($filter['max'] * ($filter['page'] - 1));
			}
		}
		if( !empty($filter['search']) && is_array($filter['search']) ) {
			foreach( $filter['search'] as $searchModel => $searchValue ) {
				$query->where($c::getCondition($searchModel, $searchValue));
			}
		}
		return $query;
	}
	
	public function updateItem(PermanentEntity $item, $input, $fields = null) {
		return $item->update($input, $fields !== null ? $fields : $this->getEditableFields());
	}
	
	public function deleteItem(PermanentEntity $item) {
		return $item->remove();
	}
	
	public function addColumn($label, $orderKey, $valueFunction) {
		$this->columns[] = (object) [
			'label'         => $label,
			'orderKey'      => $orderKey,
			'valueFunction' => $valueFunction,
		];
	}
	
	/**
	 * @return array
	 */
	public function getColumns() {
		return $this->columns;
	}
	
	public function getItemLink($item) {
		return $item->getLink();
	}
	
	/**
	 * @return FieldList
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 * @param FieldList $fields
	 */
	public function setFields($fields) {
		$this->fields = $fields;
	}
	
}
