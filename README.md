# ControlePonto-Api
Projeto Prático Uninove - Criação de um sistema de ponto

#### Aplicação em geral: 
O projeto consiste em uma aplicação de controle de ponto, onde o usuário, através do site ou aplicativo, poderá bater registrar e consultar seu horário . Também será possivel editar e excluir através da plataforma web.

#### Definições

#####Site
 Para o desenvolvimento do site, foi definido as seguintes tecnologias a serem trabalhadas:
 - HTML
 - CSS 
 - JS
 - Bootstrap
 - JQuery
 
 
 >Não foi identificada a necessidade de se trabalhar com um framework SPA para essa aplicação

###### Apresentação
As telas que irão compor o site são:

- Cadastro (Nome, e-mail, senha)
- Login (E-mail e senha)
- Dashboard (Tabela com os horários, com opção de adicionar, editar, alterar ou excluir horário)
- Validação de alteração (Aparecer modal de confirmação)

>Todas essas funcionalidades serão consumidas através da API


##### Mobile

A solução mobile será desenvolvida em Flutter e contará com as seguintes funcionalidades:

- Login(email e senha); 
- Tela de marcação automática (Um único botão que será acionado e que enviará para a API o horário exato da marcação);
- Tela que mostra as ultimas marcações;

>Aplicativo não poderá editar nem deletar informações 

##### Infraestrutura:
###### Api 
- Desenvolvida em php, com endpoints de crud (cadastro do ponto, login);

###### Banco de dados: 
- Desenvolvimento das tabelas em MySQL; 
- Acessado através da API;

>O campo de senha será criptofado por padrão


###### Deploy:

  >Site: Github pages 
  Api: hospedagem gratuita no WebhostApp 
  Mobile: Será gerado um apk apenas para a apresentação do projeto;



![](https://img.shields.io/github/stars/tangsan06/ControlePonto-Api.svg) ![](https://img.shields.io/github/forks/tangsan06/ControlePonto-Api.svg) ![](https://img.shields.io/github/issues/tangsan06/ControlePonto-Api.svg)
