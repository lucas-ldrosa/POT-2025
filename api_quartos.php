<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Gerenciamento de quartos:
if ($method === 'GET') {
    obterQuartos();
} elseif ($method === 'POST') {
    criarQuarto();
} elseif ($method === 'PUT') {
    atualizarQuarto();
} elseif ($method === 'DELETE') {
    deletarQuarto();
}

// Obter quartos disponíveis:
function obterQuartos() {
    global $pdo;
    
    try {
        // Filtra quartos ativos:
        $sql = "SELECT * FROM quartos WHERE ativo = 1 ORDER BY numero";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($quartos);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao buscar quartos.']);
    }
}

// Criar quarto:
function criarQuarto() {
    global $pdo;
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar dados obrigatórios:
    if (empty($data['numero']) || empty($data['tipo']) || empty($data['preco'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios: número, tipo, preço']);
        return;
    }
    
    try {
        $sql = "INSERT INTO quartos (numero, tipo, preco, descricao, ativo, created_at) 
                VALUES (?, ?, ?, ?, 1, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['numero'],
            $data['tipo'],
            $data['preco'],
            $data['descricao'] ?? null
        ]);
        
        http_response_code(201);
        echo json_encode(['sucesso' => true, 'quarto_id' => $pdo->lastInsertId()]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao criar quarto']);
    }
}

// Atualizar quarto:
function atualizarQuarto() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do quarto obrigatório.']);
        return;
    }
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar dados obrigatórios:
    if (empty($data['numero']) || empty($data['tipo']) || empty($data['preco'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios: número, tipo, preço']);
        return;
    }
    
    try {
        // Atualizar todos os campos:
        $sql = "UPDATE quartos SET numero = ?, tipo = ?, preco = ?, descricao = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['numero'],
            $data['tipo'],
            $data['preco'],
            $data['descricao'] ?? null,
            $id
        ]);
        
        echo json_encode(['sucesso' => true]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar quarto']);
    }
}

// Deletar quarto:
function deletarQuarto() {
    global $pdo;
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do quarto obrigatório.']);
        return;
    }
    
    try {
        // Desativa quarto ao invés de deletar:
        $sql = "UPDATE quartos SET ativo = 0 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data['id']]);
        
        echo json_encode(['sucesso' => true, 'mensagem' => 'Quarto desativado com sucesso.']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao deletar quarto.']);
    }
}
?>