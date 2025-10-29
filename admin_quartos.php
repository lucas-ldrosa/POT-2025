<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo - Quartos</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
<div class="admin-container">
    <h1 class="admin-title">Painel Administrativo - Quartos</h1>

    <section class="admin-section">
        <h2 class="admin-subtitle">Adicionar/Editar Quarto</h2>
        <form id="form-quarto" onsubmit="event.preventDefault(); salvarQuarto();">
            <div class="form-group">
                <label>Número*:</label>
                <input type="text" id="numero" required placeholder="Ex: 101, 102">
            </div>
            
            <div class="form-group">
                <label>Tipo*:</label>
                <input type="text" id="tipo" required placeholder="Ex: Standard, Luxo, Suite">
            </div>
            
            <div class="form-group">
                <label>Preço (R$)*:</label>
                <input type="number" id="preco" step="0.01" min="0" required placeholder="Ex: 250.00">
            </div>
            
            <div class="form-group">
                <label>Descrição:</label>
                <input type="text" id="descricao" placeholder="Breve descrição do quarto">
            </div>
            
            <div class="btn-group">
                <button type="submit" id="btn-salvar">Salvar</button>
                <button type="button" class="btn-secondary" onclick="limparFormulario()">Cancelar</button>
            </div>
        </form>
    </section>

    <section class="admin-section">
        <h2 class="admin-subtitle">Lista de Quartos:</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-quartos">
                <tr>
                    <td colspan="6" class="loading">Carregando quartos...</td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<script>
let editId = null;

// Carregar quartos com segurança:
async function carregarQuartos() {
    try {
        const resposta = await fetch('api_quartos.php');
        
        if (!resposta.ok) {
            throw new Error('Erro ao carregar quartos');
        }
        
        const quartos = await resposta.json();
        const tbody = document.getElementById('tabela-quartos');
        
        tbody.innerHTML = '';
        
        if (quartos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #999;">Nenhum quarto cadastrado</td></tr>';
            return;
        }
        
        quartos.forEach(q => {
            const tr = document.createElement('tr');
            
            // ID:
            const tdId = document.createElement('td');
            tdId.textContent = q.id;
            tr.appendChild(tdId);
            
            // Número:
            const tdNumero = document.createElement('td');
            tdNumero.textContent = q.numero;
            tr.appendChild(tdNumero);
            
            // Tipo:
            const tdTipo = document.createElement('td');
            tdTipo.textContent = q.tipo;
            tr.appendChild(tdTipo);
            
            // Preço:
            const tdPreco = document.createElement('td');
            tdPreco.textContent = 'R$ ' + parseFloat(q.preco).toFixed(2);
            tr.appendChild(tdPreco);
            
            // Descrição:
            const tdDesc = document.createElement('td');
            tdDesc.textContent = q.descricao || '-';
            tr.appendChild(tdDesc);
            
            // Ações:
            const tdAcoes = document.createElement('td');
            
            const btnEditar = document.createElement('button');
            btnEditar.textContent = 'Editar';
            btnEditar.onclick = () => editarQuarto(q);
            
            const btnExcluir = document.createElement('button');
            btnExcluir.textContent = 'Excluir';
            btnExcluir.className = 'btn-danger';
            btnExcluir.onclick = () => excluirQuarto(q.id);
            
            tdAcoes.appendChild(btnEditar);
            tdAcoes.appendChild(document.createTextNode(' '));
            tdAcoes.appendChild(btnExcluir);
            
            tr.appendChild(tdAcoes);
            tbody.appendChild(tr);
        });
        
    } catch (erro) {
        console.error('Erro ao carregar quartos:', erro);
        alert('Erro ao carregar quartos: ' + erro.message);
    }
}

// Editar quarto:
function editarQuarto(quarto) {
    editId = quarto.id;
    
    document.getElementById('numero').value = quarto.numero;
    document.getElementById('tipo').value = quarto.tipo;
    document.getElementById('preco').value = quarto.preco;
    document.getElementById('descricao').value = quarto.descricao || '';
    
    document.getElementById('btn-salvar').textContent = 'Atualizar';
    
    document.querySelector('.admin-section').scrollIntoView({ behavior: 'smooth' });
}

// Limpar formulário:
function limparFormulario() {
    editId = null;
    document.getElementById('form-quarto').reset();
    document.getElementById('btn-salvar').textContent = 'Salvar';
}

// Salvar quarto:
async function salvarQuarto() {
    const dados = {
        numero: document.getElementById('numero').value.trim(),
        tipo: document.getElementById('tipo').value.trim(),
        preco: document.getElementById('preco').value,
        descricao: document.getElementById('descricao').value.trim()
    };

    // Validações:
    if (!dados.numero || !dados.tipo || !dados.preco) {
        alert('Preencha todos os campos obrigatórios.');
        return;
    }

    if (parseFloat(dados.preco) <= 0) {
        alert('O preço deve ser maior que zero.');
        return;
    }

    let method = 'POST';
    let url = 'api_quartos.php';

    if (editId) {
        method = 'PUT';
        url = 'api_quartos.php?id=' + editId;
    }

    try {
        const resposta = await fetch(url, {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(dados)
        });

        const resultado = await resposta.json();

        if (resposta.ok) {
            alert(editId ? 'Quarto atualizado com sucesso!' : 'Quarto criado com sucesso!');
            limparFormulario();
            carregarQuartos();
        } else {
            alert('Erro: ' + (resultado.erro || 'Erro ao salvar quarto.'));
        }
    } catch (erro) {
        console.error('Erro:', erro);
        alert('Erro de conexão ao salvar quarto.');
    }
}

// Excluir quarto:
async function excluirQuarto(id) {
    if (!confirm('Confirma a exclusão deste quarto?\n\nAtenção: Não será possível excluir se houver reservas associadas.')) {
        return;
    }

    try {
        const resposta = await fetch('api_quartos.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });

        const resultado = await resposta.json();

        if (resposta.ok) {
            alert('Quarto excluído com sucesso!');
            carregarQuartos();
        } else {
            alert('Erro: ' + (resultado.erro || 'Erro ao excluir quarto.'));
        }
    } catch (erro) {
        console.error('Erro:', erro);
        alert('Erro de conexão ao excluir quarto.');
    }
}

// Inicializar:
window.onload = function() {
    carregarQuartos();
};
</script>

</body>
</html>