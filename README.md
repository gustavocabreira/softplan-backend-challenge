# Softplan Backend Challenge

### Candidato: Gustavo de Sousa Cabreira
### Vaga: Pessoa Desenvolvedora PHP (Sênior) - Cód. 9326

## Sobre o projeto

API desenvolvida em Laravel para enviar notificações por e-mail sobre a disponibilidade de novos bolos, mantendo os usuários informados em tempo real.

Quando um usuário cadastrar um bolo com uma lista de e-mails, o sistema processa essa lista em segundo plano utilizando as filas do Laravel. Primeiramente, os e-mails são armazenados no banco de dados, e em seguida, é enviado um e-mail ao usuário notificando que o bolo está disponível para ser adquirido.

Caso o estoque do bolo seja esgotado e um novo estoque seja adicionado, o sistema envia outro e-mail para o usuário informando que o bolo voltou a estar disponível.

Todo o processamento de e-mails e notificações é feito de forma assíncrona, aproveitando as filas do Laravel para garantir eficiência e desempenho na execução dessas tarefas.

## Tecnologias Utilizadas

- Docker e Docker Compose
- PHP 8.4
- Laravel Framework
- Swoole (Laravel Octane)
- MySQL
- [Nginx](https://www.nginx.com/) (Servidor de aplicações)
- [Redis](https://redis.io/) (Cache e Filas)
- [Pest](https://pestphp.com/) (Testes)
- [Supervisor](https://github.com/ochinchina/supervisord) (Gerenciador de processos)
- [Mailhog](https://github.com/mailhog/MailHog) (SMTP de teste) e [Jim](https://github.com/mailhog/MailHog/blob/master/docs/JIM.md) (Simulação de erros)
- [Meilisearch](https://www.meilisearch.com/) (Full-text Search)
- [Scramble](https://scramble.dedoc.co/) (Documentação)

## Instalação

### Requisitos

- [Docker](https://docs.docker.com/engine/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Deverá ter as portas 80, 1025, 3306, 6379, 7700, 8025 e 9051 abertas e desocupadas.

### Executando o projeto

1. Clone o repositório

```bash
git clone https://github.com/gustavocabreira/softplan-backend-challenge.git
```

2. Entre na pasta do projeto

```bash
cd softplan-backend-challenge
```

3. Entre na pasta docker/local

```bash
cd docker/local
```

4. Execute o comando para instalar o projeto

```bash
sh install.sh --app-name=softplan-backend-challenge
```

5. Após a instalação, utilize o comando para iniciar o projeto

```bash
sh start.sh
```

6. Acesse a aplicação em http://localhost

7. Você pode acessar a documentação do projeto em http://localhost/docs/api

## Código de respostas HTTP

| Código | Descrição              | Explicação                                                                     | 
|--------|------------------------|--------------------------------------------------------------------------------|
| 200 | OK                        | A requisição performou com sucesso.                                            |
| 201 | Created                   | O recurso foi criado com sucesso.                                              |
| 204 | No Content                | A requisição performou com sucesso, mas não retornou nenhum conteúdo.          |
| 404 | Not Found                 | O recurso não foi encontrado.                                                  |
| 422 | Unprocessable Entity      | O recurso não pode ser processado devido a um erro nas informações fornecidas. |
| 500 | Internal Server Error     | Ocorreu um erro no servidor.                                                   |

## Testes

Os testes do projeto estão no diretório `tests/` e foram desenvolvidos utilizando o pacote [Pest](https://pestphp.com/docs/installation).
Pest é uma biblioteca de testes para PHP que permite escrever testes de forma fácil e rápida.

Para executar os testes, siga os passos abaixo:

1. Acesse o diretório /docker/local
2. Execute o comando para interagir com o container

```bash
docker compose exec -it laravel sh
```

3. Execute o seguinte comando

```bash
php artisan test
```

## Mailhog e Jim

Mailhog é uma ferramenta de SMTP de teste que permite enviar e-mails para um servidor SMTP local.
Jim é uma ferramenta de simulação de erros que permite simular erros de conexão, erros de autenticação e outros erros comuns em aplicações que usam o protocolo SMTP.

Utilizando o JIM para simular o comportamento de um servidor SMTP, conseguimos criar cenários como:
- Enviar e-mails com sucesso
- Enviar e-mails com falha
- Enviar e-mails com erros de conexão
- Enviar e-mails com erros de autenticação

Para utilizá-lo, siga os passos abaixo:

1. Após a instalação do projeto, acesse http://localhost:8025

## Meilisearch

Meilisearch é uma plataforma de busca de texto completo que permite pesquisar em documentos de texto, imagens e outros tipos de dados.

Para acessá-lo, siga os passos abaixo:

1. Acesse http://localhost:7700
2. Informe a API Key `meilisearch1234`
