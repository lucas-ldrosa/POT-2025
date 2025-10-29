const header = document.querySelector("header")
const scrollWatcher = document.createElement('div')

scrollWatcher.setAttribute("data-scroll-watcher", "")
header.before(scrollWatcher)

const navObserver = new IntersectionObserver(
  (entries) => {
    header.classList.toggle("sticking", !entries[0].isIntersecting);
  }
)

navObserver.observe(scrollWatcher)


const openModal = document.querySelector("#subscribe-button")
const modalEmpty = document.querySelector("#modal-empty")
const modalError = document.querySelector("#modal-error")
const modalSuccess = document.querySelector("#modal-success")

openModal.addEventListener("click", () => {
  const emailDigitado = document.querySelector("#email-form").value;

  if (emailDigitado === ""){
    modalEmpty.showModal();
  } else if (isValidEmail(emailDigitado) == false){
    modalError.showModal();
  } else {
    modalSuccess.showModal();
    document.querySelector('#email-form').value = '';
  }
})

document.querySelectorAll("button.modal-close").forEach(btn => {
  btn.addEventListener("click", () => {
    btn.closest("dialog").close();
  })
})

// RESERVA - Carregar e submeter formulário

// Obter elementos do formulário de reserva pelos IDs:
const inputEntrada = document.getElementById("data-entrada");
const inputSaida = document.getElementById("data-saida");
const inputQuarto = document.getElementById("quarto-select");
const inputAdulto = document.getElementById("quantidade-adultos");
const inputCrianca = document.getElementById("quantidade-criancas");
const btnEnviar = document.getElementById("btn-reservar");

// Carregar quartos disponíveis ao iniciar a página:
document.addEventListener('DOMContentLoaded', () => {
  carregarQuartos();
  definirDataMinima();
});

// Data mínima como hoje:
function definirDataMinima() {
  const hoje = new Date().toISOString().split('T')[0];
  inputEntrada.min = hoje;
  inputSaida.min = hoje;
}

// Carregar lista de quartos via API:
function carregarQuartos() {
  fetch('api_quartos.php')
    .then(response => {
      if (!response.ok) throw new Error('Erro ao carregar quartos.');
      return response.json();
    })
    .then(quartos => {
      // Preencher dropdown com opções de quartos:
      preencherQuartos(quartos);
    })
    .catch(erro => {
      console.error('Erro:', erro);
      mostrarModalErro('Erro ao carregar quartos disponíveis');
    });
}

// Preencher select de quartos com dados da API:
function preencherQuartos(quartos) {

  inputQuarto.innerHTML = '<option value="">Quarto</option>';
  
  quartos.forEach(quarto => {
    const option = document.createElement('option');
    option.value = quarto.id;
    option.textContent = `${quarto.numero} - ${quarto.tipo} (R$ ${parseFloat(quarto.preco).toFixed(2)})`;
    inputQuarto.appendChild(option);
  });
}

// Evento de envio do formulário de reserva:
btnEnviar.addEventListener("click", () => {
  enviarReserva();
});

// Função para validar e enviar dados de reserva:
function enviarReserva() {
  // Capturar valores do formulário:
  const quartId = inputQuarto.value;
  const entrada = inputEntrada.value;
  const saida = inputSaida.value;
  const adultos = inputAdulto.value;
  const criancas = inputCrianca.value;
  
  // Validar se campos estão preenchidos:
  if (!quartId || !entrada || !saida || !adultos) {
    mostrarModalErro('Por favor, preencha todos os campos obrigatórios.');
    return;
  }
  
  // Validar datas:
  if (new Date(entrada) >= new Date(saida)) {
    mostrarModalErro('Data de saída deve ser após data de entrada.');
    return;
  }
  
  // Validar quantidades:
  if (adultos <= 0) {
    mostrarModalErro('Deve haver pelo menos 1 adulto.');
    return;
  }
  
  // Abrir modal para coleta de dados pessoais:
  abrirModalDadosPessoais(quartId, entrada, saida, adultos, criancas);
}

// Modal para dados pessoais:
function abrirModalDadosPessoais(quartId, entrada, saida, adultos, criancas) {
  // Criar modal dinamicamente:
  const modal = document.createElement('dialog');
  modal.className = 'modal';
  modal.id = 'modal-dados-pessoais';
  modal.innerHTML = `
    <h1>Dados da Reserva</h1>
    <form id="form-dados">
      <div style="margin: 15px 0;">
        <label>Nome Completo *</label>
        <input type="text" id="nome-completo" required style="width: 100%; padding: 8px; margin-top: 5px;">
      </div>
      <div style="margin: 15px 0;">
        <label>E-mail *</label>
        <input type="email" id="email-reserva" required style="width: 100%; padding: 8px; margin-top: 5px;">
      </div>
      <div style="margin: 15px 0;">
        <label>CPF *</label>
        <input type="text" id="cpf-reserva" required style="width: 100%; padding: 8px; margin-top: 5px;">
      </div>
      <div style="margin: 15px 0;">
        <label>Telefone *</label>
        <input type="tel" id="telefone-reserva" required style="width: 100%; padding: 8px; margin-top: 5px;">
      </div>
      <div class="modal-buttom" style="margin-top: 30px;">
        <button type="submit" style="width: 100px; margin-right: 10px;">CONFIRMAR</button>
        <button type="button" class="modal-close" style="width: 100px;">CANCELAR</button>
      </div>
    </form>
  `;
  
  document.body.appendChild(modal);
  modal.showModal();
  

  const inputCpf = document.getElementById('cpf-reserva');
  inputCpf.addEventListener('blur', function() {
    if (!validarCPF(this.value) && this.value !== "") {

        this.style.borderColor = '#E74C3C'; 
        this.style.boxShadow = '0 0 8px rgba(231, 76, 60, 0.3)';
    } else {

        this.style.borderColor = '#E0E0E0';
        this.style.boxShadow = 'none';
    }
  });

  inputCpf.addEventListener('focus', function() {
    this.style.borderColor = '#C19B76'; 
    this.style.boxShadow = '0 0 8px rgba(193, 155, 118, 0.2)';
  });
  

  modal.querySelector('.modal-close').addEventListener('click', () => {
    modal.close();
    modal.remove();
  });
  
  // Submeter formulário:
  document.getElementById('form-dados').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const nomeCompleto = document.getElementById('nome-completo').value;
    const email = document.getElementById('email-reserva').value;
    const cpf = document.getElementById('cpf-reserva').value;
    const telefone = document.getElementById('telefone-reserva').value;
    
    // Validar dados antes de enviar:
    if (!nomeCompleto || !email || !cpf || !telefone) {
      mostrarModalErro('Todos os campos são obrigatórios.');
      return;
    }
    
    if (!isValidEmail(email)) {
      mostrarModalErro('Email inválido');
      return;
    }

      // Adicionar validação de CPF no ENVIO:

    if (!validarCPF(cpf)) {
        mostrarModalErro('CPF inválido. Por favor, verifique o número.');
        document.getElementById('cpf-reserva').focus(); 
        return;
    }
    
    // Enviar dados para backend via API:
    salvarReservaBackend(quartId, entrada, saida, nomeCompleto, email, cpf, telefone);
    
    modal.close();
    modal.remove();
  });
}

// Enviar reserva para o backend:
function salvarReservaBackend(quartId, entrada, saida, nome, email, cpf, telefone) {
  
    const dados = {
      quarto_id: quartId,
      nome_cliente: nome,
      email: email,
      telefone: telefone,
      cpf: cpf,
      data_checkin: entrada,
      data_checkout: saida
    };
  
  // Requisição POST para API de reservas:
  fetch('api_reserva.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(dados)
  })
  .then(response => {
    return response.json().then(data => ({ ok: response.ok, data }));
  })
  .then(result => {
    if (result.ok) {
      if (result.data.sucesso) {
        mostrarModalSucesso('Reserva confirmada com sucesso!');
        // Limpar formulário principal:
        inputEntrada.value = '';
        inputSaida.value = '';
        inputQuarto.value = ''; 
        inputAdulto.value = '1'; 
        inputCrianca.value = '0'; 
      } else {

        mostrarModalErro(result.data.erro || 'Erro desconhecido.');
      }
    } else {
      // Caso de Erro:
      mostrarModalErro(result.data.erro || 'Erro ao processar reserva. Tente novamente.');
    }
  })
  .catch(erro => {
    console.error('Erro de fetch:', erro);
    mostrarModalErro('Não foi possível conectar ao servidor. Verifique sua conexão.');
  });
}

// Validar formato de e-mail:
function isValidEmail(email) {
  const EMAIL_REGEX = /^[a-z0-9.!#$%&'*+/=?^_`{|}~-]+@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i;
  return EMAIL_REGEX.test(email);
}

// Validar CPF:
function validarCPF(cpf) {
  cpf = cpf.replace(/\D/g, '');
  
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
    return false;
  }
  
  let soma = 0;
  let resto;
  
  for (let i = 1; i <= 9; i++) {
    soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
  }
  
  resto = (soma * 10) % 11;
  if (resto === 10 || resto === 11) resto = 0;
  if (resto !== parseInt(cpf.substring(9, 10))) return false;
  
  soma = 0;
  for (let i = 1; i <= 10; i++) {
    soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
  }
  
  resto = (soma * 10) % 11;
  if (resto === 10 || resto === 11) resto = 0;
  if (resto !== parseInt(cpf.substring(10, 11))) return false;
  
  return true;
}

// Mostrar modal de erro:
function mostrarModalErro(mensagem) {
  const modal = document.createElement('dialog');
  modal.className = 'modal';
  modal.innerHTML = `
    <h1>Erro</h1>
    <p>${mensagem}</p>
    <div class="modal-buttom">
      <button class="modal-close">FECHAR</button>
    </div>
  `;
  document.body.appendChild(modal);
  modal.showModal();
  
  modal.querySelector('.modal-close').addEventListener('click', () => {
    modal.close();
    modal.remove();
  });
}

// Mostrar modal de sucesso:
function mostrarModalSucesso(mensagem) {
  const modal = document.createElement('dialog');
  modal.className = 'modal';
  modal.innerHTML = `
    <h1>Sucesso!</h1>
    <p>${mensagem}</p>
    <div class="modal-buttom">
      <button class="modal-close">FECHAR</button>
    </div>
  `;
  document.body.appendChild(modal);
  modal.showModal();
  
  modal.querySelector('.modal-close').addEventListener('click', () => {
    modal.close();
    modal.remove();
  });
}