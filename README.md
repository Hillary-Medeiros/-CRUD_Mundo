# CRUD Mundo

## Informações do Projeto
- **Aluna:** Hillary Medeiros
- **Curso:** Desenvolvimento de Sistemas
- **Disciplina:** Programação Web

---

## Descrição
O projeto **CRUD Mundo** é uma aplicação web para gerenciar informações sobre **países e cidades**. Foi desenvolvido com **HTML, CSS, JavaScript, PHP e MySQL** e permite **criar, ler, atualizar e excluir** registros de países e suas cidades, mantendo a integridade referencial.

O sistema também consome as seguintes **APIs externas** para enriquecer os dados:
- **REST Countries** — informações sobre países (bandeira, moeda, capital).
- **OpenWeatherMap** — informações climáticas em tempo real das cidades cadastradas.

---

## Tecnologias
- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP
- Banco de dados: MySQL
- APIs: REST Countries, OpenWeatherMap
- Versionamento: Git / GitHub

---

## Instalação e Uso

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/seu-usuario/crud-mundo.git](https://github.com/seu-usuario/crud-mundo.git)

2. **Inicie o Servidor:**

Instale e inicie o servidor local (ex.: XAMPP ou WAMP) e ative Apache e MySQL.

3. **Importe o Banco de Dados:**
   
    Via phpMyAdmin: [Acesse http://localhost/phpmyadmin e importe o arquivo database/bd_mundo.sql].
Via Linha de Comando:
   ```bash
   mysql -u root -p < database/bd_mundo.sql

4. **Copie os Arquivos:**
   
Copie a pasta do projeto (ex.: crud-mundo) para o diretório público do seu servidor:
  
  XAMPP: C:\xampp\htdocs\

5. **Ajuste a Conexão:**
  
Ajuste as credenciais de conexão no arquivo de configuração (ex.: backend/config.php):  
  
$db_host = 'localhost';  
$db_user = 'root';  
$db_pass = ''; // Senha do seu MySQL, se houver  
$db_name = 'bd_mundo';  

6. **Acesse o Projeto:**
  
Abra no navegador: http://localhost/crud-mundo/  

---

## Estrutura do Projeto

  ├── frontend/  
  │   └── (Páginas HTML, arquivos CSS e JS do cliente.)  
  ├── backend/  
  │   └── (Scripts PHP: conexão, controllers, operações CRUD.)  
  ├── database/  
  │   └── bd_mundo.sql (Script SQL para criação das tabelas.)  
  └── README.md  
