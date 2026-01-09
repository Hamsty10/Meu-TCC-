<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>ServiGera - Conectando Você ao Serviço Certo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }

        html,
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #fff;
            color: #222;
        }

        header {
            background-color: #900000;
            color: #fff;
            padding: 25px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        header img {
            height: 80px;
            border-radius: 12px;
        }

        section {
            padding: 80px 20px;
            text-align: center;
        }

        section:nth-child(even) {
            background-color: #f9f9f9;
        }

        h2 {
            color: #e60000;
            font-size: 2.4rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.15rem;
            line-height: 1.7;
            margin: 0 auto 30px;
            max-width: 850px;
            color: #333;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url("fundo.png") center/cover fixed;
            color: #fff;
            padding: 120px 20px;
            text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.5);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            color: #eee;
            max-width: 700px;
            margin: 0 auto 40px;
            font-size: 1.2rem;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 16px 35px;
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff;
            background-color: #e60000;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: #ff3333;
            transform: scale(1.05);
        }

        .features {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
            margin-top: 50px;
        }

        .feature-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .feature-card img {
            height: 80px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #e60000;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 1rem;
        }

        .sobre {
            max-width: 900px;
            margin: 0 auto;
        }

        .galeria {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin-top: 30px;
        }

       .galeria img {
    width: 200px;
    height: 140px;
    object-fit: contain; 
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}


        .galeria img:hover {
            transform: scale(1.05);
        }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }

        @media (max-width: 700px) {
            .feature-card {
                width: 90%;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="servigeralateral.png" alt="ServiGera">
    </header>
    <section class="hero">
        <h1>Bem-vindo ao ServiGera</h1>
        <p>Conectamos clientes e técnicos de forma simples, rápida e eficiente.  
            Um sistema feito para facilitar o dia a dia de quem precisa contratar ou oferecer serviços gerais.</p>
        <a href="#sobre" class="btn">Saiba Mais</a>
    </section>
    <section id="sobre">
        <h2>O que é o ServiGera?</h2>
        <p class="sobre">
            O <strong>ServiGera</strong> é uma plataforma digital que intermedia a comunicação entre <strong>clientes</strong> que precisam de serviços gerais e <strong>técnicos</strong> capacitados para realizá-los.
            O sistema foi criado para simplificar todo o processo: do pedido à execução do serviço.
        </p>
        <div class="features">
            <div class="feature-card">
                <img src="imagens/cliente.png" alt="Cliente">
                <h3>Para Clientes</h3>
                <p>Solicite serviços com poucos cliques, receba propostas e escolha o técnico ideal conforme sua cidade e avaliação.</p>
            </div>
            <div class="feature-card">
                <img src="imagens/tecnico.png" alt="Técnico">
                <h3>Para Técnicos</h3>
                <p>Envie propostas, aceite pedidos e amplie sua clientela local com base na sua cidade e reputação.</p>
            </div>
        </div>
    </section>
    <section>
        <h2>Como Funciona?</h2>
        <p>
            O processo é simples: o cliente cria um pedido → técnicos da mesma cidade visualizam → enviam propostas → o cliente escolhe o técnico → serviço realizado com segurança.
        </p>

        <div class="galeria">
            <img src="imagens/pedido.png" alt="Pedido de Serviço">
            <img src="imagens/proposta.png" alt="Envio de Propostas">
            <img src="imagens/avaliacao.png" alt="Avaliações">
        </div>
    </section>
    <section>
    <h2>Por que usar o ServiGera?</h2>
        <p>
            Porque valorizamos praticidade, confiança e eficiência.  
            Nosso sistema foi desenvolvido com segurança em PHP, MySQL e JavaScript, garantindo confiabilidade e performance.
        </p>
    </section>
<section>
        <h2>Comece Agora</h2>
    <p>Pronto para aproveitar tudo o que o ServiGera oferece?  
            Clique abaixo para iniciar sua sessão e acessar o sistema completo.</p>
<a href="tela_inicial.php" class="btn">Iniciar Sessão</a>
    </section>
<footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>
</html>