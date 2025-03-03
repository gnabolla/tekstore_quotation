<?php

/**
 * Class Database
 *
 * Extended Database wrapper using PDO with additional methods for the quotation system.
 */
class Database
{
    /** @var PDO */
    public $connection;

    /**
     * Database constructor.
     *
     * @param array  $config   Array of config details (host, port, dbname, charset).
     * @param string $username DB username.
     * @param string $password DB password.
     */
    public function __construct(array $config, string $username = 'root', string $password = '')
    {
        // Build DSN string. Example: mysql:host=localhost;port=3306;dbname=isur_system;charset=utf8mb4
        $dsn = 'mysql:' . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    /**
     * Prepare, execute, and return statement for a query.
     *
     * @param string $query  SQL query string.
     * @param array  $params Parameters to bind.
     *
     * @return PDOStatement
     */
    public function query(string $query, array $params = []): PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        return $statement;
    }

    /**
     * Fetch a single record from the database.
     *
     * @param string $query  SQL query string.
     * @param array  $params Parameters to bind.
     *
     * @return array|false   Returns the record or false if not found.
     */
    public function fetchOne(string $query, array $params = [])
    {
        $statement = $this->query($query, $params);
        return $statement->fetch();
    }

    /**
     * Fetch all records from the database.
     *
     * @param string $query  SQL query string.
     * @param array  $params Parameters to bind.
     *
     * @return array   Returns all records.
     */
    public function fetchAll(string $query, array $params = []): array
    {
        $statement = $this->query($query, $params);
        return $statement->fetchAll();
    }

    /**
     * Insert a record into the database.
     *
     * @param string $table  Table name.
     * @param array  $data   Associative array of column names and values.
     *
     * @return string|false  Returns the last inserted ID or false on failure.
     */
    public function insert(string $table, array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($query, array_values($data));
        return $this->connection->lastInsertId();
    }

    /**
     * Update a record in the database.
     *
     * @param string $table  Table name.
     * @param array  $data   Associative array of column names and values.
     * @param string $where  WHERE clause.
     * @param array  $params Parameters for the WHERE clause.
     *
     * @return int  Returns the number of affected rows.
     */
    public function update(string $table, array $data, string $where, array $params = []): int
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $statement = $this->query($query, array_merge(array_values($data), $params));
        return $statement->rowCount();
    }

    /**
     * Delete a record from the database.
     *
     * @param string $table  Table name.
     * @param string $where  WHERE clause.
     * @param array  $params Parameters for the WHERE clause.
     *
     * @return int  Returns the number of affected rows.
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $query = "DELETE FROM {$table} WHERE {$where}";
        
        $statement = $this->query($query, $params);
        return $statement->rowCount();
    }

    /**
     * Generate a new quote number.
     *
     * @param string $prefix Prefix for the quote number (e.g., 'QUO').
     * @return string The generated quote number.
     */
    public function generateQuoteNumber(string $prefix = 'QUO'): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the latest quote number for this month
        $query = "SELECT quote_number FROM quotations 
                 WHERE quote_number LIKE ? 
                 ORDER BY id DESC LIMIT 1";
        
        $pattern = "{$prefix}-{$year}{$month}%";
        $latestQuote = $this->fetchOne($query, [$pattern]);
        
        if ($latestQuote) {
            // Extract the sequential number part
            $parts = explode('-', $latestQuote['quote_number']);
            $lastNumber = intval(substr(end($parts), 6)); // Skip the year and month (6 digits)
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format: QUO-YYYYMM-XXXX (XXXX is sequential)
        return sprintf("%s-%s%s-%04d", $prefix, $year, $month, $newNumber);
    }
}