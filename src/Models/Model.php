<?php

namespace App\Models;

use PDO;
use PDOStatement;
use Exception;

abstract class Model
{
    protected static string $table;
    protected static array $fillable = [];
    protected array $attributes = [];
    protected static ?PDO $db = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$fillable)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, static::$fillable)) {
            $this->attributes[$name] = $value;
        }
    }

    public static function find($id)
    {
        $stmt = self::$db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
        $stmt->execute([$id]);
        
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }
        
        return new static($result);
    }

    public static function all(): array
    {
        $stmt = self::$db->query("SELECT * FROM " . static::$table);
        return array_map(fn($row) => new static($row), $stmt->fetchAll());
    }

    public function save(): bool
    {
        if (isset($this->attributes['id'])) {
            return $this->update();
        }
        
        return $this->insert();
    }

    protected function insert(): bool
    {
        $fields = array_keys($this->attributes);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO " . static::$table . " 
                (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = self::$db->prepare($sql);
        $result = $stmt->execute(array_values($this->attributes));
        
        if ($result) {
            $this->attributes['id'] = self::$db->lastInsertId();
        }
        
        return $result;
    }

    protected function update(): bool
    {
        $id = $this->attributes['id'];
        unset($this->attributes['id']);
        
        $fields = array_map(fn($field) => "{$field} = ?", array_keys($this->attributes));
        
        $sql = "UPDATE " . static::$table . " 
                SET " . implode(', ', $fields) . " 
                WHERE id = ?";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([...array_values($this->attributes), $id]);
    }

    public function delete(): bool
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }
        
        $stmt = self::$db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([$this->attributes['id']]);
    }

    public static function where(string $field, $value): PDOStatement
    {
        $stmt = self::$db->prepare("SELECT * FROM " . static::$table . " WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt;
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }
}