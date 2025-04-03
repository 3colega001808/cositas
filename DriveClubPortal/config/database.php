<?php
/**
 * Database Configuration
 * 
 * This file handles the database connection parameters
 * for the DriveClub application.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'driveclub');

/**
 * Get database connection
 * 
 * @return mysqli|false Returns a mysqli connection object or false on failure
 */
function getDbConnection() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return false;
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Execute a query and return the result
 * 
 * @param string $sql The SQL query to execute
 * @param array $params Optional parameters for prepared statements
 * @param string $types Optional string of parameter types (i=integer, s=string, d=double, b=blob)
 * @return array|bool Query results as associative array or false on failure
 */
function executeQuery($sql, $params = [], $types = null) {
    $conn = getDbConnection();
    
    if (!$conn) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Query preparation failed: " . $conn->error);
            $conn->close();
            return false;
        }
        
        // If we have parameters, bind them
        if (!empty($params)) {
            if ($types === null) {
                // Auto-determine types if not provided
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } elseif (is_string($param)) {
                        $types .= 's';
                    } else {
                        $types .= 'b';
                    }
                }
            }
            
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if (!$result) {
            // For queries that don't return results (INSERT, UPDATE, etc.)
            if ($stmt->affected_rows >= 0) {
                $stmt->close();
                $conn->close();
                return true;
            }
            
            error_log("Query execution failed: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return false;
        }
        
        // Fetch all rows as associative array
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $conn->close();
        
        return $rows;
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
        return false;
    }
}

/**
 * Get a single row from a query
 * 
 * @param string $sql The SQL query to execute
 * @param array $params Optional parameters for prepared statements
 * @param string $types Optional string of parameter types
 * @return array|null Single row as associative array or null if not found
 */
function fetchOne($sql, $params = [], $types = null) {
    $results = executeQuery($sql, $params, $types);
    
    if ($results === false) {
        return null;
    }
    
    return empty($results) ? null : $results[0];
}

/**
 * Insert data into a table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value pairs
 * @return int|bool Last inserted ID or false on failure
 */
function insertData($table, $data) {
    $conn = getDbConnection();
    
    if (!$conn) {
        return false;
    }
    
    try {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Insert preparation failed: " . $conn->error);
            $conn->close();
            return false;
        }
        
        // Determine parameter types
        $types = '';
        $params = [];
        
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            
            $params[] = $value;
        }
        
        $stmt->bind_param($types, ...$params);
        
        $stmt->execute();
        
        $insertId = $conn->insert_id;
        
        $stmt->close();
        $conn->close();
        
        return $insertId;
    } catch (Exception $e) {
        error_log("Insert error: " . $e->getMessage());
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
        return false;
    }
}

/**
 * Update data in a table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value pairs to update
 * @param string $whereClause WHERE clause without the "WHERE" keyword
 * @param array $whereParams Parameters for the WHERE clause
 * @return bool True on success, false on failure
 */
function updateData($table, $data, $whereClause, $whereParams = []) {
    $conn = getDbConnection();
    
    if (!$conn) {
        return false;
    }
    
    try {
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "$column = ?";
        }
        $setClause = implode(', ', $setClauses);
        
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Update preparation failed: " . $conn->error);
            $conn->close();
            return false;
        }
        
        // Determine parameter types and combine parameters
        $types = '';
        $params = [];
        
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            
            $params[] = $value;
        }
        
        foreach ($whereParams as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            
            $params[] = $value;
        }
        
        $stmt->bind_param($types, ...$params);
        
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        
        $stmt->close();
        $conn->close();
        
        return $affectedRows >= 0;
    } catch (Exception $e) {
        error_log("Update error: " . $e->getMessage());
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
        return false;
    }
}

/**
 * Delete data from a table
 * 
 * @param string $table Table name
 * @param string $whereClause WHERE clause without the "WHERE" keyword
 * @param array $whereParams Parameters for the WHERE clause
 * @return bool True on success, false on failure
 */
function deleteData($table, $whereClause, $whereParams = []) {
    $conn = getDbConnection();
    
    if (!$conn) {
        return false;
    }
    
    try {
        $sql = "DELETE FROM $table WHERE $whereClause";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Delete preparation failed: " . $conn->error);
            $conn->close();
            return false;
        }
        
        // Determine parameter types
        if (!empty($whereParams)) {
            $types = '';
            
            foreach ($whereParams as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }
            
            $stmt->bind_param($types, ...$whereParams);
        }
        
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        
        $stmt->close();
        $conn->close();
        
        return $affectedRows >= 0;
    } catch (Exception $e) {
        error_log("Delete error: " . $e->getMessage());
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
        return false;
    }
}

/**
 * Begin a transaction
 * 
 * @return bool|mysqli True on success or mysqli connection on success, false on failure
 */
function beginTransaction() {
    $conn = getDbConnection();
    
    if (!$conn) {
        return false;
    }
    
    $conn->begin_transaction();
    
    return $conn;
}

/**
 * Commit a transaction
 * 
 * @param mysqli $conn Database connection
 * @return bool True on success, false on failure
 */
function commitTransaction($conn) {
    if (!$conn) {
        return false;
    }
    
    $result = $conn->commit();
    $conn->close();
    
    return $result;
}

/**
 * Rollback a transaction
 * 
 * @param mysqli $conn Database connection
 * @return bool True on success, false on failure
 */
function rollbackTransaction($conn) {
    if (!$conn) {
        return false;
    }
    
    $result = $conn->rollback();
    $conn->close();
    
    return $result;
}
