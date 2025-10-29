<?php

// Conexão com MySQL Local:
$host = 'localhost';
$user = 'root';            
$password = '';       // <--- Coloque a senha do banco de dados aqui, entre os apóstrofos. Exemplo: $password = 'SuaSenhaAqui';     
$database = 'hotel_reservas';
$port = 3306;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        

        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    

    
} catch (PDOException $e) {
    // Tratamento de erro:
    error_log("ERRO DE CONEXÃO COM O BANCO DE DADOS: " . $e->getMessage());
    
    // Retorna o erro:
    http_response_code(503);
    echo json_encode([
        'erro' => 'Serviço indisponível. Banco de dados não respondeu.',
        'detalhes' => 'Verifique se MySQL está rodando e as credenciais estão corretas.'
    ]);
    
    exit();
}
?>