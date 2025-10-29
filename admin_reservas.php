<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo - Reservas</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
<div class="admin-container">
    <h1 class="admin-title">Painel Administrativo - Reservas</h1>

    <section class="admin-section">
        <h2 class="admin-subtitle">Adicionar/Editar Reserva</h2>
        <form id="form-reserva" onsubmit="event.preventDefault(); salvarReserva();">
            <div class="form-group">
                <label>Quarto*:</label>
                <select id="quarto-select" required>
                    <option value="">Selecione um quarto:</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Data de Entrada*:</label>
                <input type="date" id="data-entrada" required>
            </div>
            
            <div class="form-group">
                <label>Data de Saída*:</label>
                <input type="date" id="data-saida" required>
            </div>
            
            <div class="form-group">
                <label>Nome Completo*:</label>
                <input type="text" id="nomecliente" required placeholder="Digite o nome completo">
            </div>
            
            <div class="form-group">
                <label>E-mail*:</label>
                <input type="email" id="email" required placeholder="email@exemplo.com">
            </div>
            
            <div class="form-group">
                <label>CPF:</label>
                <input type="text" id="cpf" placeholder="000.000.000-00">
            </div>
            
            <div class="form-group">
                <label>Telefone*:</label>
                <input type="tel" id="telefone" required placeholder="(00) 00000-0000">
            </div>
            
            <div class="form-group">
                <label>Status*:</label>
                <select id="status" required>
                    <option value="confirmada">Confirmada</option>
                    <option value="pendente">Pendente</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            
            <div class="btn-group">
                <button type="submit" id="btn-salvar">Salvar</button>
                <button type="button" class="btn-secondary" onclick="limparFormulario()">Cancelar</button>
            </div>
        </form>
    </section>

    <section class="admin-section">
        <h2 class="admin-subtitle">Lista de Reservas:</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Quarto</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-reservas">
                <tr>
                    <td colspan="10" class="loading">Carregando reservas...</td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<script>
let editId = null;

// Carregar reservas com segurança:
async function carregarReservas() {
    try {
        const resposta = await fetch('api_reserva.php');
        
        if (!resposta.ok) {
            throw new Error('Erro ao carregar reservas.');
        }
        
        const reservas = await resposta.json();
        const tbody = document.getElementById('tabela-reservas');
        
        tbody.innerHTML = '';
        
        if (reservas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; color: #999;">Nenhuma reserva encontrada!</td></tr>';
            return;
        }
        
        reservas.forEach(r => {
            const tr = document.createElement('tr');
            
            // ID:
            const tdId = document.createElement('td');
            tdId.textContent = r.id;
            tr.appendChild(tdId);
            
            // Quarto:
            const tdQuarto = document.createElement('td');
            tdQuarto.textContent = r.quarto_numero || r.quarto_id;
            tr.appendChild(tdQuarto);
            
            // Data Entrada:
            const tdEntrada = document.createElement('td');
            tdEntrada.textContent = formatarData(r.data_checkin);
            tr.appendChild(tdEntrada);
            
            // Data Saída:
            const tdSaida = document.createElement('td');
            tdSaida.textContent = formatarData(r.data_checkout);
            tr.appendChild(tdSaida);
            
            // Nome:
            const tdNome = document.createElement('td');
            tdNome.textContent = r.nome_cliente;
            tr.appendChild(tdNome);
            
            // E-mail:
            const tdEmail = document.createElement('td');
            tdEmail.textContent = r.email;
            tr.appendChild(tdEmail);
            
            // CPF:
            const tdCpf = document.createElement('td');
            tdCpf.textContent = r.cpf || '-';
            tr.appendChild(tdCpf);
            
            // Telefone:
            const tdTelefone = document.createElement('td');
            tdTelefone.textContent = r.telefone;
            tr.appendChild(tdTelefone);
            
            // Status:
            const tdStatus = document.createElement('td');
            tdStatus.textContent = r.status;
            tdStatus.className = 'status-' + r.status;
            tr.appendChild(tdStatus);
            
            // Ações:
            const tdAcoes = document.createElement('td');
            
            const btnEditar = document.createElement('button');
            btnEditar.textContent = 'Editar';
            btnEditar.onclick = () => editarReserva(r);
            
            const btnExcluir = document.createElement('button');
            btnExcluir.textContent = 'Excluir';
            btnExcluir.className = 'btn-danger';
            btnExcluir.onclick = () => excluirReserva(r.id);
            
            tdAcoes.appendChild(btnEditar);
            tdAcoes.appendChild(document.createTextNode(' '));
            tdAcoes.appendChild(btnExcluir);
            
            tr.appendChild(tdAcoes);
            tbody.appendChild(tr);
        });
        
    } catch (erro) {
        console.error('Erro ao carregar reservas:', erro);
        alert('Erro ao carregar reservas: ' + erro.message);
    }
}

// Formatar data:
function formatarData(data) {
    if (!data) return '-';
    const partes = data.split('-');
    if (partes.length === 3) {
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }
    return data;
}

// Carregar quartos:
async function carregarQuartos() {
    try {
        const resposta = await fetch('api_quartos.php');
        
        if (!resposta.ok) {
            throw new Error('Erro ao carregar quartos.');
        }
        
        const quartos = await resposta.json();
        const select = document.getElementById('quarto-select');
        
        select.innerHTML = '<option value="">Selecione um quarto:</option>';
        
        quartos.forEach(q => {
            const option = document.createElement('option');
            option.value = q.id;
            option.textContent = q.numero + ' - ' + q.tipo;
            select.appendChild(option);
        });
        
    } catch (erro) {
        console.error('Erro ao carregar quartos:', erro);
        alert('Erro ao carregar quartos: ' + erro.message);
    }
}

// Editar reserva:
function editarReserva(reserva) {
    editId = reserva.id;
    
    document.getElementById('quarto-select').value = reserva.quarto_id;
    document.getElementById('data-entrada').value = reserva.data_checkin;
    document.getElementById('data-saida').value = reserva.data_checkout;
    document.getElementById('nomecliente').value = reserva.nome_cliente;
    document.getElementById('email').value = reserva.email;
    document.getElementById('cpf').value = reserva.cpf || '';
    document.getElementById('telefone').value = reserva.telefone;
    document.getElementById('status').value = reserva.status;
    
    document.getElementById('btn-salvar').textContent = 'Atualizar';
    
    document.querySelector('.admin-section').scrollIntoView({ behavior: 'smooth' });
}

// Limpar formulário:
function limparFormulario() {
    editId = null;
    document.getElementById('form-reserva').reset();
    document.getElementById('btn-salvar').textContent = 'Salvar';
}

// Salvar reserva:
async function salvarReserva() {
    const dados = {
        quarto_id: document.getElementById('quarto-select').value,
        data_checkin: document.getElementById('data-entrada').value,
        data_checkout: document.getElementById('data-saida').value,
        nome_cliente: document.getElementById('nomecliente').value.trim(),
        email: document.getElementById('email').value.trim(),
        cpf: document.getElementById('cpf').value.trim(),
        telefone: document.getElementById('telefone').value.trim(),
        status: document.getElementById('status').value
    };

    if (!dados.quarto_id) {
        alert('Selecione um quarto:');
        return;
    }
    
    if (!dados.nome_cliente || !dados.email || !dados.telefone) {
        alert('Preencha todos os campos obrigatórios!');
        return;
    }
    
    if (!dados.data_checkin || !dados.data_checkout) {
        alert('Preencha as datas de entrada e saída.');
        return;
    }
    
    if (new Date(dados.data_checkin) >= new Date(dados.data_checkout)) {
        alert('A data de saída deve ser posterior à data de entrada.');
        return;
    }

    let method = 'POST';
    let url = 'api_reserva.php';

    if (editId) {
        method = 'PUT';
        dados.id = editId;
    }

    try {
        const resposta = await fetch(url, {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(dados)
        });

        const resultado = await resposta.json();

        if (resposta.ok) {
            alert(editId ? 'Reserva atualizada com sucesso!' : 'Reserva criada com sucesso!');
            limparFormulario();
            carregarReservas();
        } else {
            alert('Erro: ' + (resultado.erro || 'Erro ao salvar reserva.'));
        }
    } catch (erro) {
        console.error('Erro:', erro);
        alert('Erro de conexão ao salvar reserva.');
    }
}

// Excluir reserva:
async function excluirReserva(id) {
    if (!confirm('Confirma a exclusão desta reserva?\n\nEsta ação não pode ser desfeita.')) {
        return;
    }
    
    try {
        const resposta = await fetch('api_reserva.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });

        const resultado = await resposta.json();

        if (resposta.ok) {
            alert('Reserva excluída com sucesso!');
            carregarReservas();
        } else {
            alert('Erro: ' + (resultado.erro || 'Erro ao excluir reserva.'));
        }
    } catch (erro) {
        console.error('Erro:', erro);
        alert('Erro de conexão ao excluir reserva.');
    }
}

// Inicializar:
window.onload = function() {
    carregarReservas();
    carregarQuartos();
    
    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data-entrada').min = hoje;
    document.getElementById('data-saida').min = hoje;
};
</script>

</body>
</html>