<?php

namespace Parable\ORM;

class Model
{
    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var array */
    protected $mapper = [];

    /** @var array */
    protected $exportable = [];

    /** @var null|int */
    public $id;

    /** @var null|string */
    protected $tableName;

    /** @var null|string */
    protected $tableKey;

    public function __construct(
        \Parable\ORM\Database $database
    ) {
        $this->database = $database;
    }

    /**
     * Generate a query set to use the current Model's table name & key
     *
     * @return \Parable\ORM\Query
     */
    public function createQuery()
    {
        $query = \Parable\ORM\Query::createInstance();
        $query->setTableName($this->getTableName());
        $query->setTableKey($this->getTableKey());
        return $query;
    }

    /**
     * Saves the model, either inserting (no id) or updating (id)
     *
     * @return bool
     */
    public function save()
    {
        $array = $this->toArray();

        $query = $this->createQuery();

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        if ($this->id) {
            $query->setAction('update');
            $query->addValue($this->getTableKey(), $this->id);

            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }

            // Since it's an update, add updated_at if the model implements it
            if (property_exists($this, 'updated_at')) {
                $query->addValue('updated_at', $now);
                $this->updated_at = $now;
            }
        } else {
            $query->setAction('insert');

            foreach ($array as $key => $value) {
                if ($key !== $this->tableKey) {
                    $query->addValue($key, $value);
                }
            }

            // Since it's an insert, add created_at if the model implements it
            if (property_exists($this, 'created_at')) {
                $query->addValue('created_at', $now);
                $this->created_at = $now;
            }
        }
        $result = $this->database->query($query);
        if ($result && $query->getAction() === 'insert') {
            $this->id = $this->database->getInstance()->lastInsertId();
        }
        return $result ? true : false;
    }

    /**
     * Deletes the current model from the database
     *
     * @return bool
     */
    public function delete()
    {
        $query = $this->createQuery();
        $query->setAction('delete');
        $query->where($query->buildAndSet([$this->getTableKey(), '=', $this->id]));
        $result = $this->database->query($query);

        return $result ? true : false;
    }

    /**
     * Populates the current model with the data provided
     *
     * @param array $data
     *
     * @return $this;
     */
    public function populate(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $this->guessValueType($value);
            }
        }
        return $this;
    }

    /**
     * Set the tableName
     *
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Return the tableName
     *
     * @return null|string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set the tableKey
     *
     * @param string $tableKey
     *
     * @return $this
     */
    public function setTableKey($tableKey)
    {
        $this->tableKey = $tableKey;
        return $this;
    }

    /**
     * Return the tableKey
     *
     * @return null|string
     */
    public function getTableKey()
    {
        return $this->tableKey;
    }

    /**
     * Set the mapper
     *
     * @param array $mapper
     *
     * @return $this;
     */
    public function setMapper(array $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Return the mapper
     *
     * @return array
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Returns the exportable array
     *
     * @return array
     */
    public function getExportable()
    {
        return $this->exportable;
    }

    /**
     * Attempts to guess the value type. Will return int, float or string.
     *
     * @param string $value
     *
     * @return int|float|string
     */
    public function guessValueType($value)
    {
        if (is_numeric($value) && (int)$value == $value) {
            return (int)$value;
        } elseif (is_numeric($value) && (float)$value == $value) {
            return (float)$value;
        }
        return $value;
    }

    /**
     * Generates an array of the current model, without the protected values
     *
     * @return array
     */
    public function toArray()
    {
        $reflection = new \ReflectionClass(static::class);

        $arrayData = [];
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $value = $property->getValue($this);

            // We don't want to add either static properties or when the value evaluates to regular empty but isn't a 0
            if ($property->isStatic() || ($value !== 0 && empty($value))) {
                continue;
            }

            // If it's specifically decreed that it's a null value, we leave it in, which will set it to NULL in the db
            if ($value === \Parable\ORM\Database::NULL_VALUE) {
                $value = null;
            }

            $arrayData[$property->getName()] = $value;
        }

        if ($this->getMapper()) {
            $arrayData = $this->toMappedArray($arrayData);
        }

        return $arrayData;
    }

    /**
     * Attempts to use stored mapper array to map fields from the current model's properties to what is set in the
     * array.
     *
     * @param array $array
     *
     * @return array
     */
    public function toMappedArray(array $array)
    {
        $mappedArray = [];
        foreach ($this->getMapper() as $from => $to) {
            $mappedArray[$to] = $array[$from];
        }
        return $mappedArray;
    }

    /**
     * Export to array, which will exclude unexportable keys
     *
     * @return array
     */
    public function exportToArray()
    {
        $data = $this->toArray();
        $exportData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->exportable)) {
                $exportData[$key] = $data[$key];
            }
        }
        return $exportData;
    }

    /**
     * Reset all public properties to null
     *
     * @return $this
     */
    public function reset()
    {
        $reflection = new \ReflectionClass(static::class);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $this->{$property->getName()} = null;
            }
        }
        return $this;
    }
}
