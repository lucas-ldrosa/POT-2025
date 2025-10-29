<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Hotel - Reserva de Quartos</title>
</head>

<body>
    <div class="page-wraper">
        <header>
            <img src="img/logo.png" alt="Logo Sharan" class="logo">
            <nav>
                <ul class="nav_links">
                    <li><a href="#HOME">HOME</a></li>
                    <li><a href="#POST DETAIL">POST DETAIL</a></li>
                    <li><a href="#PAGES">PAGES</a></li>
                    <li><a href="#PROJECTS">PROJECTS</a></li>
                    <li><a href="#SHORTCODES">SHORTCODES</a></li>
                </ul>
            </nav>
            <nav>
                <ul style="list-style: none; display: flex; gap: 10px; margin: 0; padding: 0;">
                    <li><a href="admin_reservas.php" style="text-decoration:none; padding: 7px 15px; background:#252525; color:#fff; border-radius:3px;">Admin Reservas</a></li>
                    <li><a href="admin_quartos.php" style="text-decoration:none; padding: 7px 15px; background:#252525; color:#fff; border-radius:3px;">Admin Quartos</a></li>
                </ul>
            </nav>

        </header>

        <main class="page-content">
            <section id="banner"></section>
            <section id="reserva-container">
                <div id="reserva">
                    <div class="reserva-grid">
                      <div class="reserva-title">
                        <h2>Reserva</h2>
                      </div>
                        <div class="reserva-field entrada-saida-group">
                            <label for="data-entrada">Entrada</label>
                            <input type="date" id="data-entrada" class="reserva-input" placeholder="&#x2794 Entrada" required>
                        </div>

                        <div class="reserva-field entrada-saida-group">
                            <label for="data-saida">Saída</label>
                            <input type="date" id="data-saida" class="reserva-input" placeholder="&#x2794 Saída" required>
                        </div>

                        <div class="reserva-field">
                            <label for="quarto-select">Quarto</label>
                            <select id="quarto-select" class="reserva-input reserva-select" required>
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="reserva-field">
                            <label for="quantidade-adultos">Adultos</label>
                            <input type="number" id="quantidade-adultos" class="reserva-input" min="1" value="1" required>
                        </div>

                        <div class="reserva-field">
                            <label for="quantidade-criancas">Crianças</label>
                            <input type="number" id="quantidade-criancas" class="reserva-input" min="0" value="0">
                        </div>

                        <div class="reserva-field button-field">
                            <button id="btn-reservar" class="btn-reservar">Enviar</button>
                        </div>
                    </div>
                </div>
            </section>
            <div class="section-about">
                <div class="about-info">
                    <h2>Sobre
                        <div class="h2-shadow">SOBRE</div>
                    </h2>
                    <hr>
                    <h3>We will be so proud to be our guest. Lorem Ipsum is simply
                        dummy text of the printing.</h3>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
                        has been the typesetting industry's standard dummy text ever since the when. Lorem
                        Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <div class="info-grid">
                        <div class="grid-item">
                            <img src="img/grid-img/restaurante.png" alt="Restaurante" class="grid-img">
                            <h4>Restaurante</h4>
                            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
                        </div>
                        <div class="grid-item">
                            <img src="img/grid-img/wellness-spa.png" alt="Wellness & Spa" class="grid-img">
                            <h4>Wellness & Spa</h4>
                            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
                        </div>
                        <div class="grid-item">
                            <img src="img/grid-img/free-wifi.png" alt="Free Wifi" class="grid-img">
                            <h4>Free Wifi</h4>
                            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
                        </div>
                        <div class="grid-item">
                            <img src="img/grid-img/espaço-jogos.png" alt="Espaço de jogos" class="grid-img">
                            <h4>Espaço de jogos</h4>
                            <p>Lorem ipsum dolor sit piscing sed nonmy</p>
                        </div>
                    </div>
                    <button>SAIBA MAIS<span class="line"></span></button>
                </div>
                <div class="about-img">
                    <img src="img/pic1.png" alt="Foto do quarto.">
                </div>
            </div>

            <div class="section-acomodacoes">
                <h2>Acomodações<span class="h2-shadow">ACOMODAÇÕES</span></h2>
                <hr>
                <nav>
                    <ul class="nav_links">
                        <li><a href="#TODOS">TODOS</a></li>
                        <p>/</p>
                        <li><a href="#CASAL">CASAL</a></li>
                        <p>/</p>
                        <li><a href="#SOLTEIRO">SOLTEIRO</a></li>
                        <p>/</p>
                        <li><a href="#SUÍTE">SUÍTE</a></li>
                    </ul>
                </nav>
                <div class="acomodacoes-container">
                    <div class="acomodacoes-item">
                        <img src="img/aco-img/pic1.png" alt="Casal 01">
                        <h4>Casal 01</h4>
                        <div class="aco-info-flex">
                            <div class="aco-info">
                            <p class="span-price">R$ 299,00/NOITE</p>
                            <img src="img/icn/Icon.png" alt=""><span><strong> tamanho</strong> 30m² <img src="img/icn/person.png" alt=""> <strong> Adultos:</strong> 3</span>
                            </div>
                            <button>SAIBA MAIS</button>
                        </div>
                    </div>
                    <div class="acomodacoes-item">
                        <img src="img/aco-img/pic2.png" alt="Solteiro 01">
                        <h4>Solteiro 01</h4>
                        <div class="aco-info-flex">
                        <div class="aco-info">
                            <p class="span-price">R$ 199,00/NOITE</p>
                            <img src="img/icn/Icon.png" alt=""><span><strong> tamanho</strong> 30m² <img src="img/icn/person.png" alt=""> <strong> Adultos:</strong> 3</span>
                        </div>
                        <button>SAIBA MAIS</button>
                        </div>
                    </div>
                    <div class="acomodacoes-item">
                        <img src="img/aco-img/pic3.png" alt="Casal 02">
                        <h4>Casal 02</h4>
                        <div class="aco-info-flex">
                        <div class="aco-info">
                            <p class="span-price">R$ 299,00/NOITE</p>
                            <img src="img/icn/Icon.png" alt=""><span><strong> tamanho</strong> 30m² <img src="img/icn/person.png" alt=""> <strong> Adultos:</strong> 3</span>
                        </div>
                        <button>SAIBA MAIS</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container-footer">
                <div class="newsletter-flex">
                    <div class="newsletter">
                        <h4>NEWSLETTER</h4>
                        <p>Never Miss Anything From Construx By Signing Up To Our Newsletter.</p>
                    </div>
                    <div class="news-email">
                        <input type="email" placeholder="DIGITE SEU EMAIL" id="email-form" required>
                        <button id="subscribe-button">ENVIAR<span class="line"></span></button>
                    </div>
                </div>
                <hr>
                <div class="last-container">
                    <div class="end-box">
                        <img src="img/logo-light.png" alt="SHARAN" class="logo-footer">
                        <p>Today we can tell you, thanks to your
                            passion, hard work creativity, and
                            expertise, you delivered us the most
                            beautiful house great looks.
                        </p>
                        <ul class="social-links">
                            <li><a href="#"><img src="img/socials/facebook.png" alt="Facebook"></a></li>
                            <li><a href="#"><img src="img/socials/internet.png" alt="Internet"></a></li>
                            <li><a href="#"><img src="img/socials/linkedin.png" alt="Linkedin"></a></li>
                            <li><a href="#"><img src="img/socials/google.png" alt="Google +"></a></li>
                            <li><a href="#"><img src="img/socials/instagram.png" alt="Instagram"></a></li>
                        </ul>
                    </div>
                    <div class="end-box">
                        <h4>LINKS</h4>
                        <ul class="links">
                            <li><a href="#">ABOUT</a></li>
                            <li><a href="#">GALERY</a></li>
                            <li><a href="#">BLOG</a></li>
                            <li><a href="#">PORTFOLIO</a></li>
                            <li><a href="#">CONTACT US</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="end-box">
                        <h4>ACOMODAÇÕES</h4>
                        <ul class="links">
                            <li><a href="#">CLASSIC</a></li>
                            <li><a href="#">SUPERIOR</a></li>
                            <li><a href="#">DELUX</a></li>
                            <li><a href="#">MASTER</a></li>
                            <li><a href="#">LUXURY</a></li>
                            <li><a href="#">BANQUET HALLS</a></li>
                        </ul>
                    </div>
                    <div class="end-box">
                        <h4>FALE CONOSCO</h4>
                        <div class="fale-conosco">
                            <div class="fale-item">
                                <img src="img/icn/map.png" alt="Mapa" class="icons">
                                <p>92 Princess Road, parkvenue, Greater
                                    London, NW18JR, United Kingdom
                                </p>
                            </div>
                            <div class="fale-item">
                                <img src="img/icn/mail.png" alt="E-mail" class="icons">
                                <p>sharandemo@gmail.com</p>
                            </div>
                            <div class="fale-item">
                                <img src="img/icn/phone.png" alt="Telefone" class="icons">
                                <p>(+0091) 912-3456-073</p>
                            </div>
                            <div class="fale-item">
                                <img src="img/icn/fax.png" alt="Fax" class="icons">
                                <p>(+0091) 912-3456-084</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="foot-end">
                <p>© 2024 Your Company. Designed By Teste.</p>
            </div>
        </footer>
    </div>

    <dialog class="modal" id="modal-empty">
        <h1>Campo Vazio</h1>
        <p>Por gentileza, preencha o campo de email.</p>
        <div class="modal-buttom">
            <button class="modal-close">FECHAR</button>
        </div>
    </dialog>

    <dialog class="modal" id="modal-error">
        <h1>E-mail Inválido</h1>
        <p>Por gentileza, utilize um email válido.</p>
        <div class="modal-buttom">
            <button class="modal-close">FECHAR</button>
        </div>
    </dialog>

    <dialog class="modal" id="modal-success">
        <h1>Sucesso!</h1>
        <p>Inscrição realizada com sucesso.</p>
        <div class="modal-buttom">
            <button class="modal-close">FECHAR</button>
        </div>
    </dialog>

    <script src="js.js"></script>
</body>

</html>