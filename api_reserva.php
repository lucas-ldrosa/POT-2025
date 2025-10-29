<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

error_log("=== API RESERVA INICIADA ===");
error_log("Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once 'config.php';
    error_log("Config incluído com sucesso.");
} catch (Exception $e) {
    error_log("Erro ao incluir config: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de configuração.', 'detalhes' => $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            error_log("Chamando criarReserva()");
            criarReserva();
            break;
        case 'GET':
            error_log("Chamando obterReservas()");
            obterReservas();
            break;
        case 'PUT':
            error_log("Chamando atualizarReserva()");
            atualizarReserva();
            break;
        case 'DELETE':
            error_log("Chamando cancelarReserva()");
            cancelarReserva();
            break;
        default:
            error_log("Método não permitido: " . $method);
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido.']);
            exit();
    }
} catch (Exception $e) {
    error_log("ERRO GERAL: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro interno do servidor.',
        'detalhes' => $e->getMessage(),
        'linha' => $e->getLine(),
        'arquivo' => $e->getFile()
    ]);
}

// Criar nova reserva:
function criarReserva() {
    global $pdo;
    
    error_log("--- CRIAR RESERVA ---");
    
    $json = file_get_contents('php://input');
    error_log("JSON recebido: " . $json);
    
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Erro ao decodificar JSON: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(['erro' => 'JSON inválido.', 'detalhes' => json_last_error_msg()]);
        return;
    }
    
    error_log("Dados decodificados: " . print_r($data, true));
    
    // Validar campos obrigatórios:
    $camposObrigatorios = ['quarto_id', 'nome_cliente', 'email', 'telefone', 'data_checkin', 'data_checkout'];
    
    foreach ($camposObrigatorios as $campo) {
        if (empty($data[$campo])) {
            error_log("Campo obrigatório em branco: " . $campo);
            http_response_code(400);
            echo json_encode(['erro' => "Campo obrigatório: $campo"]);
            return;
        }
    }
    
    // Validar e-mail:
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        error_log("E-mail inválido: " . $data['email']);
        http_response_code(400);
        echo json_encode(['erro' => 'E-mail inválido']);
        return;
    }
    
    // Validar datas:
    if (strtotime($data['data_checkin']) >= strtotime($data['data_checkout'])) {
        error_log("Data de checkout deve ser após checkin.");
        http_response_code(400);
        echo json_encode(['erro' => 'Data de saída deve ser posterior à data de entrada.']);
        return;
    }
    
    // Verificar se quarto existe e está ativo:
    try {
        $stmt = $pdo->prepare("SELECT id, numero, tipo FROM quartos WHERE id = ? AND ativo = 1");
        $stmt->execute([$data['quarto_id']]);
        $quarto = $stmt->fetch();
        
        if (!$quarto) {
            error_log("Quarto não encontrado ou inativo.");
            http_response_code(404);
            echo json_encode(['erro' => 'O quarto não foi encontrado ou está inativo.']);
            return;
        }
        
        error_log("Quarto encontrado: " . print_r($quarto, true));
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar quarto: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao verificar quarto.', 'detalhes' => $e->getMessage()]);
        return;
    }
    
    // Verificar disponibilidade nas datas:
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as conflitos 
            FROM reservas 
            WHERE quarto_id = ? 
            AND status = 'confirmada'
            AND (
                (data_checkin <= ? AND data_checkout > ?) OR
                (data_checkin < ? AND data_checkout >= ?) OR
                (data_checkin >= ? AND data_checkout <= ?)
            )
        ");
        
        $stmt->execute([
            $data['quarto_id'], 
            $data['data_checkin'], $data['data_checkin'],
            $data['data_checkout'], $data['data_checkout'],
            $data['data_checkin'], $data['data_checkout']
        ]);
        
        $conflito = $stmt->fetch();
        
        if ($conflito['conflitos'] > 0) {
            error_log("Quarto já reservado neste período.");
            http_response_code(409);
            echo json_encode(['erro' => 'Quarto já está reservado neste período.']);
            return;
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar disponibilidade: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao verificar disponibilidade.', 'detalhes' => $e->getMessage()]);
        return;
    }
    
    // Inserir reserva:
    try {
        $sql = "INSERT INTO reservas 
                (quarto_id, nome_cliente, email, telefone, cpf, data_checkin, data_checkout, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmada', NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            $data['quarto_id'],
            $data['nome_cliente'],
            $data['email'],
            $data['telefone'],
            $data['cpf'] ?? null,
            $data['data_checkin'],
            $data['data_checkout']
        ];
        
        $stmt->execute($parametros);
        
        $reservaId = $pdo->lastInsertId();
        
        error_log("A Reserva foi criada com sucesso! ID: " . $reservaId);
        
        http_response_code(201);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'A reserva foi criada com sucesso!',
            'reserva_id' => $reservaId,
            'quarto' => $quarto
        ]);
        
    } catch (PDOException $e) {
        error_log("Houve um erro ao inserir a reserva: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'erro' => 'Houve um erro ao processar a reserva.',
            'detalhes' => $e->getMessage()
        ]);
    }
}

// Obter reservas:
function obterReservas() {
    global $pdo;
    
    error_log("--- OBTER RESERVAS ---");
    
    try {
        $sql = "SELECT 
                    r.id, 
                    r.quarto_id,
                    r.nome_cliente, 
                    r.email, 
                    r.telefone, 
                    r.cpf,
                    r.data_checkin, 
                    r.data_checkout, 
                    r.status, 
                    r.created_at,
                    q.numero as quarto_numero, 
                    q.tipo as quarto_tipo, 
                    q.preco as quarto_preco
                FROM reservas r
                INNER JOIN quartos q ON r.quarto_id = q.id
                ORDER BY r.data_checkin DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $reservas = $stmt->fetchAll();
        
        error_log("Reservas encontradas: " . count($reservas) . " reservas encontradas.");
        
        http_response_code(200);
        echo json_encode($reservas);
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar reservas: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao buscar reservas.', 'detalhes' => $e->getMessage()]);
    }
}

// Autalizar reservas:
function atualizarReserva() {
    global $pdo;
    
    error_log("--- ATUALIZAR RESERVA ---");
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (empty($data['id'])) {
        error_log("ID da reserva não fornecido.");
        http_response_code(400);
        echo json_encode(['erro' => 'O ID da reserva é obrigatório.']);
        return;
    }
    
    // Verificar se a reserva existe:
    try {
        $stmt = $pdo->prepare("SELECT id, quarto_id, data_checkin, data_checkout FROM reservas WHERE id = ?");
        $stmt->execute([$data['id']]);
        $reservaAtual = $stmt->fetch();
        
        if (!$reservaAtual) {
            error_log("⚠️ Reserva não encontrada");
            http_response_code(404);
            echo json_encode(['erro' => 'Reserva não encontrada.']);
            return;
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar reserva: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao buscar reserva.', 'detalhes' => $e->getMessage()]);
        return;
    }
    
    // Validar datas se fornecidas:
    $dataCheckin = $data['data_checkin'] ?? $reservaAtual['data_checkin'];
    $dataCheckout = $data['data_checkout'] ?? $reservaAtual['data_checkout'];
    
    if (strtotime($dataCheckin) >= strtotime($dataCheckout)) {
        error_log("Data de checkout deve ser após checkin.");
        http_response_code(400);
        echo json_encode(['erro' => 'Data de saída deve ser posterior à data de entrada.']);
        return;
    }
    
    // Se o quarto_id foi alterado, verificar disponibilidade:
    if (isset($data['quarto_id']) && $data['quarto_id'] != $reservaAtual['quarto_id']) {
        try {
            // Verificar se o novo quarto existe:
            $stmt = $pdo->prepare("SELECT id FROM quartos WHERE id = ? AND ativo = 1");
            $stmt->execute([$data['quarto_id']]);
            
            if (!$stmt->fetch()) {
                error_log("Novo quarto não encontrado.");
                http_response_code(404);
                echo json_encode(['erro' => 'Quarto não encontrado.']);
                return;
            }
            
            // Verificar disponibilidade do novo quarto:
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as conflitos 
                FROM reservas 
                WHERE quarto_id = ? 
                AND id != ?
                AND status = 'confirmada'
                AND (
                    (data_checkin <= ? AND data_checkout > ?) OR
                    (data_checkin < ? AND data_checkout >= ?) OR
                    (data_checkin >= ? AND data_checkout <= ?)
                )
            ");
            
            $stmt->execute([
                $data['quarto_id'],
                $data['id'],
                $dataCheckin, $dataCheckin,
                $dataCheckout, $dataCheckout,
                $dataCheckin, $dataCheckout
            ]);
            
            $conflito = $stmt->fetch();
            
            if ($conflito['conflitos'] > 0) {
                error_log("Novo quarto indisponível neste período.");
                http_response_code(409);
                echo json_encode(['erro' => 'Quarto indisponível neste período.']);
                return;
            }
            
        } catch (PDOException $e) {
            error_log("Erro ao verificar disponibilidade: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao verificar disponibilidade.', 'detalhes' => $e->getMessage()]);
            return;
        }
    }
    
    try {
        // Incluir quarto_id nos campos permitidos:
        $camposPermitidos = ['quarto_id', 'nome_cliente', 'email', 'telefone', 'cpf', 'data_checkin', 'data_checkout', 'status'];
        $setClause = [];
        $valores = [];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($data[$campo])) {
                $setClause[] = "$campo = ?";
                $valores[] = $data[$campo];
            }
        }
        
        if (empty($setClause)) {
            error_log("Nenhum campo para atualizar.");
            http_response_code(400);
            echo json_encode(['erro' => 'Nenhum campo para atualizar fornecido.']);
            return;
        }
        
        $valores[] = $data['id'];
        
        $sql = "UPDATE reservas SET " . implode(', ', $setClause) . " WHERE id = ?";
        
        error_log("SQL UPDATE: " . $sql);
        error_log("Valores: " . print_r($valores, true));
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        error_log("Reserva atualizada com sucesso.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva atualizada com sucesso.']);
        
    } catch (PDOException $e) {
        error_log("Houve um erro ao atualizar a reserva: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar reserva.', 'detalhes' => $e->getMessage()]);
    }
}

// Cancelar reservas:
function cancelarReserva() {
    global $pdo;
    
    error_log("--- CANCELAR/EXCLUIR RESERVA ---");
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (empty($data['id'])) {
        error_log("ID da reserva não fornecido.");
        http_response_code(400);
        echo json_encode(['erro' => 'O ID da reserva é obrigatório.']);
        return;
    }
    
    try {

        $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->execute([$data['id']]);
        
        if ($stmt->rowCount() > 0) {
            error_log("Reserva excluída.");
            http_response_code(200);
            echo json_encode(['sucesso' => true, 'mensagem' => 'A reserva foi excluída com sucesso.']);
        } else {
            error_log("Atenção! Reserva não encontrada.");
            http_response_code(404);
            echo json_encode(['erro' => 'Reserva não foi encontrada.']);
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao excluir reserva: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao excluir a reserva.', 'detalhes' => $e->getMessage()]);
    }
}
?>