<?php

namespace denisok94\helper\other;

use Exception;
use SQLite3;
use SQLite3Result;

/**
 * Класс для работы с SQLite базой
 * 
 * 
 * ```php
 * try {
 *  // подключаемся
 *  $db = new SQLiteDatabase($file_db);
 *  // что-то делаем...
 * } catch (/Exception $e) {
 *  // обрабатываем ошибки, если есть
 * } finally {
 *  // закрываем соединение
 *  $db->close();
 * }
 * ```
 */
class SQLiteDataBase
{
    protected SQLite3 $db;

    public function __construct(string $dbPath)
    {
        $this->db = new SQLite3($dbPath, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        $this->db->busyTimeout(5000); // Ожидание до 5 секунд при блокировке базы данных
        $this->db->enableExceptions(true); // Активация исключений для обработки ошибок
    }

    /**
     * @param string $query
     * @return bool
     */
    public function exec(string $query): bool
    {
        return $this->db->exec($query);
    }

    /**
     * Выполнить любой кастомный sql запрос
     * @param string $query
     * @return bool|SQLite3Result
     */
    public function query(string $query)
    {
        return $this->db->query($query);
    }

    /**
     * @param string $table Название таблицы
     * @param array $columns 
     * @param string|array $where
     * @param string $order
     * @throws Exception
     * @return array<array|bool>
     * 
     * ```php
     * $maps = $db->select('map', ['*'], "type_id = $typeId", 'type_id ASC');
     * ```
     */
    public function select(string $table, array $columns = ['*'], $where = null, ?string $order = null): array
    {
        $query = "SELECT " . implode(', ', $columns) . " FROM $table";

        if ($where) {
            if (is_array($where)) {
                $wheres = [];
                foreach ($where as $column => $value) {
                    $wheres[] = "$column = $value";
                }
                $query .= " WHERE " . implode(' AND ', $wheres);
            } else if (is_string($where)) {
                $query .= " WHERE $where";
            }
        }

        if ($order) {
            $query .= " ORDER BY $order";
        }

        $result = $this->db->query($query);

        if ($result) {
            $rows = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
            return $rows;
        }

        throw new Exception('Ошибка выборки данных: ' . $this->db->lastErrorMsg());
    }

    /**
     * @param string $table Название таблицы
     * @param array $data  Ассоциативный массив: 'имя_столбца' => 'значение'
     * @throws Exception
     * @return int
     * 
     * ```php
     * $db->insert('table_name', [
     *  'item_id' => $itemId,
     *  'type' => $type,
     * ]);
     * ```
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $this->db->prepare($query);
        $i = 1;
        foreach ($data as $value) {
            $stmt->bindValue($i++, $value);
        }

        if ($stmt->execute()) {
            return $this->db->lastInsertRowID();
        }

        throw new Exception('Ошибка вставки данных: ' . $this->db->lastErrorMsg());
    }

    /**
     * Универсальное обновление записей с условием WHERE
     * @param string $table Название таблицы
     * @param array $data Данные для обновления: 'имя_столбца' => 'значение'
     * @param string $where Условие WHERE
     * @param array $params Параметры для подготовленного запроса
     * @return bool Успешность операции
     * @throws Exception
     */
    public function update(string $table, array $data, string $where = '', array $params = []): bool
    {
        if (empty($data)) {
            return false;
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "$column = :update_$column";
        }
        $setClause = implode(', ', $setParts);

        $query = "UPDATE $table SET $setClause";

        if (!empty($where)) {
            $query .= " WHERE $where";
        }

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception('Ошибка подготовки запроса: ' . $this->db->lastErrorMsg());
        }

        // Привязываем данные для обновления
        foreach ($data as $column => $value) {
            $stmt->bindValue(":update_$column", $value);
        }

        // Привязываем параметры для условия WHERE
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($stmt->execute()) {
            return $this->db->changes() > 0;
        }

        throw new Exception('Ошибка обновления записей: ' . $this->db->lastErrorMsg());
    }

    /**
     * @param string $table Название таблицы
     * @param string $where
     * @throws Exception
     * @return bool
     */
    public function delete(string $table, string $where): bool
    {
        $query = "DELETE FROM $table WHERE $where";

        if ($this->exec($query)) {
            return true;
        }

        throw new Exception('Ошибка удаления данных: ' . $this->db->lastErrorMsg());
    }

    //------------------

    /**
     * Поиск одного элемента по ID
     * @param string $table Название таблицы
     * @param int $id Значение ID для поиска
     * @param array $columns Столбцы для выборки (по умолчанию все)
     * @return array|null Ассоциативный массив с данными или null, если запись не найдена
     * @throws Exception
     * 
     * ```php
     * $element = $db->getById('table_name', $id);
     * ```
     */
    public function getById(string $table, int $id, array $columns = ['*']): ?array
    {
        $columnsStr = implode(', ', $columns);
        $query = "SELECT $columnsStr FROM $table WHERE id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception('Ошибка подготовки запроса: ' . $this->db->lastErrorMsg());
        }

        $stmt->bindValue(1, $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception('Ошибка выполнения запроса: ' . $this->db->lastErrorMsg());
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);

        // Если строка не найдена, возвращаем null
        return $row ?: null;
    }

    /**
     * Обновление записи по ID
     * @param string $table Название таблицы
     * @param int $id ID записи для обновления
     * @param array $data Данные для обновления: 'имя_столбца' => 'значение'
     * @return bool Успешность операции
     * @throws Exception
     * 
     * ```php
     * $db->updateById('table_name', $id, ['collection_id' => $collection_id])
     * ```
     */
    public function updateById(string $table, int $id, array $data): bool
    {
        if (empty($data)) {
            return true; // Ничего не обновлять
        }

        $setParts = [];
        $i = 1;
        foreach ($data as $column => $value) {
            $setParts[] = "$column = :$column";
        }
        $setClause = implode(', ', $setParts);

        $query = "UPDATE $table SET $setClause WHERE id = :id";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception('Ошибка подготовки запроса: ' . $this->db->lastErrorMsg());
        }

        // Привязываем значения
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            // Проверяем, что запись действительно была обновлена
            return $this->db->changes() > 0;
        }

        throw new Exception('Ошибка обновления записи: ' . $this->db->lastErrorMsg());
    }

    /**
     * Безопасное обновление — сначала проверяет существование записи
     * @param string $table Название таблицы
     * @param int $id ID записи
     * @param array $data Данные для обновления: 'имя_столбца' => 'значение'
     * @return array ['success' => bool, 'message' => string]
     * 
     * ```php
     * $db->safeUpdateById('table_name', $id, ['collection_id' => $collection_id])
     * ```
     */
    public function safeUpdateById(string $table, int $id, array $data): array
    {
        try {
            // Сначала проверяем, существует ли запись
            $existing = $this->getById($table, $id);
            if ($existing === null) {
                return [
                    'success' => false,
                    'message' => "Запись с ID $id не найдена"
                ];
            }

            // Выполняем обновление
            $updated = $this->updateById($table, $id, $data);

            if ($updated) {
                return [
                    'success' => true,
                    'message' => "Запись с ID $id успешно обновлена"
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Запись с ID $id не была изменена (возможно, данные уже актуальны)"
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка обновления: ' . $e->getMessage()
            ];
        }
    }

    //---------------------------------------------

    /**
     * Создание новой таблицы
     * @param string $tableName Название таблицы
     * @param array $columns Ассоциативный массив: 'имя_столбца' => 'тип_и_атрибуты'
     * @param bool $ifNotExists Добавлять IF NOT EXISTS (по умолчанию true)
     * @return bool Успешность выполнения
     * @throws Exception
     * 
     * 
     * ```php
     * $db = new SQLiteDataBase($file_db);
     * $db->exec("PRAGMA foreign_keys = ON");
     * 
     * $db->createTable('anime', [
     *  'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
     *  'name' => 'TEXT NOT NULL',
     *  'created_at' => 'TEXT DEFAULT (datetime(\'now\'))'
     * ]);

     * $db->createTable('map', [
     *  'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
     *  'anime_id' => 'INTEGER NULL',
     *  'type' => 'TEXT NOT NULL',
     *  'created_at' => 'TEXT DEFAULT (datetime(\'now\'))',
     *  //
     *  'FOREIGN KEY(anime_id) REFERENCES anime(id) ON DELETE CASCADE' => ''
     * ]);

     * $db->exec("CREATE INDEX IF NOT EXISTS idx_map_anime_id ON map(anime_id)");
     * 
     * $db->close();
     * ```
     */
    public function createTable(string $tableName, array $columns, bool $ifNotExists = true): bool
    {
        $ifNotExistsClause = $ifNotExists ? 'IF NOT EXISTS ' : '';
        $columnsDefinition = [];
        $constraints = [];

        foreach ($columns as $columnName => $columnDef) {
            // Если ключ — строка (не числовая), считаем его ограничением
            if (is_string($columnName)) {
                $columnsDefinition[] = "$columnName $columnDef";
            } else {
                // Иначе — это столбец
                $constraints[] = $columnDef;
            }
        }

        $query = "CREATE TABLE $ifNotExistsClause$tableName (\n    " .
            implode(",\n    ", $columnsDefinition);

        // Добавляем ограничения, если есть
        if (!empty($constraints)) {
            $query .= ",\n    " . implode(",\n    ", $constraints);
        }

        $query .= "\n)";

        if ($this->db->exec($query)) {
            return true;
        }

        throw new Exception('Ошибка создания таблицы: ' . $this->db->lastErrorMsg());
    }

    public function close(): void
    {
        $this->db->close();
    }
}
